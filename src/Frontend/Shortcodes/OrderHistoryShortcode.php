<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\Auth\CustomerAuthHandler;
use Wordpify\Auth\WPUserLinker;
use Wordpify\Shopify\CustomerMutation;
use Wordpify\Shopify\StorefrontClient;

class OrderHistoryShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$atts = shortcode_atts( [ 'limit' => 10 ], $atts, 'wordpify_orders' );

		$user_id = get_current_user_id();
		CustomerAuthHandler::ensure_linked( $user_id );

		$token  = WPUserLinker::get_shopify_token( $user_id );
		$orders = [];

		if ( $token ) {
			try {
				$client = new StorefrontClient();
				$data   = $client->query( CustomerMutation::get_orders(), [ 'customerAccessToken' => $token ] );
				$orders = array_slice( $data['customer']['orders']['nodes'] ?? [], 0, (int) $atts['limit'] );
			} catch ( \RuntimeException ) {
				// Fail gracefully.
			}
		}

		ob_start();
		include WORDPIFY_PATH . 'templates/account/orders.php';
		return ob_get_clean();
	}
}
