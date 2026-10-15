<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\Frontend\ProductLink;
use Wordpify\Options;
use WP_REST_Request;
use WP_REST_Response;

class ShopEndpoint {

	const ROUTE = '/shop';

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
				'permission_callback' => function( WP_REST_Request $request ): bool|\WP_Error {
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
					'gst_cart_enabled'        => [ 'required' => false, 'type' => 'boolean' ],
					'gst_product_enabled'     => [ 'required' => false, 'type' => 'boolean' ],
					'gst_text'                => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'checkout_notice_enabled' => [ 'required' => false, 'type' => 'boolean' ],
					'checkout_notice'         => [
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'product_page_id'         => [ 'required' => false, 'type' => 'integer' ],
				],
			],
		] );
	}

	public static function get( WP_REST_Request $_request ): WP_REST_Response {
		$pages = get_pages( [ 'post_status' => 'publish', 'sort_column' => 'post_title' ] );

		return new WP_REST_Response( Options::get_shop_settings() + [
			'available_pages' => array_map(
				fn( $page ) => [
					'id'              => $page->ID,
					'title'           => $page->post_title ?: __( '(no title)', 'wordpify' ),
					'has_single_view' => self::has_single_view( $page ),
				],
				$pages
			),
		], 200 );
	}

	/**
	 * Whether a page can display a single product: it holds [wordpify_product]
	 * or the Elementor Single Product widget.
	 */
	private static function has_single_view( \WP_Post $page ): bool {
		return ProductLink::page_contains( $page->ID, 'wordpify_product', 'wordpify-product-single' );
	}

	public static function save( WP_REST_Request $request ): WP_REST_Response {
		$current = Options::get_shop_settings();

		$gst_cart = $request->get_param( 'gst_cart_enabled' );
		if ( $gst_cart !== null ) {
			$current['gst_cart_enabled'] = (bool) $gst_cart;
		}

		$gst_product = $request->get_param( 'gst_product_enabled' );
		if ( $gst_product !== null ) {
			$current['gst_product_enabled'] = (bool) $gst_product;
		}

		$gst_text = $request->get_param( 'gst_text' );
		if ( $gst_text !== null ) {
			$current['gst_text'] = sanitize_text_field( $gst_text );
		}

		$checkout_notice_enabled = $request->get_param( 'checkout_notice_enabled' );
		if ( $checkout_notice_enabled !== null ) {
			$current['checkout_notice_enabled'] = (bool) $checkout_notice_enabled;
		}

		$checkout_notice = $request->get_param( 'checkout_notice' );
		if ( $checkout_notice !== null ) {
			$current['checkout_notice'] = sanitize_text_field( $checkout_notice );
		}

		$product_page_id = $request->get_param( 'product_page_id' );
		if ( $product_page_id !== null ) {
			$product_page_id = (int) $product_page_id;
			if ( $product_page_id !== 0 && get_post_type( $product_page_id ) !== 'page' ) {
				return new WP_REST_Response( [ 'message' => __( 'Product page must be a page.', 'wordpify' ) ], 400 );
			}
			$current['product_page_id'] = $product_page_id;
		}

		update_option( Options::SHOP_SETTINGS, $current );

		return new WP_REST_Response( [ 'saved' => true ], 200 );
	}
}
