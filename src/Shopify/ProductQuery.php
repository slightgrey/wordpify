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
}
