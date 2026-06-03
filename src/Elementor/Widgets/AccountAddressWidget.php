<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Wordpify\Frontend\Shortcodes\AccountAddressShortcode;

class AccountAddressWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-account-address'; }
	public function get_title(): string { return __( 'Address Management', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-map-pin'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {}

	protected function render(): void {
		echo AccountAddressShortcode::render( [] );
	}
}
