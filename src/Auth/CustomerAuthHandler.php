<?php

declare(strict_types=1);

namespace Wordpify\Auth;

use Wordpify\Shopify\CustomerMutation;
use Wordpify\Shopify\StorefrontClient;

class CustomerAuthHandler {

	/**
	 * Ensure the WP agent user has a linked Shopify customer account.
	 * Auto-creates or re-links transparently.
	 *
	 * Returns true on success, false on failure.
	 */
	public static function ensure_linked( int $wp_user_id ): bool {
		if ( WPUserLinker::is_linked( $wp_user_id ) ) {
			return true;
		}

		$wp_user = get_userdata( $wp_user_id );
		if ( ! $wp_user ) {
			return false;
		}

		$client = new StorefrontClient();

		// Try to create a new Shopify customer.
		$password = wp_generate_password( 20, true, true );

		try {
			$data   = $client->query( CustomerMutation::create(), [
				'input' => [
					'email'     => $wp_user->user_email,
					'firstName' => $wp_user->first_name ?: $wp_user->display_name,
					'lastName'  => $wp_user->last_name ?: '',
					'password'  => $password,
				],
			] );

			$errors = $data['customerCreate']['customerUserErrors'] ?? [];

			// If email already taken, attempt to get a token for existing account is not
			// possible via Storefront API without the password. Store a pending state.
			if ( ! empty( $errors ) ) {
				foreach ( $errors as $err ) {
					if ( ( $err['code'] ?? '' ) === 'TAKEN' ) {
						// Cannot auto-link an existing Shopify account without credentials.
						// The account will remain unlinked until the user explicitly connects.
						return false;
					}
				}
				return false;
			}

			$customer_id = $data['customerCreate']['customer']['id'] ?? '';
			if ( ! $customer_id ) {
				return false;
			}

			// Now get an access token for the newly created customer.
			$token_data = $client->query( CustomerMutation::access_token_create(), [
				'input' => [
					'email'    => $wp_user->user_email,
					'password' => $password,
				],
			] );

			$token_errors = $token_data['customerAccessTokenCreate']['customerUserErrors'] ?? [];
			if ( ! empty( $token_errors ) ) {
				return false;
			}

			$access_token = $token_data['customerAccessTokenCreate']['customerAccessToken']['accessToken'] ?? '';
			$expires_at   = $token_data['customerAccessTokenCreate']['customerAccessToken']['expiresAt'] ?? '';
			if ( ! $access_token ) {
				return false;
			}

			WPUserLinker::save_customer( $wp_user_id, $customer_id, $access_token, $expires_at, $password );
			return true;

		} catch ( \RuntimeException $_e ) {
			return false;
		}
	}

	/**
	 * Refresh the Shopify access token for a user.
	 * Called when an API call returns an auth error.
	 */
	public static function refresh_token( int $wp_user_id, string $email = '', string $password = '' ): bool {
		if ( $email === '' || $password === '' ) {
			$wp_user  = get_userdata( $wp_user_id );
			$email    = $wp_user ? $wp_user->user_email : $email;
			$password = WPUserLinker::get_shopify_password( $wp_user_id );
		}

		if ( $email === '' || $password === '' ) {
			return false;
		}

		$client = new StorefrontClient();

		try {
			$data = $client->query( CustomerMutation::access_token_create(), [
				'input' => [ 'email' => $email, 'password' => $password ],
			] );

			$token      = $data['customerAccessTokenCreate']['customerAccessToken']['accessToken'] ?? '';
			$expires_at = $data['customerAccessTokenCreate']['customerAccessToken']['expiresAt'] ?? '';
			if ( ! $token ) {
				return false;
			}

			$customer_id = WPUserLinker::get_shopify_customer_id( $wp_user_id );
			WPUserLinker::save_customer( $wp_user_id, $customer_id, $token, $expires_at );
			return true;
		} catch ( \RuntimeException $_e ) {
			return false;
		}
	}

	public static function maybe_refresh_token( int $wp_user_id ): void {
		if ( WPUserLinker::is_token_expiring( $wp_user_id ) ) {
			self::refresh_token( $wp_user_id );
		}
	}
}
