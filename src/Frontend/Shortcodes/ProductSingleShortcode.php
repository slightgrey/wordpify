<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\PostTypes\ProductPostType;
use WP_Query;

class ProductSingleShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$atts = shortcode_atts( [
			'handle' => '',
			'id'     => 0,
		], $atts, 'wordpify_product' );

		if ( $atts['id'] ) {
			$product = get_post( (int) $atts['id'] );
			if ( ! $product || $product->post_type !== ProductPostType::POST_TYPE ) {
				return '';
			}
		} elseif ( $atts['handle'] ) {
			$query = new WP_Query( [
				'post_type'      => ProductPostType::POST_TYPE,
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'meta_key'       => '_wordpify_handle',
				'meta_value'     => sanitize_text_field( $atts['handle'] ),
			] );
			if ( empty( $query->posts ) ) {
				return '';
			}
			$product = $query->posts[0];
		} else {
			return '';
		}

		$variants = json_decode( get_post_meta( $product->ID, '_wordpify_variants', true ) ?: '[]', true );
		$images   = json_decode( get_post_meta( $product->ID, '_wordpify_images', true ) ?: '[]', true );

		ob_start();
		include WORDPIFY_PATH . 'templates/product/single.php';
		return ob_get_clean();
	}
}
