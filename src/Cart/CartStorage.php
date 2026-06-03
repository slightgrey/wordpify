<?php

declare(strict_types=1);

namespace Wordpify\Cart;

class CartStorage {

	public static function get_cart_id( int $user_id ): ?string {
		$id = get_user_meta( $user_id, 'wordpify_cart_id', true );
		return $id ?: null;
	}

	public static function set_cart_id( int $user_id, string $cart_id ): void {
		update_user_meta( $user_id, 'wordpify_cart_id', $cart_id );
	}

	public static function clear_cart_id( int $user_id ): void {
		delete_user_meta( $user_id, 'wordpify_cart_id' );
	}
}
