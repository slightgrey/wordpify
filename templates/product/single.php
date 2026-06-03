<?php
/**
 * Template: Single Product
 *
 * @var WP_Post $product
 * @var array   $variants
 * @var array   $images
 */
defined( 'ABSPATH' ) || exit;
$first_image = $images[0] ?? '';
?>
<div class="wordpify-product-single" id="wordpify-product-<?php echo esc_attr( $product->ID ); ?>">
	<div class="wordpify-product-single__gallery">
		<?php foreach ( $images as $img ) : ?>
		<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" loading="lazy" />
		<?php endforeach; ?>
	</div>
	<div class="wordpify-product-single__info">
		<h2 class="wordpify-product-single__title"><?php echo esc_html( $product->post_title ); ?></h2>

		<?php if ( ! empty( $variants ) ) : ?>
		<div class="wordpify-product-single__price">
			<?php echo esc_html( $variants[0]['price'] . ' ' . $variants[0]['currency_code'] ); ?>
		</div>
		<?php endif; ?>

		<div class="wordpify-product-single__description">
			<?php echo wp_kses_post( $product->post_content ); ?>
		</div>

		<?php if ( count( $variants ) > 1 ) : ?>
		<div class="wordpify-product-single__variants">
			<label for="wordpify-variant-select-<?php echo esc_attr( $product->ID ); ?>"><?php esc_html_e( 'Options', 'wordpify' ); ?></label>
			<select id="wordpify-variant-select-<?php echo esc_attr( $product->ID ); ?>" class="wordpify-variant-select">
				<?php foreach ( $variants as $v ) : ?>
				<option value="<?php echo esc_attr( $v['id'] ); ?>" <?php echo $v['available'] ? '' : 'disabled'; ?>>
					<?php
					$v_title = ( ! empty( $v['title'] ) && $v['title'] !== 'Default Title' ) ? $v['title'] . ' — ' : '';
					echo esc_html( $v_title . $v['price'] . ' ' . $v['currency_code'] );
					?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<button class="wordpify-btn wordpify-btn--primary wordpify-add-to-cart"
			data-variant-id="<?php echo esc_attr( $variants[0]['id'] ?? '' ); ?>"
			data-product-id="<?php echo esc_attr( $product->ID ); ?>"
			data-quantity="1">
			<?php esc_html_e( 'Add to Cart', 'wordpify' ); ?>
		</button>
	</div>
</div>
