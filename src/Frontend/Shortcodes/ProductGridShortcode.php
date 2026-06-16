<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\PostTypes\CollectionTaxonomy;
use Wordpify\PostTypes\ProductPostType;
use WP_Query;

class ProductGridShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$atts = shortcode_atts( [
			'columns'           => 3,
			'collection'        => '',
			'limit'             => 12,
			'orderby'           => 'menu_order',
			'image_height'      => 220,
			'heading_color'     => '#111827',
			'heading_font_size' => 16,
			'desc_color'        => '#6b7280',
			'desc_font_size'    => 13,
			'btn_bg_color'      => '#7c3aed',
			'btn_text_color'    => '#ffffff',
			'btn_border_radius' => 6,
		], $atts, 'wordpify_products' );

		$args = [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => (int) $atts['limit'],
			'orderby'        => in_array( $atts['orderby'], [ 'title', 'date', 'menu_order' ], true ) ? $atts['orderby'] : 'menu_order',
			'order'          => 'ASC',
		];

		if ( $atts['collection'] ) {
			$args['tax_query'] = [ [
				'taxonomy' => CollectionTaxonomy::TAXONOMY,
				'field'    => 'slug',
				'terms'    => sanitize_text_field( $atts['collection'] ),
			] ];
		}

		$query    = new WP_Query( $args );
		$products = $query->posts;
		$columns  = max( 2, min( 4, (int) $atts['columns'] ) );

		$style = [
			'image_height'      => max( 50, (int) $atts['image_height'] ),
			'heading_color'     => ! empty( $atts['heading_color'] ) ? sanitize_text_field( $atts['heading_color'] ) : '#111827',
			'heading_font_size' => max( 10, (int) $atts['heading_font_size'] ),
			'desc_color'        => ! empty( $atts['desc_color'] ) ? sanitize_text_field( $atts['desc_color'] ) : '#6b7280',
			'desc_font_size'    => max( 10, (int) $atts['desc_font_size'] ),
			'btn_bg_color'      => ! empty( $atts['btn_bg_color'] ) ? sanitize_text_field( $atts['btn_bg_color'] ) : '#7c3aed',
			'btn_text_color'    => ! empty( $atts['btn_text_color'] ) ? sanitize_text_field( $atts['btn_text_color'] ) : '#ffffff',
			'btn_border_radius' => max( 0, (int) $atts['btn_border_radius'] ),
		];

		ob_start();
		include WORDPIFY_PATH . 'templates/product/grid.php';
		return ob_get_clean();
	}
}
