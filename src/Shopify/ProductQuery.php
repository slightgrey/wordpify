<?php

declare(strict_types=1);

namespace Wordpify\Shopify;

class ProductQuery {

	public static function get_all(): string {
		return <<<'GQL'
		query GetAllProducts($cursor: String) {
			products(first: 100, after: $cursor) {
				pageInfo {
					hasNextPage
					endCursor
				}
				nodes {
					id
					title
					descriptionHtml
					handle
					tags
					images(first: 10) {
						nodes {
							url
							altText
						}
					}
					collections(first: 20) {
						nodes {
							id
							handle
							title
						}
					}
					variants(first: 50) {
						nodes {
							id
							sku
							title
							availableForSale
							quantityAvailable
							requiresComponents
							price {
								amount
								currencyCode
							}
						}
					}
				}
			}
		}
		GQL;
	}

	/**
	 * Fetch the component products of bundle variants (Shopify Bundles).
	 * Kept separate from get_all() so the main page query stays cheap.
	 */
	public static function get_variant_components(): string {
		return <<<'GQL'
		query GetVariantComponents($ids: [ID!]!) {
			nodes(ids: $ids) {
				... on ProductVariant {
					id
					components(first: 30) {
						nodes {
							quantity
							productVariant {
								id
								title
								product {
									title
									handle
								}
							}
						}
					}
				}
			}
		}
		GQL;
	}
}
