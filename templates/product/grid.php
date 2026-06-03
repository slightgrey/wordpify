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
?>
<div class="wordpify-product-grid wordpify-product-grid--cols-<?php echo esc_attr( $columns ); ?>">
	<?php foreach ( $products as $product ) :
		$images   = json_decode( get_post_meta( $product->ID, '_wordpify_images', true ) ?: '[]', true );
		$variants = json_decode( get_post_meta( $product->ID, '_wordpify_variants', true ) ?: '[]', true );
		$handle   = get_post_meta( $product->ID, '_wordpify_handle', true );
		$image    = $images[0] ?? '';
		$excerpt  = $product->post_content ? wp_trim_words( wp_strip_all_tags( $product->post_content ), 20, '…' ) : '';

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
			<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" loading="lazy" />
		</div>
		<?php endif; ?>
		<div class="wordpify-product-card__body">
			<h3 class="wordpify-product-card__title" style="font-size:<?php echo esc_attr( $style['heading_font_size'] ); ?>px;color:<?php echo esc_attr( $style['heading_color'] ); ?>;"><?php echo esc_html( $product->post_title ); ?></h3>
			<?php if ( $excerpt ) : ?>
			<p class="wordpify-product-card__excerpt" style="font-size:<?php echo esc_attr( $style['desc_font_size'] ); ?>px;color:<?php echo esc_attr( $style['desc_color'] ); ?>;"><?php echo esc_html( $excerpt ); ?></p>
			<?php endif; ?>
			<?php if ( count( $variants ) === 1 ) : ?>
			<?php if ( $price ) : ?>
			<p class="wordpify-product-card__price"><?php echo esc_html( $price ); ?></p>
			<?php endif; ?>
			<button class="wordpify-btn wordpify-btn--primary wordpify-add-to-cart"
				style="background-color:<?php echo esc_attr( $style['btn_bg_color'] ); ?>;color:<?php echo esc_attr( $style['btn_text_color'] ); ?>;border-radius:<?php echo esc_attr( $style['btn_border_radius'] ); ?>px;"
				data-variant-id="<?php echo esc_attr( $variants[0]['id'] ); ?>"
				data-quantity="1">
				<?php esc_html_e( 'Add to Cart', 'wordpify' ); ?>
			</button>
			<?php else : ?>
			<?php if ( $price ) : ?>
			<p class="wordpify-product-card__price"><?php echo esc_html( $price ); ?></p>
			<?php endif; ?>
			<a href="#wordpify-product-<?php echo esc_attr( $product->ID ); ?>" class="wordpify-btn wordpify-btn--secondary wordpify-view-product">
				<?php esc_html_e( 'View Options', 'wordpify' ); ?>
			</a>
			<?php endif; ?>
		</div>
	</article>
	<?php endforeach; ?>
</div>
