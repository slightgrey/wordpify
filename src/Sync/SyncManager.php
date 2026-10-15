<?php

declare(strict_types=1);

namespace Wordpify\Sync;

use Wordpify\Options;
use Wordpify\PostTypes\ProductPostType;
use Wordpify\Shopify\ProductQuery;
use Wordpify\Shopify\StorefrontClient;

class SyncManager {

	public static function run(): void {
		if ( get_transient( 'wordpify_sync_lock' ) ) {
			return;
		}
		set_transient( 'wordpify_sync_lock', true, 300 );

		$settings                     = Options::get_sync_settings();
		$settings['last_sync_status'] = 'running';
		update_option( Options::SYNC_SETTINGS, $settings );

		try {
			$client     = new StorefrontClient();
			$cursor     = null;
			$count      = 0;
			$page       = 0;
			$max_pages  = (int) apply_filters( 'wordpify_sync_max_pages', 500 );
			$synced_ids = [];

			do {
				if ( ++$page > $max_pages ) {
					break;
				}

				$variables = $cursor ? [ 'cursor' => $cursor ] : [];
				$data = $client->query( ProductQuery::get_all(), $variables );

				if ( ! isset( $data['products'] ) || ! is_array( $data['products'] ) ) {
					throw new \RuntimeException( 'Shopify returned no product data. Ensure the Storefront API token has the "unauthenticated_read_product_listings" scope enabled under Storefront API integration in your Shopify app settings.' );
				}

				$products  = $data['products'];
				$page_info = $products['pageInfo'] ?? [];
				$nodes     = $products['nodes'] ?? [];

				$components_map = self::fetch_bundle_components( $client, $nodes );

				foreach ( $nodes as $product ) {
					ProductImporter::upsert( $product, $components_map );
					$synced_ids[] = (string) ( $product['id'] ?? '' );
					$count++;
				}

				$cursor = $page_info['hasNextPage'] ? ( $page_info['endCursor'] ?? null ) : null;
			} while ( $cursor );

			if ( $count > 0 ) {
				self::prune_unsynced( $synced_ids );
			}

			$settings['last_sync_status']  = 'success';
			$settings['last_sync_message'] = $count === 0
				? __( 'Sync completed but no products were found. Ensure products are published to this app\'s sales channel in Shopify admin, and that the Storefront API token has the "unauthenticated_read_product_listings" scope.', 'wordpify' )
				: null;
			$settings['product_count']     = $count;
			$settings['last_sync_at']      = gmdate( 'c' );
		} catch ( \Throwable $e ) {
			$settings['last_sync_status']  = 'error';
			$settings['last_sync_message'] = substr( wp_strip_all_tags( $e->getMessage() ), 0, 200 );
			$settings['last_sync_at']      = gmdate( 'c' );
		} finally {
			delete_transient( 'wordpify_sync_lock' );
		}

		update_option( Options::SYNC_SETTINGS, $settings );
	}

	/**
	 * Fetch components for every bundle variant (requiresComponents) in a page of products.
	 *
	 * @return array<string, array> Map of variant GID => component nodes.
	 */
	private static function fetch_bundle_components( StorefrontClient $client, array $products ): array {
		$bundle_variant_ids = [];
		foreach ( $products as $product ) {
			foreach ( $product['variants']['nodes'] ?? [] as $v ) {
				if ( ! empty( $v['requiresComponents'] ) && ! empty( $v['id'] ) ) {
					$bundle_variant_ids[] = (string) $v['id'];
				}
			}
		}

		$map = [];
		foreach ( array_chunk( $bundle_variant_ids, 250 ) as $ids ) {
			$data = $client->query( ProductQuery::get_variant_components(), [ 'ids' => $ids ] );

			foreach ( $data['nodes'] ?? [] as $node ) {
				if ( ! empty( $node['id'] ) ) {
					$map[ (string) $node['id'] ] = $node['components']['nodes'] ?? [];
				}
			}
		}

		return $map;
	}

	/**
	 * Trash any synced product post whose Shopify ID didn't come back in this run
	 * (deleted, unpublished, or removed from the sales channel in Shopify).
	 */
	private static function prune_unsynced( array $synced_ids ): void {
		$stale_ids = get_posts( [
			'post_type'      => ProductPostType::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => '_wordpify_shopify_product_id',
					'value'   => $synced_ids,
					'compare' => 'NOT IN',
				],
			],
		] );

		foreach ( $stale_ids as $post_id ) {
			wp_trash_post( $post_id );
		}
	}
}
