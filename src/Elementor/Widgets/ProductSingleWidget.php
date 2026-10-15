<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Wordpify\Frontend\Shortcodes\ProductSingleShortcode;

class ProductSingleWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-product-single'; }
	public function get_title(): string { return __( 'Single Product', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-single-product'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Settings', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'handle', [
			'label'       => __( 'Product Handle', 'wordpify' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'e.g. my-product', 'wordpify' ),
			'description' => __( 'Leave empty on your product page to show the product linked from the product grid.', 'wordpify' ),
		] );

		$this->add_control( 'show_title', [
			'label'       => __( 'Product Title', 'wordpify' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'auto',
			'options'     => [
				'auto' => __( 'Auto', 'wordpify' ),
				'yes'  => __( 'Show', 'wordpify' ),
				'no'   => __( 'Hide', 'wordpify' ),
			],
			'description' => __( 'Auto hides it on the product page, where the page title already shows the product name. Choose Show if your layout hides the page title.', 'wordpify' ),
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		echo ProductSingleShortcode::render( [
			'handle'     => $s['handle'],
			'show_title' => $s['show_title'] ?? 'auto',
		] );
	}
}
