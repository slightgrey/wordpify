<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

use Wordpify\Auth\CustomerAuthHandler;
use Wordpify\Auth\WPUserLinker;
use Wordpify\Shopify\CustomerMutation;
use Wordpify\Shopify\StorefrontClient;

class AccountAddressShortcode {

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user_id = get_current_user_id();
		CustomerAuthHandler::ensure_linked( $user_id );

		$token     = WPUserLinker::get_shopify_token( $user_id );
		$addresses = [];

		if ( $token ) {
			try {
				$client    = new StorefrontClient();
				$data      = $client->query( CustomerMutation::get_addresses(), [ 'customerAccessToken' => $token ] );
				$addresses = $data['customer']['addresses']['nodes'] ?? [];
			} catch ( \RuntimeException ) {
				// Fail gracefully.
			}
		}

		ob_start();
		include WORDPIFY_PATH . 'templates/account/address.php';
		return ob_get_clean();
	}
}
