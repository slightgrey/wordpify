<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Crypto\TokenEncryption;
use Wordpify\Options;
use Wordpify\Shopify\StorefrontClient;
use WP_REST_Request;
use WP_REST_Response;

class ConnectionEndpoint {

	const ROUTE = '/connection';

	public static function register_routes(): void {
		register_rest_route( 'wordpify/v1', self::ROUTE, [
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get' ],
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
			],
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'save' ],
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
				'args'                => [
					'store_domain' => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
					'access_token' => [ 'required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ],
				],
			],
			[
				'methods'             => 'DELETE',
				'callback'            => [ self::class, 'disconnect' ],
				'permission_callback' => fn() => current_user_can( 'manage_options' ),
			],
		] );
	}

	public static function get( WP_REST_Request $request ): WP_REST_Response {
		$connection = Options::get_connection();
		// Mask token.
		if ( ! empty( $connection['access_token'] ) ) {
			$connection['access_token'] = str_repeat( '*', 8 ) . substr( $connection['access_token'], -4 );
		}
		return new WP_REST_Response( $connection, 200 );
	}

	public static function save( WP_REST_Request $request ): WP_REST_Response {
		$store_domain = $request->get_param( 'store_domain' );
		$access_token = $request->get_param( 'access_token' );

		if ( str_starts_with( $access_token, 'shpat_' ) ) {
			return new WP_REST_Response( [
				'connected' => false,
				'shop_name' => '',
				'error'     => __( 'This looks like a Shopify Admin API token (shpat_…). Wordpify requires the Storefront API public access token — find it in Shopify admin under Apps → Develop apps → [your app] → Storefront API integration.', 'wordpify' ),
			], 200 );
		}

		$client = new StorefrontClient( $store_domain, $access_token );
		$result = $client->test_connection();

		if ( ! $result['connected'] && $result['error'] && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			error_log( 'Wordpify connection error: ' . $result['error'] );
		}

		$connection = [
			'store_domain'   => $store_domain,
			'access_token'   => TokenEncryption::encrypt( $access_token ),
			'connected'      => $result['connected'],
			'last_tested_at' => gmdate( 'c' ),
		];

		update_option( Options::CONNECTION, $connection );

		return new WP_REST_Response( [
			'connected' => $result['connected'],
			'shop_name' => $result['shop_name'],
			'error'     => $result['connected'] ? '' : ( $result['error'] ?: __( 'Could not connect to Shopify. Check your store domain and access token.', 'wordpify' ) ),
		], 200 );
	}

	public static function disconnect( WP_REST_Request $request ): WP_REST_Response {
		update_option( Options::CONNECTION, [
			'store_domain'   => '',
			'access_token'   => '',
			'connected'      => false,
			'last_tested_at' => null,
		] );
		return new WP_REST_Response( [ 'disconnected' => true ], 200 );
	}
}
