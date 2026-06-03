<?php

declare(strict_types=1);

namespace Wordpify\Elementor;

use Wordpify\Elementor\Widgets\AccountAddressWidget;
use Wordpify\Elementor\Widgets\AccountDetailsWidget;
use Wordpify\Elementor\Widgets\CartWidget;
use Wordpify\Elementor\Widgets\CheckoutWidget;
use Wordpify\Elementor\Widgets\MiniCartWidget;
use Wordpify\Elementor\Widgets\OrderHistoryWidget;
use Wordpify\Elementor\Widgets\ProductGridWidget;
use Wordpify\Elementor\Widgets\ProductSingleWidget;

class ElementorManager {

	public static function register_category( $elements_manager ): void {
		$elements_manager->add_category( 'wordpify', [
			'title' => __( 'Wordpify', 'wordpify' ),
			'icon'  => 'eicon-cart',
		] );
	}

	public static function register_widgets( $widgets_manager ): void {
		$widgets = [
			ProductGridWidget::class,
			ProductSingleWidget::class,
			AccountDetailsWidget::class,
			AccountAddressWidget::class,
			OrderHistoryWidget::class,
			CartWidget::class,
			MiniCartWidget::class,
			CheckoutWidget::class,
		];

		foreach ( $widgets as $widget_class ) {
			$widgets_manager->register( new $widget_class() );
		}
	}
}
