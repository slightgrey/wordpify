<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\Cart\CartManager;

class CartShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$cart = [];
		try {
			$manager = new CartManager();
			$cart    = $manager->get_or_create( get_current_user_id() );
		} catch ( \RuntimeException ) {
			// Fail gracefully.
		}

		ob_start();
		include WORDPIFY_PATH . 'templates/cart/cart.php';
		return ob_get_clean();
	}
}
