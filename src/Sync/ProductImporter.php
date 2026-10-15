<?php

declare(strict_types=1);

namespace Wordpify\Sync;

use Wordpify\PostTypes\CollectionTaxonomy;
use Wordpify\PostTypes\ProductPostType;

class ProductImporter {

	/**
	 * Insert or update a WordPress post from a Shopify product node.
	 * Returns the WP post ID.
	 *
	 * @param array $components_map Bundle variant GID => component nodes (see SyncManager).
	 */
	public static function upsert( array $product, array $components_map = [] ): int {
		$shopify_id = $product['id'] ?? '';

		$existing = get_posts( [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => [ 'publish', 'trash' ],
			'posts_per_page' => 1,
			'meta_key'       => '_wordpify_shopify_product_id',
			'meta_value'     => $shopify_id,
			'fields'         => 'ids',
		] );

		$post_data = [
			'post_title'   => sanitize_text_field( $product['title'] ?? '' ),
			'post_content' => wp_kses_post( $product['descriptionHtml'] ?? '' ),
			'post_status'  => 'publish',
			'post_type'    => ProductPostType::POST_TYPE,
		];

		if ( ! empty( $existing ) ) {
			$post_id = (int) $existing[0];

			if ( get_post_status( $post_id ) === 'trash' ) {
				wp_untrash_post( $post_id );
			}

			$post_data['ID'] = $post_id;
			$post_id         = wp_update_post( $post_data, true );
		} else {
			$post_id = wp_insert_post( $post_data, true );
		}

		if ( is_wp_error( $post_id ) ) {
			throw new \RuntimeException( 'Failed to upsert product: ' . $post_id->get_error_message() );
		}

		// Post meta.
		update_post_meta( $post_id, '_wordpify_shopify_product_id', $shopify_id );
		update_post_meta( $post_id, '_wordpify_handle', sanitize_text_field( $product['handle'] ?? '' ) );
		update_post_meta( $post_id, '_wordpify_tags', wp_json_encode( $product['tags'] ?? [] ) );
		update_post_meta( $post_id, '_wordpify_synced_at', gmdate( 'c' ) );

		// Variants.
		$variants  = [];
		$is_bundle = false;
		foreach ( $product['variants']['nodes'] ?? [] as $v ) {
			$requires_components = (bool) ( $v['requiresComponents'] ?? false );
			$is_bundle           = $is_bundle || $requires_components;

			$variants[] = [
				'id'                  => $v['id'],
				'sku'                 => $v['sku'] ?? '',
				'title'               => $v['title'] ?? '',
				'price'               => $v['price']['amount'] ?? '0',
				'currency_code'       => $v['price']['currencyCode'] ?? '',
				'inventory_quantity'  => $v['quantityAvailable'] ?? 0,
				'available'           => $v['availableForSale'] ?? false,
				'requires_components' => $requires_components,
				'components'          => self::map_components( $components_map[ $v['id'] ] ?? [] ),
			];
		}
		update_post_meta( $post_id, '_wordpify_variants', wp_json_encode( $variants ) );
		update_post_meta( $post_id, '_wordpify_is_bundle', $is_bundle ? '1' : '0' );

		// Images (CDN URLs only).
		$images = array_map(
			fn( $img ) => $img['url'] ?? '',
			$product['images']['nodes'] ?? []
		);
		update_post_meta( $post_id, '_wordpify_images', wp_json_encode( array_filter( $images ) ) );

		// Collections as taxonomy terms.
		$term_ids = [];
		foreach ( $product['collections']['nodes'] ?? [] as $col ) {
			$term = self::upsert_collection_term( $col );
			if ( $term ) {
				$term_ids[] = $term;
			}
		}
		wp_set_object_terms( $post_id, $term_ids, CollectionTaxonomy::TAXONOMY );

		return (int) $post_id;
	}

	/**
	 * Normalise Shopify ProductVariantComponent nodes into the stored component shape.
	 */
	private static function map_components( array $nodes ): array {
		$components = [];
		foreach ( $nodes as $c ) {
			$variant = $c['productVariant'] ?? [];
			if ( empty( $variant['id'] ) ) {
				continue;
			}

			$components[] = [
				'variant_id'    => sanitize_text_field( $variant['id'] ),
				'product_title' => sanitize_text_field( $variant['product']['title'] ?? '' ),
				'variant_title' => sanitize_text_field( $variant['title'] ?? '' ),
				'handle'        => sanitize_text_field( $variant['product']['handle'] ?? '' ),
				'quantity'      => (int) ( $c['quantity'] ?? 1 ),
			];
		}

		return $components;
	}

	private static function upsert_collection_term( array $col ): ?int {
		$shopify_col_id = $col['id'] ?? '';
		$handle         = $col['handle'] ?? '';
		$title          = $col['title'] ?? '';

		// Find existing term by meta.
		$terms = get_terms( [
			'taxonomy'   => CollectionTaxonomy::TAXONOMY,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'   => 'wordpify_shopify_collection_id',
					'value' => $shopify_col_id,
				],
			],
		] );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$term_id = (int) $terms[0]->term_id;
			wp_update_term( $term_id, CollectionTaxonomy::TAXONOMY, [ 'name' => $title ] );
		} else {
			$result = wp_insert_term( $title, CollectionTaxonomy::TAXONOMY, [ 'slug' => $handle ] );
			if ( is_wp_error( $result ) ) {
				return null;
			}
			$term_id = (int) $result['term_id'];
		}

		update_term_meta( $term_id, 'wordpify_shopify_collection_id', $shopify_col_id );
		update_term_meta( $term_id, 'wordpify_collection_handle', $handle );

		return $term_id;
	}
}
