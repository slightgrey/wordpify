<?php

declare(strict_types=1);

namespace Wordpify\Frontend\Shortcodes;

class CheckoutShortcode {

	/**
	 * Registers a template_redirect guard that redirects away from the checkout
	 * page when the user has no active cart.
	 */
	public static function guard(): void {
		add_action( 'template_redirect', function () {
			global $post;

			if ( ! is_singular() || ! $post ) {
				return;
			}

			$is_checkout = has_shortcode( $post->post_content, 'wordpify_checkout' )
				|| ( defined( 'ELEMENTOR_VERSION' ) && str_contains(
					(string) get_post_meta( $post->ID, '_elementor_data', true ),
					'wordpify-checkout'
				) );

			if ( ! $is_checkout ) {
				return;
			}

			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( home_url( '/' ) );
				exit;
			}
		} );
	}

	public static function render( array $atts ): string {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		ob_start();
		include WORDPIFY_PATH . 'templates/checkout/checkout.php';
		return ob_get_clean();
	}
}
