<?php

declare(strict_types=1);

namespace Wordpify\REST;

use Wordpify\PostTypes\ProductPostType;
use WP_Query;
use WP_REST_Request;
use WP_REST_Response;

class ProductsEndpoint {

	public static function register_routes(): void {
		register_rest_route( 'wordpify/v1', '/products', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'get' ],
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'args'                => [
				'page'     => [ 'default' => 1, 'type' => 'integer' ],
				'per_page' => [ 'default' => 20, 'type' => 'integer' ],
			],
		] );

		register_rest_route( 'wordpify/v1', '/products/order', [
			'methods'             => 'GET',
			'callback'            => [ self::class, 'get_order' ],
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
		] );

		register_rest_route( 'wordpify/v1', '/products/order', [
			'methods'             => 'POST',
			'callback'            => [ self::class, 'update_order' ],
			'permission_callback' => fn() => current_user_can( 'manage_options' ),
			'args'                => [
				'order' => [ 'required' => true, 'type' => 'array' ],
			],
		] );
	}

	public static function get( WP_REST_Request $request ): WP_REST_Response {
		$page     = (int) $request->get_param( 'page' );
		$per_page = min( (int) $request->get_param( 'per_page' ), 100 );

		$query = new WP_Query( [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => $per_page,
			'paged'          => $page,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );

		$products = [];
		foreach ( $query->posts as $post ) {
			$variants = json_decode( get_post_meta( $post->ID, '_wordpify_variants', true ) ?: '[]', true );
			$images   = json_decode( get_post_meta( $post->ID, '_wordpify_images', true ) ?: '[]', true );

			$products[] = [
				'id'             => $post->ID,
				'title'          => $post->post_title,
				'handle'         => get_post_meta( $post->ID, '_wordpify_handle', true ),
				'shopify_id'     => get_post_meta( $post->ID, '_wordpify_shopify_product_id', true ),
				'variant_count'  => count( $variants ),
				'image_url'      => $images[0] ?? '',
				'synced_at'      => get_post_meta( $post->ID, '_wordpify_synced_at', true ),
			];
		}

		return new WP_REST_Response( [
			'products'    => $products,
			'total'       => (int) $query->found_posts,
			'total_pages' => (int) $query->max_num_pages,
		], 200 );
	}

	public static function get_order(): WP_REST_Response {
		$query = new WP_Query( [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
		] );

		$products = [];
		foreach ( $query->posts as $post ) {
			$images = json_decode( get_post_meta( $post->ID, '_wordpify_images', true ) ?: '[]', true );

			$products[] = [
				'id'         => $post->ID,
				'title'      => $post->post_title,
				'image_url'  => $images[0] ?? '',
				'menu_order' => (int) $post->menu_order,
			];
		}

		return new WP_REST_Response( [ 'products' => $products ], 200 );
	}

	public static function update_order( WP_REST_Request $request ): WP_REST_Response {
		global $wpdb;

		$order = $request->get_param( 'order' );
		if ( ! is_array( $order ) || empty( $order ) ) {
			return new WP_REST_Response( [ 'message' => __( 'order must be a non-empty array of product IDs.', 'wordpify' ) ], 400 );
		}

		foreach ( array_values( $order ) as $index => $id ) {
			$id = (int) $id;
			if ( $id <= 0 || get_post_type( $id ) !== ProductPostType::POST_TYPE ) {
				continue;
			}

			$wpdb->update( $wpdb->posts, [ 'menu_order' => $index ], [ 'ID' => $id ] );
			clean_post_cache( $id );
		}

		return new WP_REST_Response( [ 'success' => true ], 200 );
	}
}
