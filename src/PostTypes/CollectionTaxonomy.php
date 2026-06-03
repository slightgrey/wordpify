<?php

declare(strict_types=1);

namespace Wordpify\PostTypes;

class CollectionTaxonomy {

	const TAXONOMY = 'wordpify_collection';

	public static function register(): void {
		register_taxonomy( self::TAXONOMY, ProductPostType::POST_TYPE, [
			'labels'            => [
				'name'          => __( 'Collections', 'wordpify' ),
				'singular_name' => __( 'Collection', 'wordpify' ),
			],
			'public'            => false,
			'publicly_queryable' => false,
			'show_ui'           => false,
			'show_in_rest'      => false,
			'hierarchical'      => false,
			'rewrite'           => false,
		] );
	}
}
