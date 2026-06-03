<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\Cart\CartManager;

class MiniCartShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$atts = shortcode_atts( [
			'show_count' => true,
			'cart_url'   => '',
		], $atts, 'wordpify_mini_cart' );

		$item_count = 0;
		try {
			$manager    = new CartManager();
			$cart       = $manager->get_or_create( get_current_user_id() );
			$item_count = (int) ( $cart['totalQuantity'] ?? 0 );
		} catch ( \RuntimeException ) {
			// Fail gracefully.
		}

		$show_count = filter_var( $atts['show_count'], FILTER_VALIDATE_BOOLEAN );
		$cart_url   = esc_url_raw( $atts['cart_url'] );

		ob_start();
		include WORDPIFY_PATH . 'templates/cart/mini-cart.php';
		return ob_get_clean();
	}
}
