<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Wordpify\Frontend\Shortcodes\OrderHistoryShortcode;

class OrderHistoryWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-order-history'; }
	public function get_title(): string { return __( 'Order History', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-table'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Settings', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'limit', [
			'label'   => __( 'Orders Limit', 'wordpify' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 10,
			'min'     => 1,
			'max'     => 50,
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		echo OrderHistoryShortcode::render( [ 'limit' => $s['limit'] ] );
	}
}
