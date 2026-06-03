<?php

declare(strict_types=1);

namespace Wordpify;

use Wordpify\Admin\AdminPage;
use Wordpify\Cart\CartManager;
use Wordpify\Elementor\ElementorManager;
use Wordpify\Frontend\PublicAssets;
use Wordpify\Frontend\ShortcodeRegistry;
use Wordpify\PostTypes\CollectionTaxonomy;
use Wordpify\PostTypes\ProductPostType;
use Wordpify\REST\AuthEndpoint;
use Wordpify\REST\CartEndpoint;
use Wordpify\REST\ConnectionEndpoint;
use Wordpify\REST\ProductsEndpoint;
use Wordpify\REST\SettingsEndpoint;
use Wordpify\REST\SyncEndpoint;
use Wordpify\Sync\SyncManager;

class Plugin {

	private static ?self $instance = null;

	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	private function __construct() {}

	private function init(): void {
		// Post types + taxonomy.
		add_action( 'init', [ ProductPostType::class, 'register' ] );
		add_action( 'init', [ CollectionTaxonomy::class, 'register' ] );

		// Shortcodes.
		add_action( 'init', [ ShortcodeRegistry::class, 'register_all' ] );

		// REST API.
		add_action( 'rest_api_init', [ ConnectionEndpoint::class, 'register_routes' ] );
		add_action( 'rest_api_init', [ SettingsEndpoint::class, 'register_routes' ] );
		add_action( 'rest_api_init', [ SyncEndpoint::class, 'register_routes' ] );
		add_action( 'rest_api_init', [ ProductsEndpoint::class, 'register_routes' ] );
		add_action( 'rest_api_init', [ CartEndpoint::class, 'register_routes' ] );
		add_action( 'rest_api_init', [ AuthEndpoint::class, 'register_routes' ] );

		// Admin.
		add_action( 'admin_menu', [ AdminPage::class, 'register' ] );
		add_action( 'admin_enqueue_scripts', [ AdminPage::class, 'enqueue_assets' ] );

		// Frontend assets.
		add_action( 'wp_enqueue_scripts', [ PublicAssets::class, 'enqueue' ] );

		// Cron handler.
		add_action( 'wordpify_sync_products', [ SyncManager::class, 'run' ] );

		// Elementor widgets (guarded by elementor/loaded check).
		add_action( 'elementor/widgets/register', [ ElementorManager::class, 'register_widgets' ] );
		add_action( 'elementor/elements/categories_registered', [ ElementorManager::class, 'register_category' ] );
	}
}
