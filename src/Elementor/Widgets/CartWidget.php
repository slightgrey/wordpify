<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Wordpify\Frontend\Shortcodes\CartShortcode;

class CartWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-cart'; }
	public function get_title(): string { return __( 'Cart', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-cart'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {}

	protected function render(): void {
		echo CartShortcode::render( [] );
	}
}
