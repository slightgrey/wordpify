<?php

declare(strict_types=1);

namespace Wordpify\Frontend;

use Wordpify\Options;
use Wordpify\PostTypes\ProductPostType;
use WP_Post;

/**
 * Links products to the "product page" chosen in Shop settings: a normal WP page
 * holding [wordpify_product] (or the Elementor widget) with no handle, which reads
 * the product from ?item=<handle>. Works with any theme or page builder.
 */
class ProductLink {

	const QUERY_ARG = 'item';

	/**
	 * Product page URL for a handle, or '' when no product page is configured.
	 */
	public static function url( string $handle ): string {
		$page_id = self::page_id();
		if ( ! $page_id || $handle === '' ) {
			return '';
		}

		return add_query_arg( self::QUERY_ARG, rawurlencode( $handle ), get_permalink( $page_id ) );
	}

	/**
	 * Handle requested via the query string (empty if none).
	 */
	public static function requested_handle(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only lookup.
		return isset( $_GET[ self::QUERY_ARG ] ) ? sanitize_title( wp_unslash( $_GET[ self::QUERY_ARG ] ) ) : '';
	}

	/**
	 * Find an enabled synced product post by its Shopify handle.
	 * Disabled products count as not found (popup, product page, REST, titles).
	 */
	public static function find_by_handle( string $handle ): ?WP_Post {
		static $cache = [];

		if ( $handle === '' ) {
			return null;
		}
		if ( array_key_exists( $handle, $cache ) ) {
			return $cache[ $handle ];
		}

		$posts = get_posts( [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'meta_query'     => [
				[ 'key' => '_wordpify_handle', 'value' => $handle ],
				ProductPostType::enabled_meta_clause(),
			],
		] );

		return $cache[ $handle ] = $posts[0] ?? null;
	}

	/**
	 * The product shown on the current request's product page, or null.
	 */
	public static function current_product(): ?WP_Post {
		$page_id = self::page_id();
		// Mirrors the shortcodes, which only render for logged-in users.
		if ( ! $page_id || is_admin() || ! is_user_logged_in() || ! is_page( $page_id ) ) {
			return null;
		}

		return self::find_by_handle( self::requested_handle() );
	}

	/**
	 * Whether a page holds a shortcode or the matching Elementor widget.
	 */
	public static function page_contains( int $page_id, string $shortcode, string $widget ): bool {
		$page = $page_id ? get_post( $page_id ) : null;
		if ( ! $page ) {
			return false;
		}

		if ( has_shortcode( $page->post_content, $shortcode ) ) {
			return true;
		}

		$elementor = (string) get_post_meta( $page_id, '_elementor_data', true );

		return str_contains( $elementor, '"widgetType":"' . $widget . '"' )
			|| (bool) preg_match( '/\[' . preg_quote( $shortcode, '/' ) . '[\s\]]/', $elementor );
	}

	/**
	 * Whether the product page also holds the product grid. Products then open in a
	 * popup over the grid, so the page keeps its own title.
	 */
	public static function is_popup_page(): bool {
		static $popup = null;

		return $popup ??= self::page_contains( self::page_id(), 'wordpify_products', 'wordpify-product-grid' );
	}

	/**
	 * Replace the product page's own title (the theme's H1) with the product name.
	 * Limited to the page itself inside the loop, so menus and page lists keep "Product".
	 */
	public static function filter_the_title( $title, $post_id = 0 ) {
		if ( (int) $post_id !== self::page_id() || ! self::page_id() || self::is_popup_page() || ! in_the_loop() ) {
			return $title;
		}

		$product = self::current_product();

		return $product ? esc_html( $product->post_title ) : $title;
	}

	/**
	 * Use the product's title as the browser tab title on the product page.
	 */
	public static function filter_document_title( array $parts ): array {
		$product = self::is_popup_page() ? null : self::current_product();
		if ( $product ) {
			$parts['title'] = $product->post_title;
		}

		return $parts;
	}

	private static function page_id(): int {
		static $page_id = null;

		if ( $page_id === null ) {
			$page_id = (int) Options::get_shop_settings()['product_page_id'];
			$page_id = ( $page_id && get_post_status( $page_id ) === 'publish' ) ? $page_id : 0;
		}

		return $page_id;
	}
}
