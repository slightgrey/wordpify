<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Options;
use Wordpify\Sync\CronScheduler;
use WP_REST_Request;
use WP_REST_Response;

class SettingsEndpoint {

	const ROUTE = '/settings';

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
				'args'                => [
					'interval'              => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'enum'              => [ 'off', 'hourly', 'twicedaily', 'daily' ],
					],
					'delete_on_uninstall' => [ 'required' => false, 'type' => 'boolean' ],
				],
			],
		] );
	}

	public static function get( WP_REST_Request $_request ): WP_REST_Response {
		return new WP_REST_Response( [
			'sync_settings'       => Options::get_sync_settings(),
			'delete_on_uninstall' => Options::delete_on_uninstall(),
		], 200 );
	}

	public static function save( WP_REST_Request $request ): WP_REST_Response {
		$interval = $request->get_param( 'interval' );
		if ( $interval !== null ) {
			$settings             = Options::get_sync_settings();
			$settings['interval'] = $interval;
			update_option( Options::SYNC_SETTINGS, $settings );
			CronScheduler::reschedule( $interval );
		}

		$delete = $request->get_param( 'delete_on_uninstall' );
		if ( $delete !== null ) {
			update_option( Options::DELETE_ON_UNINSTALL, (bool) $delete );
		}

		return new WP_REST_Response( [ 'saved' => true ], 200 );
	}
}
