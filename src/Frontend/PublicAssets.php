<?php

declare(strict_types=1);

namespace Wordpify\Frontend;

use Wordpify\Cart\CartStorage;

class PublicAssets {

	public static function enqueue(): void {
		$js_path  = WORDPIFY_PATH . 'assets/js/wordpify-public.js';
		$css_path = WORDPIFY_PATH . 'assets/css/wordpify-public.css';

		wp_enqueue_style(
			'wordpify-public',
			WORDPIFY_URL . 'assets/css/wordpify-public.css',
			[],
			file_exists( $css_path ) ? filemtime( $css_path ) : WORDPIFY_VERSION
		);

		wp_enqueue_script(
			'wordpify-public',
			WORDPIFY_URL . 'assets/js/wordpify-public.js',
			[],
			file_exists( $js_path ) ? filemtime( $js_path ) : WORDPIFY_VERSION,
			true
		);

		$user_id = get_current_user_id();
		$cart_id = $user_id ? CartStorage::get_cart_id( $user_id ) : null;

		wp_localize_script( 'wordpify-public', 'wordpifyPublic', [
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'restUrl' => rest_url(),
			'cartId'  => $cart_id,
			'userId'  => $user_id,
		] );
	}
}
