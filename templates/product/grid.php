<?php
/**
 * Template: Product Grid
 *
 * @var WP_Post[] $products
 * @var int       $columns
 * @var array     $style
 */
defined( 'ABSPATH' ) || exit;

$currency_symbols = [
	'USD' => '$', 'AUD' => '$', 'CAD' => '$', 'SGD' => '$', 'HKD' => '$', 'MXN' => '$',
	'EUR' => '€',
	'GBP' => '£',
	'JPY' => '¥',
	'NZD' => '$',
];
$shop = (array) get_option( 'wordpify_shop_settings', [] ) + [
	'gst_product_enabled' => false,
	'gst_text'            => '+ GST',
];
?>
<div class="wordpify-product-grid wordpify-product-grid--cols-<?php echo esc_attr( $columns ); ?>">
	<?php foreach ( $products as $product ) :
		$images   = json_decode( get_post_meta( $product->ID, '_wordpify_images', true ) ?: '[]', true );
		$variants = json_decode( get_post_meta( $product->ID, '_wordpify_variants', true ) ?: '[]', true );
		$handle   = get_post_meta( $product->ID, '_wordpify_handle', true );
		$url      = \Wordpify\Frontend\ProductLink::url( (string) $handle );
		$image    = \Wordpify\Frontend\ProductImage::or_placeholder( $images[0] ?? '' );

		// Formatted description (clamped to a few lines in CSS). Bundles without one list what's included.
		$description = trim( wp_strip_all_tags( $product->post_content ) ) !== '' ? wp_kses_post( $product->post_content ) : '';
		$components  = $description === '' ? ( $variants[0]['components'] ?? [] ) : [];

		$price = '';
		if ( ! empty( $variants ) ) {
			$raw_price    = (float) $variants[0]['price'];
			$currency     = strtoupper( $variants[0]['currency_code'] ?? 'USD' );
			$symbol       = $currency_symbols[ $currency ] ?? ( $currency . ' ' );
			$price        = $symbol . number_format( $raw_price, 2 ) . ' ' . $currency;
		}
	?>
	<article class="wordpify-product-card" data-product-id="<?php echo esc_attr( $product->ID ); ?>" data-handle="<?php echo esc_attr( $handle ); ?>">
		<?php if ( $image ) : ?>
		<div class="wordpify-product-card__image" style="height:<?php echo esc_attr( $style['image_height'] ); ?>px;">
			<?php if ( $url ) : ?><a href="<?php echo esc_url( $url ); ?>" class="wordpify-product-card__link" tabindex="-1" aria-hidden="true"><?php endif; ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" loading="lazy" />
			<?php if ( $url ) : ?></a><?php endif; ?>
		</div>
		<?php endif; ?>
		<div class="wordpify-product-card__body">
			<h3 class="wordpify-product-card__title" style="font-size:<?php echo esc_attr( $style['heading_font_size'] ); ?>px;color:<?php echo esc_attr( $style['heading_color'] ); ?>;">
				<?php if ( $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" class="wordpify-product-card__link"><?php echo esc_html( $product->post_title ); ?></a>
				<?php else : ?>
				<?php echo esc_html( $product->post_title ); ?>
				<?php endif; ?>
			</h3>
			<?php if ( $description || $components ) : ?>
			<div class="wordpify-product-card__excerpt" style="font-size:<?php echo esc_attr( $style['desc_font_size'] ); ?>px;color:<?php echo esc_attr( $style['desc_color'] ); ?>;">
				<?php if ( $description ) : ?>
				<?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post above. ?>
				<?php else : ?>
				<p class="wordpify-product-card__bundle-title"><?php esc_html_e( "What's included", 'wordpify' ); ?></p>
				<ul>
					<?php foreach ( $components as $component ) : ?>
					<li><?php echo esc_html( (int) $component['quantity'] . ' × ' . $component['product_title'] ); ?></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ( count( $variants ) === 1 ) : ?>
			<?php if ( $price ) : ?>
			<p class="wordpify-product-card__price"><?php echo esc_html( $price ); ?><?php if ( ! empty( $shop['gst_product_enabled'] ) ) : ?> <small><?php echo esc_html( $shop['gst_text'] ); ?></small><?php endif; ?></p>
			<?php endif; ?>
			<button class="wordpify-btn wordpify-btn--primary wordpify-add-to-cart"
				style="background-color:<?php echo esc_attr( $style['btn_bg_color'] ); ?>;color:<?php echo esc_attr( $style['btn_text_color'] ); ?>;border-radius:<?php echo esc_attr( $style['btn_border_radius'] ); ?>px;"
				data-variant-id="<?php echo esc_attr( $variants[0]['id'] ); ?>"
				data-quantity="1">
				<?php esc_html_e( 'Add to Cart', 'wordpify' ); ?>
			</button>
			<?php else : ?>
			<?php if ( $price ) : ?>
			<p class="wordpify-product-card__price"><?php echo esc_html( $price ); ?><?php if ( ! empty( $shop['gst_product_enabled'] ) ) : ?> <small><?php echo esc_html( $shop['gst_text'] ); ?></small><?php endif; ?></p>
			<?php endif; ?>
			<a href="<?php echo esc_url( $url ?: '#wordpify-product-' . $product->ID ); ?>" class="wordpify-btn wordpify-btn--secondary wordpify-view-product">
				<?php esc_html_e( 'View Options', 'wordpify' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</article>
	<?php endforeach; ?>
</div>
