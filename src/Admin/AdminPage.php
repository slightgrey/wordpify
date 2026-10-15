<?php

declare(strict_types=1);

namespace Wordpify\Admin;

class AdminPage {

	const MENU_SLUG = 'wordpify';
	const ASSET_HANDLE = 'wordpify-admin';

	public static function register(): void {
		add_menu_page(
			__( 'Wordpify', 'wordpify' ),
			__( 'Wordpify', 'wordpify' ),
			'manage_options',
			self::MENU_SLUG,
			[ self::class, 'render' ],
			'dashicons-store',
			56
		);
	}

	public static function render(): void {
		echo '<div id="wordpify-admin-root"></div>';
	}

	public static function enqueue_assets( string $hook ): void {
		if ( strpos( $hook, self::MENU_SLUG ) === false ) {
			return;
		}

		$js_path  = WORDPIFY_PATH . 'dist/admin/wordpify-admin.js';
		$css_path = WORDPIFY_PATH . 'dist/admin/wordpify-admin.css';

		if ( ! file_exists( $js_path ) ) {
			add_action( 'admin_notices', function () {
				echo '<div class="notice notice-warning"><p><strong>Wordpify:</strong> Admin assets not built. Run <code>npm run build</code> inside the plugin directory.</p></div>';
			} );
			return;
		}

		// Media Library modal for the Shop settings placeholder image picker.
		wp_enqueue_media();

		wp_enqueue_script(
			self::ASSET_HANDLE,
			WORDPIFY_URL . 'dist/admin/wordpify-admin.js',
			[],
			filemtime( $js_path ),
			true
		);

		if ( file_exists( $css_path ) ) {
			wp_enqueue_style(
				self::ASSET_HANDLE,
				WORDPIFY_URL . 'dist/admin/wordpify-admin.css',
				[],
				filemtime( $css_path )
			);
		}

		wp_localize_script( self::ASSET_HANDLE, 'wordpifyAdmin', [
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => rest_url(),
			'version' => WORDPIFY_VERSION,
		] );
	}
}
