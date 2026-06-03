<?php

declare(strict_types=1);

namespace Wordpify\Auth;

use Wordpify\Crypto\TokenEncryption;

class WPUserLinker {

	public static function get_shopify_customer_id( int $user_id ): string {
		return (string) get_user_meta( $user_id, 'wordpify_shopify_customer_id', true );
	}

	public static function get_shopify_token( int $user_id ): string {
		$raw = (string) get_user_meta( $user_id, 'wordpify_shopify_access_token', true );
		return $raw !== '' ? TokenEncryption::maybe_decrypt( $raw ) : '';
	}

	public static function get_shopify_password( int $user_id ): string {
		$raw = (string) get_user_meta( $user_id, 'wordpify_shopify_customer_password', true );
		return $raw !== '' ? TokenEncryption::maybe_decrypt( $raw ) : '';
	}

	public static function get_shopify_token_expires_at( int $user_id ): string {
		return (string) get_user_meta( $user_id, 'wordpify_shopify_token_expires_at', true );
	}

	public static function save_customer(
		int $user_id,
		string $shopify_customer_id,
		string $access_token,
		string $expires_at = '',
		string $password = ''
	): void {
		update_user_meta( $user_id, 'wordpify_shopify_customer_id', $shopify_customer_id );
		update_user_meta( $user_id, 'wordpify_shopify_access_token', TokenEncryption::encrypt( $access_token ) );
		if ( $expires_at !== '' ) {
			update_user_meta( $user_id, 'wordpify_shopify_token_expires_at', $expires_at );
		}
		if ( $password !== '' ) {
			update_user_meta( $user_id, 'wordpify_shopify_customer_password', TokenEncryption::encrypt( $password ) );
		}
	}

	public static function is_linked( int $user_id ): bool {
		return ! empty( self::get_shopify_customer_id( $user_id ) )
			&& ! empty( self::get_shopify_token( $user_id ) );
	}

	public static function is_token_expiring( int $user_id ): bool {
		$expires_at = self::get_shopify_token_expires_at( $user_id );
		if ( $expires_at === '' ) {
			return false;
		}
		$expires_ts = strtotime( $expires_at );
		return $expires_ts !== false && $expires_ts < ( time() + DAY_IN_SECONDS );
	}
}
