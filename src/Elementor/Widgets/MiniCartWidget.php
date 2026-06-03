<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Wordpify\Frontend\Shortcodes\MiniCartShortcode;

class MiniCartWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-mini-cart'; }
	public function get_title(): string { return __( 'Mini Cart', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-cart-light'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Settings', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'show_count', [
			'label'        => __( 'Show Item Count', 'wordpify' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Yes', 'wordpify' ),
			'label_off'    => __( 'No', 'wordpify' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		echo MiniCartShortcode::render( [ 'show_count' => $s['show_count'] === 'yes' ] );
	}
}
