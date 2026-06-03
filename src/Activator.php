<?php

declare(strict_types=1);

namespace Wordpify;

use Wordpify\Crypto\TokenEncryption;
use Wordpify\Sync\CronScheduler;

class Activator {

	public static function activate(): void {
		if ( version_compare( PHP_VERSION, '8.2', '<' ) ) {
			deactivate_plugins( WORDPIFY_BASENAME );
			wp_die( 'Wordpify requires PHP 8.2 or higher.' );
		}

		if ( version_compare( get_bloginfo( 'version' ), '6.7', '<' ) ) {
			deactivate_plugins( WORDPIFY_BASENAME );
			wp_die( 'Wordpify requires WordPress 6.7 or higher.' );
		}

		add_option( Options::CONNECTION, [
			'store_domain'   => '',
			'access_token'   => '',
			'connected'      => false,
			'last_tested_at' => null,
		] );

		add_option( Options::SYNC_SETTINGS, [
			'interval'          => 'daily',
			'last_sync_at'      => null,
			'last_sync_status'  => 'idle',
			'last_sync_message' => null,
			'product_count'     => 0,
		] );

		add_option( Options::VERSION, WORDPIFY_VERSION );
		add_option( Options::DELETE_ON_UNINSTALL, false );

		CronScheduler::schedule( 'daily' );

		self::maybe_encrypt_existing_tokens();

		flush_rewrite_rules();
	}

	/**
	 * One-time migration: re-save any plaintext API tokens as encrypted values.
	 */
	private static function maybe_encrypt_existing_tokens(): void {
		$raw = (array) get_option( Options::CONNECTION, [] );
		if ( ! empty( $raw['access_token'] ) ) {
			$decoded = base64_decode( $raw['access_token'], true );
			if ( $decoded === false || strlen( $decoded ) <= SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
				$raw['access_token'] = TokenEncryption::encrypt( $raw['access_token'] );
				update_option( Options::CONNECTION, $raw );
			}
		}
	}
}
