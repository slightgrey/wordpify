<?php

declare(strict_types=1);

namespace Wordpify\Shopify;

class CartMutation {

	public static function create(): string {
		return <<<'GQL'
		mutation CartCreate($input: CartInput!) {
			cartCreate(input: $input) {
				cart {
					id
					checkoutUrl
					totalQuantity
					lines(first: 50) {
						nodes {
							id
							quantity
							merchandise {
								... on ProductVariant {
									id
									title
									price { amount currencyCode }
									image { url altText }
									product { id title handle }
								}
							}
						}
					}
					cost { totalAmount { amount currencyCode } subtotalAmount { amount currencyCode } }
				}
				userErrors { field message }
			}
		}
		GQL;
	}

	public static function get(): string {
		return <<<'GQL'
		query GetCart($cartId: ID!) {
			cart(id: $cartId) {
				id
				checkoutUrl
				totalQuantity
				lines(first: 50) {
					nodes {
						id
						quantity
						merchandise {
							... on ProductVariant {
								id
								title
								price { amount currencyCode }
								image { url altText }
								product { id title handle }
							}
						}
					}
				}
				cost { totalAmount { amount currencyCode } subtotalAmount { amount currencyCode } }
			}
		}
		GQL;
	}

	public static function lines_add(): string {
		return <<<'GQL'
		mutation CartLinesAdd($cartId: ID!, $lines: [CartLineInput!]!) {
			cartLinesAdd(cartId: $cartId, lines: $lines) {
				cart {
					id
					checkoutUrl
					totalQuantity
					lines(first: 50) {
						nodes {
							id
							quantity
							merchandise {
								... on ProductVariant {
									id
									title
									price { amount currencyCode }
									image { url altText }
									product { id title handle }
								}
							}
						}
					}
					cost { totalAmount { amount currencyCode } }
				}
				userErrors { field message }
			}
		}
		GQL;
	}

	public static function lines_update(): string {
		return <<<'GQL'
		mutation CartLinesUpdate($cartId: ID!, $lines: [CartLineUpdateInput!]!) {
			cartLinesUpdate(cartId: $cartId, lines: $lines) {
				cart {
					id
					totalQuantity
					lines(first: 50) {
						nodes {
							id
							quantity
							merchandise {
								... on ProductVariant {
									id
									title
									price { amount currencyCode }
									product { id title handle }
								}
							}
						}
					}
					cost { totalAmount { amount currencyCode } }
				}
				userErrors { field message }
			}
		}
		GQL;
	}

	public static function lines_remove(): string {
		return <<<'GQL'
		mutation CartLinesRemove($cartId: ID!, $lineIds: [ID!]!) {
			cartLinesRemove(cartId: $cartId, lineIds: $lineIds) {
				cart {
					id
					totalQuantity
					lines(first: 50) {
						nodes {
							id
							quantity
							merchandise {
								... on ProductVariant {
									id
									title
									price { amount currencyCode }
									product { id title handle }
								}
							}
						}
					}
					cost { totalAmount { amount currencyCode } }
				}
				userErrors { field message }
			}
		}
		GQL;
	}
}
