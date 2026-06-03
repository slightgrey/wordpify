<?php

declare(strict_types=1);

namespace Wordpify\Sync;

use Wordpify\Options;
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
			$client    = new StorefrontClient();
			$cursor    = null;
			$count     = 0;
			$page      = 0;
			$max_pages = (int) apply_filters( 'wordpify_sync_max_pages', 500 );

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

				foreach ( $nodes as $product ) {
					ProductImporter::upsert( $product );
					$count++;
				}

				$cursor = $page_info['hasNextPage'] ? ( $page_info['endCursor'] ?? null ) : null;
			} while ( $cursor );

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
}
