<?php

declare(strict_types=1);

namespace Wordpify\Cart;

use Wordpify\Shopify\CartMutation;
use Wordpify\Shopify\StorefrontClient;

class CartManager {

	private StorefrontClient $client;

	public function __construct() {
		$this->client = new StorefrontClient();
	}

	/**
	 * Get the current cart, creating one if none exists.
	 */
	public function get_or_create( int $user_id ): array {
		$cart_id = CartStorage::get_cart_id( $user_id );

		if ( $cart_id ) {
			try {
				$data = $this->client->query( CartMutation::get(), [ 'cartId' => $cart_id ] );
				if ( ! empty( $data['cart'] ) ) {
					return $data['cart'];
				}
			} catch ( \RuntimeException ) {
				// Cart expired or invalid — create a new one.
			}
		}

		return $this->create_cart( $user_id );
	}

	private function create_cart( int $user_id ): array {
		$data       = $this->client->query( CartMutation::create(), [ 'input' => (object) [] ] );
		$cart       = $data['cartCreate']['cart'] ?? null;
		$user_error = $data['cartCreate']['userErrors'][0]['message'] ?? null;

		if ( empty( $cart['id'] ) ) {
			throw new \RuntimeException(
				'Failed to create Shopify cart.' . ( $user_error ? ' ' . $user_error : ' Ensure the Storefront API token has the "unauthenticated_write_checkouts" scope.' )
			);
		}

		CartStorage::set_cart_id( $user_id, $cart['id'] );
		return $cart;
	}

	/**
	 * Add a variant to the cart.
	 *
	 * @param array $lines [[ 'merchandiseId' => 'gid://...', 'quantity' => 1 ], ...]
	 */
	public function add_items( int $user_id, array $lines ): array {
		$cart    = $this->get_or_create( $user_id );
		$cart_id = $cart['id'] ?? '';

		if ( ! $cart_id ) {
			throw new \RuntimeException( 'Could not retrieve or create a Shopify cart.' );
		}

		$data = $this->client->query( CartMutation::lines_add(), [
			'cartId' => $cart_id,
			'lines'  => $lines,
		] );

		return $data['cartLinesAdd']['cart'] ?? $cart;
	}

	/**
	 * Update a cart line quantity.
	 *
	 * @param array $lines [[ 'id' => 'gid://...lineId', 'quantity' => 2 ], ...]
	 */
	public function update_items( int $user_id, array $lines ): array {
		$cart_id = CartStorage::get_cart_id( $user_id );
		if ( ! $cart_id ) {
			throw new \RuntimeException( 'No active cart.' );
		}

		$data = $this->client->query( CartMutation::lines_update(), [
			'cartId' => $cart_id,
			'lines'  => $lines,
		] );

		return $data['cartLinesUpdate']['cart'] ?? [];
	}

	/**
	 * Remove cart lines by line ID.
	 */
	public function remove_items( int $user_id, array $line_ids ): array {
		$cart_id = CartStorage::get_cart_id( $user_id );
		if ( ! $cart_id ) {
			throw new \RuntimeException( 'No active cart.' );
		}

		$data = $this->client->query( CartMutation::lines_remove(), [
			'cartId'  => $cart_id,
			'lineIds' => $line_ids,
		] );

		return $data['cartLinesRemove']['cart'] ?? [];
	}

	/**
	 * Get the Shopify checkout URL for the current cart.
	 */
	public function get_checkout_url( int $user_id ): string {
		$cart = $this->get_or_create( $user_id );
		return $cart['checkoutUrl'] ?? '';
	}
}
