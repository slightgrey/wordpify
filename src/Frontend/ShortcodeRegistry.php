<?php

declare(strict_types=1);

namespace Wordpify\Frontend;

use Wordpify\Frontend\Shortcodes\AccountAddressShortcode;
use Wordpify\Frontend\Shortcodes\AccountDetailsShortcode;
use Wordpify\Frontend\Shortcodes\CartShortcode;
use Wordpify\Frontend\Shortcodes\MiniCartShortcode;
use Wordpify\Frontend\Shortcodes\OrderHistoryShortcode;
use Wordpify\Frontend\Shortcodes\ProductGridShortcode;
use Wordpify\Frontend\Shortcodes\ProductSingleShortcode;

class ShortcodeRegistry {

	public static function register_all(): void {
		add_shortcode( 'wordpify_products',  [ ProductGridShortcode::class,   'render' ] );
		add_shortcode( 'wordpify_product',   [ ProductSingleShortcode::class, 'render' ] );
		add_shortcode( 'wordpify_account',   [ AccountDetailsShortcode::class, 'render' ] );
		add_shortcode( 'wordpify_address',   [ AccountAddressShortcode::class, 'render' ] );
		add_shortcode( 'wordpify_orders',    [ OrderHistoryShortcode::class,   'render' ] );
		add_shortcode( 'wordpify_cart',      [ CartShortcode::class,           'render' ] );
		add_shortcode( 'wordpify_mini_cart', [ MiniCartShortcode::class,       'render' ] );
	}
}
