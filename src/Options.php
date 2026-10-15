<?php

declare(strict_types=1);

namespace Wordpify;

use Wordpify\Crypto\TokenEncryption;

class Options {

	const CONNECTION          = 'wordpify_connection';
	const SYNC_SETTINGS       = 'wordpify_sync_settings';
	const SHOP_SETTINGS       = 'wordpify_shop_settings';
	const VERSION             = 'wordpify_version';
	const DELETE_ON_UNINSTALL = 'wordpify_delete_on_uninstall';

	public static function get_connection(): array {
		$connection = (array) get_option( self::CONNECTION, [] ) + [
			'store_domain'   => '',
			'access_token'   => '',
			'connected'      => false,
			'last_tested_at' => null,
		];
		if ( $connection['access_token'] !== '' ) {
			$connection['access_token'] = TokenEncryption::maybe_decrypt( $connection['access_token'] );
		}
		return $connection;
	}

	public static function get_sync_settings(): array {
		return (array) get_option( self::SYNC_SETTINGS, [] ) + [
			'interval'          => 'daily',
			'last_sync_at'      => null,
			'last_sync_status'  => 'idle',
			'last_sync_message' => null,
			'product_count'     => 0,
		];
	}

	public static function get_shop_settings(): array {
		return (array) get_option( self::SHOP_SETTINGS, [] ) + [
			'gst_cart_enabled'       => true,
			'gst_product_enabled'    => false,
			'gst_text'               => '+ GST',
			'checkout_notice_enabled' => true,
			'checkout_notice'         => 'Shipping calculated at checkout',
			'product_page_id'         => 0,
			'placeholder_image_id'    => 0,
		];
	}

	public static function delete_on_uninstall(): bool {
		return (bool) get_option( self::DELETE_ON_UNINSTALL, false );
	}
}
