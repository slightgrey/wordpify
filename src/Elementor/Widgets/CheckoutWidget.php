<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Wordpify\Frontend\Shortcodes\CheckoutShortcode;

class CheckoutWidget extends Widget_Base {

	public function get_name(): string     { return 'wordpify-checkout'; }
	public function get_title(): string    { return __( 'Checkout', 'wordpify' ); }
	public function get_icon(): string     { return 'eicon-checkout'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {}

	protected function render(): void {
		echo CheckoutShortcode::render( [] );
	}
}
