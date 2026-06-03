<?php

declare(strict_types=1);

namespace Wordpify\Crypto;

class TokenEncryption {

	private static function key(): string {
		$salt = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'wordpify_fallback_key';
		return hash_hmac( 'sha256', 'wordpify_token_key', $salt, true );
	}

	public static function encrypt( string $plaintext ): string {
		if ( $plaintext === '' ) {
			return '';
		}
		$nonce  = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$cipher = sodium_crypto_secretbox( $plaintext, $nonce, self::key() );
		return base64_encode( $nonce . $cipher );
	}

	public static function decrypt( string $ciphertext ): string {
		if ( $ciphertext === '' ) {
			return '';
		}
		$decoded = base64_decode( $ciphertext, true );
		if ( $decoded === false || strlen( $decoded ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
			return '';
		}
		$nonce  = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$cipher = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$plain  = sodium_crypto_secretbox_open( $cipher, $nonce, self::key() );
		return $plain !== false ? $plain : '';
	}

	/**
	 * Decrypt if the value looks encrypted; return as-is if it appears to be plaintext.
	 * Used for migrating existing plaintext tokens without requiring a forced re-save.
	 */
	public static function maybe_decrypt( string $value ): string {
		if ( $value === '' ) {
			return '';
		}
		$decoded = base64_decode( $value, true );
		if ( $decoded === false || strlen( $decoded ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
			return $value;
		}
		$nonce  = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$cipher = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$plain  = sodium_crypto_secretbox_open( $cipher, $nonce, self::key() );
		return $plain !== false ? $plain : $value;
	}
}
