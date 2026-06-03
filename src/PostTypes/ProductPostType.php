<?php

declare(strict_types=1);

namespace Wordpify\PostTypes;

class ProductPostType {

	const POST_TYPE = 'wordpify_product';

	public static function register(): void {
		register_post_type( self::POST_TYPE, [
			'labels'              => [
				'name'          => __( 'Products', 'wordpify' ),
				'singular_name' => __( 'Product', 'wordpify' ),
			],
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'query_var'           => false,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'supports'            => [ 'title', 'editor' ],
		] );
	}
}
