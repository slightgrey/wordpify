<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Auth\CustomerAuthHandler;
use Wordpify\Auth\WPUserLinker;
use WP_REST_Request;
use WP_REST_Response;

class AuthEndpoint {

	public static function register_routes(): void {
		register_rest_route( 'wordpify/v1', '/auth/status', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'status' ],
			'permission_callback' => 'is_user_logged_in',
		] );
	}

	public static function status( WP_REST_Request $request ): WP_REST_Response {
		$user_id = get_current_user_id();
		$linked  = WPUserLinker::is_linked( $user_id );

		if ( ! $linked ) {
			CustomerAuthHandler::ensure_linked( $user_id );
			$linked = WPUserLinker::is_linked( $user_id );
		}

		return new WP_REST_Response( [
			'linked'      => $linked,
			'customer_id' => WPUserLinker::get_shopify_customer_id( $user_id ),
		], 200 );
	}
}
