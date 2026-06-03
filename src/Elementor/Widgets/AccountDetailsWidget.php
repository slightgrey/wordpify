<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Wordpify\Frontend\Shortcodes\AccountDetailsShortcode;

class AccountDetailsWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-account-details'; }
	public function get_title(): string { return __( 'Account Details', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-person'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {}

	protected function render(): void {
		echo AccountDetailsShortcode::render( [] );
	}
}
