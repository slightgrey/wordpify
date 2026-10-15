<?php

declare(strict_types=1);

namespace Wordpify\PostTypes;

class ProductPostType {

	const POST_TYPE = 'wordpify_product';

	/**
	 * Set when the admin disables a product. Sync never writes it, so a disabled product
	 * keeps syncing but stays hidden. Enabled = meta absent.
	 */
	const DISABLED_META = '_wordpify_disabled';

	public static function is_disabled( int $post_id ): bool {
		return get_post_meta( $post_id, self::DISABLED_META, true ) === '1';
	}

	public static function set_disabled( int $post_id, bool $disabled ): void {
		if ( $disabled ) {
			update_post_meta( $post_id, self::DISABLED_META, '1' );
		} else {
			delete_post_meta( $post_id, self::DISABLED_META );
		}
	}

	/**
	 * meta_query clause limiting storefront queries to enabled products.
	 */
	public static function enabled_meta_clause(): array {
		return [ 'key' => self::DISABLED_META, 'compare' => 'NOT EXISTS' ];
	}

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
