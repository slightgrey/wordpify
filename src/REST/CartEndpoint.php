<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Cart\CartManager;
use WP_REST_Request;
use WP_REST_Response;

class CartEndpoint {

	public static function user_nonce_permission( WP_REST_Request $request ): bool|\WP_Error {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		$nonce = $request->get_header( 'X-WP-Nonce' );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
			return new \WP_Error( 'rest_forbidden', 'Nonce verification failed.', [ 'status' => 403 ] );
		}
		return true;
	}

	public static function register_routes(): void {
		register_rest_route( 'wordpify/v1', '/cart', [
			[
				'methods'             => 'GET',
				'callback'            => [ self::class, 'get_cart' ],
				'permission_callback' => 'is_user_logged_in',
			],
			[
				'methods'             => 'POST',
				'callback'            => [ self::class, 'get_cart' ],
				'permission_callback' => [ self::class, 'user_nonce_permission' ],
			],
		] );

		register_rest_route( 'wordpify/v1', '/cart/items', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'add_items' ],
			'permission_callback' => [ self::class, 'user_nonce_permission' ],
			'args'                => [
				'lines' => [
					'required' => true,
					'type'     => 'array',
					'items'    => [
						'type'       => 'object',
						'properties' => [
							'merchandiseId' => [
								'type'              => 'string',
								'validate_callback' => fn( $v ) => (bool) preg_match( '/^gid:\/\/shopify\/ProductVariant\/\d+$/', $v ),
							],
							'quantity'      => [
								'type'    => 'integer',
								'minimum' => 1,
								'maximum' => 999,
							],
						],
					],
				],
			],
		] );

		register_rest_route( 'wordpify/v1', '/cart/items/update', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'update_item' ],
			'permission_callback' => [ self::class, 'user_nonce_permission' ],
			'args'                => [
				'lineId'   => [ 'required' => true, 'type' => 'string' ],
				'quantity' => [ 'required' => true, 'type' => 'integer', 'minimum' => 1, 'maximum' => 999 ],
			],
		] );

		register_rest_route( 'wordpify/v1', '/cart/items/remove', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'remove_item' ],
			'permission_callback' => [ self::class, 'user_nonce_permission' ],
			'args'                => [
				'lineId' => [ 'required' => true, 'type' => 'string' ],
			],
		] );

		register_rest_route( 'wordpify/v1', '/cart/checkout', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'checkout' ],
			'permission_callback' => 'is_user_logged_in',
		] );
	}

	public static function get_cart( WP_REST_Request $_request ): WP_REST_Response {
		try {
			$manager = new CartManager();
			$cart    = $manager->get_or_create( get_current_user_id() );
			return new WP_REST_Response( $cart, 200 );
		} catch ( \Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ], 500 );
		}
	}

	public static function add_items( WP_REST_Request $request ): WP_REST_Response {
		try {
			$manager = new CartManager();
			$cart    = $manager->add_items( get_current_user_id(), $request->get_param( 'lines' ) );
			return new WP_REST_Response( $cart, 200 );
		} catch ( \Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ], 500 );
		}
	}

	public static function update_item( WP_REST_Request $request ): WP_REST_Response {
		try {
			$manager  = new CartManager();
			$line_id  = $request->get_param( 'lineId' );
			$quantity = (int) $request->get_param( 'quantity' );
			$cart     = $manager->update_items( get_current_user_id(), [
				[ 'id' => $line_id, 'quantity' => $quantity ],
			] );
			return new WP_REST_Response( $cart, 200 );
		} catch ( \Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ], 500 );
		}
	}

	public static function remove_item( WP_REST_Request $request ): WP_REST_Response {
		try {
			$manager = new CartManager();
			$line_id = $request->get_param( 'lineId' );
			$cart    = $manager->remove_items( get_current_user_id(), [ $line_id ] );
			return new WP_REST_Response( $cart, 200 );
		} catch ( \Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ], 500 );
		}
	}

	public static function checkout( WP_REST_Request $_request ): WP_REST_Response {
		try {
			$manager = new CartManager();
			$url     = $manager->get_checkout_url( get_current_user_id() );
			return new WP_REST_Response( [ 'checkout_url' => $url ], 200 );
		} catch ( \Throwable $e ) {
			return new WP_REST_Response( [ 'error' => $e->getMessage() ], 500 );
		}
	}
}
