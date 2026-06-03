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
			'default' => 'title',
			'options' => [ 'title' => __( 'Title', 'wordpify' ), 'date' => __( 'Date', 'wordpify' ) ],
		] );

		$this->end_controls_section();
	}

	protected function render(): void {
		$s = $this->get_settings_for_display();
		echo ProductGridShortcode::render( [
			'columns'    => $s['columns'],
			'limit'      => $s['limit'],
			'collection' => $s['collection'],
			'orderby'    => $s['orderby'],
		] );
	}
}
