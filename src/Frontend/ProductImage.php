<?php

declare(strict_types=1);

namespace Wordpify\Frontend;

use Wordpify\Options;

/**
 * Fallback image for products (and cart lines / bundle items) without one:
 * the image chosen in Shop settings, else the bundled neutral placeholder.
 */
class ProductImage {

	public static function placeholder(): string {
		static $url = null;

		if ( $url === null ) {
			$attachment_id = (int) Options::get_shop_settings()['placeholder_image_id'];
			$url           = ( $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'large' ) : '' )
				?: WORDPIFY_URL . 'assets/images/placeholder.svg';
		}

		return $url;
	}

	/**
	 * The given image URL, or the placeholder when empty.
	 */
	public static function or_placeholder( ?string $url ): string {
		return $url ?: self::placeholder();
	}
}
