<?php

declare(strict_types=1);

namespace Wordpify\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Wordpify\Frontend\Shortcodes\ProductGridShortcode;

class ProductGridWidget extends Widget_Base {

	public function get_name(): string { return 'wordpify-product-grid'; }
	public function get_title(): string { return __( 'Product Grid', 'wordpify' ); }
	public function get_icon(): string { return 'eicon-products'; }
	public function get_categories(): array { return [ 'wordpify' ]; }

	protected function register_controls(): void {

		// ── Content Tab ───────────────────────────────────────────────────────
		$this->start_controls_section( 'content_section', [
			'label' => __( 'Settings', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'columns', [
			'label'   => __( 'Columns', 'wordpify' ),
			'type'    => Controls_Manager::SELECT,
			'default' => '3',
			'options' => [ '2' => '2', '3' => '3', '4' => '4' ],
		] );

		$this->add_control( 'limit', [
			'label'   => __( 'Limit', 'wordpify' ),
			'type'    => Controls_Manager::NUMBER,
			'default' => 12,
			'min'     => 1,
			'max'     => 100,
		] );

		$this->add_control( 'collection', [
			'label'       => __( 'Collection Slug', 'wordpify' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'e.g. summer-sale', 'wordpify' ),
		] );

		$this->add_control( 'orderby', [
			'label'   => __( 'Order By', 'wordpify' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'menu_order',
			'options' => [
				'menu_order' => __( 'Custom Order', 'wordpify' ),
				'title'      => __( 'Title', 'wordpify' ),
				'date'       => __( 'Date', 'wordpify' ),
			],
		] );

		$this->end_controls_section();

		// ── Style Tab: Image ──────────────────────────────────────────────────
		$this->start_controls_section( 'style_image', [
			'label' => __( 'Image', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'image_size', [
			'label'   => __( 'Image Size', 'wordpify' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'medium',
			'options' => [
				'small'  => __( 'Small (150px)', 'wordpify' ),
				'medium' => __( 'Medium (220px)', 'wordpify' ),
				'large'  => __( 'Large (350px)', 'wordpify' ),
				'custom' => __( 'Custom', 'wordpify' ),
			],
		] );

		$this->add_control( 'image_height', [
			'label'      => __( 'Custom Height', 'wordpify' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 50, 'max' => 800 ] ],
			'default'    => [ 'size' => 220, 'unit' => 'px' ],
			'condition'  => [ 'image_size' => 'custom' ],
		] );

		$this->end_controls_section();

		// ── Style Tab: Heading ────────────────────────────────────────────────
		$this->start_controls_section( 'style_heading', [
			'label' => __( 'Heading', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'heading_color', [
			'label'   => __( 'Color', 'wordpify' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#111827',
		] );

		$this->add_control( 'heading_font_size', [
			'label'      => __( 'Font Size', 'wordpify' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 10, 'max' => 60 ] ],
			'default'    => [ 'size' => 16, 'unit' => 'px' ],
		] );

		$this->end_controls_section();

		// ── Style Tab: Description ────────────────────────────────────────────
		$this->start_controls_section( 'style_desc', [
			'label' => __( 'Description', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'desc_color', [
			'label'   => __( 'Color', 'wordpify' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#6b7280',
		] );

		$this->add_control( 'desc_font_size', [
			'label'      => __( 'Font Size', 'wordpify' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 10, 'max' => 30 ] ],
			'default'    => [ 'size' => 13, 'unit' => 'px' ],
		] );

		$this->end_controls_section();

		// ── Style Tab: Add to Cart Button ─────────────────────────────────────
		$this->start_controls_section( 'style_btn', [
			'label' => __( 'Add to Cart Button', 'wordpify' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'btn_bg_color', [
			'label'   => __( 'Background Color', 'wordpify' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#7c3aed',
		] );

		$this->add_control( 'btn_text_color', [
			'label'   => __( 'Text Color', 'wordpify' ),
			'type'    => Controls_Manager::COLOR,
			'default' => '#ffffff',
		] );

		$this->add_control( 'btn_border_radius', [
			'label'      => __( 'Border Radius', 'wordpify' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
			'default'    => [ 'size' => 6, 'unit' => 'px' ],
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$preset_heights = [ 'small' => 150, 'medium' => 220, 'large' => 350 ];
		$image_height   = $s['image_size'] === 'custom'
			? (int) ( $s['image_height']['size'] ?? 220 )
			: ( $preset_heights[ $s['image_size'] ] ?? 220 );

		echo ProductGridShortcode::render( [
			'columns'           => $s['columns'],
			'limit'             => $s['limit'],
			'collection'        => $s['collection'],
			'orderby'           => $s['orderby'],
			'image_height'      => $image_height,
			'heading_color'     => $s['heading_color'] ?: '#111827',
			'heading_font_size' => (int) ( $s['heading_font_size']['size'] ?? 16 ),
			'desc_color'        => $s['desc_color'] ?: '#6b7280',
			'desc_font_size'    => (int) ( $s['desc_font_size']['size'] ?? 13 ),
			'btn_bg_color'      => $s['btn_bg_color'] ?: '#7c3aed',
			'btn_text_color'    => $s['btn_text_color'] ?: '#ffffff',
			'btn_border_radius' => (int) ( $s['btn_border_radius']['size'] ?? 6 ),
		] );
	}
}
