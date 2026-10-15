<?php
/**
 * Plugin Name: Wordpify
 * Plugin URI:  https://github.com/wordpify
 * Description: WordPress + Shopify integration via Storefront API. Syncs products, manages cart, and handles agent customer accounts.
 * Version:     1.4.0
 * Author:      Web of D
 * Author URI: https://vincelayte.me/
 * Text Domain: wordpify
 * Domain Path: /languages
 * Requires PHP: 8.2
 * Requires at least: 6.7
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WORDPIFY_VERSION', '1.4.0' );
define( 'WORDPIFY_PATH', plugin_dir_path( __FILE__ ) );
define( 'WORDPIFY_URL', plugin_dir_url( __FILE__ ) );
define( 'WORDPIFY_BASENAME', plugin_basename( __FILE__ ) );

require_once WORDPIFY_PATH . 'vendor/autoload.php';

register_activation_hook( __FILE__, [ \Wordpify\Activator::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \Wordpify\Deactivator::class, 'deactivate' ] );

add_action( 'plugins_loaded', function () {
	\Wordpify\Plugin::get_instance();
} );
