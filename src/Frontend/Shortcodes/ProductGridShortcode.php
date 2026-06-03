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
			'columns'    => 3,
			'collection' => '',
			'limit'      => 12,
			'orderby'    => 'title',
		], $atts, 'wordpify_products' );

		$args = [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => (int) $atts['limit'],
			'orderby'        => in_array( $atts['orderby'], [ 'title', 'date' ], true ) ? $atts['orderby'] : 'title',
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

		ob_start();
		include WORDPIFY_PATH . 'templates/product/grid.php';
		return ob_get_clean();
	}
}
