<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\Frontend\ProductLink;
use Wordpify\PostTypes\ProductPostType;
use WP_Post;

class ProductSingleShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$atts = shortcode_atts( [
			'handle'     => '',
			'id'         => 0,
			// auto: hide on the product page, where the page title (H1) already shows the product name.
			'show_title' => 'auto',
			// auto: popup when the page also holds the product grid and no product is fixed by id/handle.
			'display'    => 'auto',
		], $atts, 'wordpify_product' );

		$fixed_product = $atts['id'] || $atts['handle'];
		$popup         = $atts['display'] === 'popup'
			|| ( $atts['display'] === 'auto' && ! $fixed_product
				&& ProductLink::page_contains( (int) get_queried_object_id(), 'wordpify_products', 'wordpify-product-grid' ) );

		if ( $popup ) {
			return self::render_popup();
		}

		if ( $atts['id'] ) {
			$product = get_post( (int) $atts['id'] );
			if ( ! $product || $product->post_type !== ProductPostType::POST_TYPE || ProductPostType::is_disabled( $product->ID ) ) {
				return '';
			}
		} else {
			// No handle attribute: use the product from the URL (?item=handle),
			// so one "product page" can display any product linked from the grid.
			$handle  = $atts['handle'] ? sanitize_text_field( $atts['handle'] ) : ProductLink::requested_handle();
			$product = ProductLink::find_by_handle( $handle );
			if ( ! $product ) {
				// Message only for ?item= links (e.g. an old shared link); a fixed handle that is
				// missing or disabled renders nothing, like a fixed id.
				return ! $atts['handle'] && $handle !== ''
					? '<p class="wordpify-product-single__not-found">' . esc_html__( 'Product not found.', 'wordpify' ) . '</p>'
					: '';
			}
		}

		$show_title = match ( $atts['show_title'] ) {
			'yes'   => true,
			'no'    => false,
			default => ProductLink::current_product()?->ID !== $product->ID,
		};

		return self::render_product( $product, $show_title );
	}

	/**
	 * Render the single product template for a product.
	 */
	public static function render_product( WP_Post $product, bool $show_title = true ): string {
		$variants = json_decode( get_post_meta( $product->ID, '_wordpify_variants', true ) ?: '[]', true );
		$images   = json_decode( get_post_meta( $product->ID, '_wordpify_images', true ) ?: '[]', true );

		ob_start();
		include WORDPIFY_PATH . 'templates/product/single.php';
		return ob_get_clean();
	}

	/**
	 * Popup shell for the grid page. Opens on load when the URL holds ?item=handle;
	 * the public JS fills it when a grid product is clicked.
	 */
	private static function render_popup(): string {
		$product = ProductLink::find_by_handle( ProductLink::requested_handle() );
		$content = $product ? self::render_product( $product ) : '';

		ob_start();
		include WORDPIFY_PATH . 'templates/product/popup.php';
		return ob_get_clean();
	}
}
