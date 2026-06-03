<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Options;
use Wordpify\Sync\SyncManager;
use WP_REST_Request;
use WP_REST_Response;

class SyncEndpoint {

	public static function register_routes(): void {
		register_rest_route( 'wordpify/v1', '/sync/status', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'status' ],
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
		] );

		register_rest_route( 'wordpify/v1', '/sync/run', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'run' ],
			'permission_callback' => function( \WP_REST_Request $request ): bool|\WP_Error {
				if ( ! current_user_can( 'manage_options' ) ) {
					return false;
				}
				$nonce = $request->get_header( 'X-WP-Nonce' );
				if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
					return new \WP_Error( 'rest_forbidden', 'Nonce verification failed.', [ 'status' => 403 ] );
				}
				return true;
			},
		] );
	}

	public static function status( WP_REST_Request $_request ): WP_REST_Response {
		return new WP_REST_Response( Options::get_sync_settings(), 200 );
	}

	public static function run( WP_REST_Request $_request ): WP_REST_Response {
		SyncManager::run();
		return new WP_REST_Response( Options::get_sync_settings(), 200 );
	}
}
