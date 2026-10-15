<?php
/**
 * Template: Single Product
 *
 * @var WP_Post $product
 * @var array   $variants
 * @var array   $images
 * @var bool    $show_title
 */
defined( 'ABSPATH' ) || exit;
$first_image = $images[0] ?? '';
?>
<div class="wordpify-product-single" id="wordpify-product-<?php echo esc_attr( $product->ID ); ?>">
	<?php if ( $first_image ) : ?>
	<div class="wordpify-product-single__gallery">
		<div class="wordpify-product-single__main-image">
			<img src="<?php echo esc_url( $first_image ); ?>" alt="<?php echo esc_attr( $product->post_title ); ?>" />
		</div>
		<?php if ( count( $images ) > 1 ) : ?>
		<div class="wordpify-product-single__thumbs">
			<?php foreach ( $images as $i => $img ) : ?>
			<button type="button" class="wordpify-product-single__thumb<?php echo $i === 0 ? ' is-active' : ''; ?>" data-image="<?php echo esc_url( $img ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'wordpify' ), $i + 1 ) ); ?>">
				<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" />
			</button>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<div class="wordpify-product-single__info">
		<?php if ( $show_title ) : ?>
		<h2 class="wordpify-product-single__title"><?php echo esc_html( $product->post_title ); ?></h2>
		<?php endif; ?>

		<?php if ( ! empty( $variants ) ) : ?>
		<div class="wordpify-product-single__price">
			<?php echo esc_html( $variants[0]['price'] . ' ' . $variants[0]['currency_code'] ); ?>
		</div>
		<?php endif; ?>

		<div class="wordpify-product-single__description">
			<?php echo wp_kses_post( $product->post_content ); ?>
		</div>

		<?php if ( ! empty( $variants[0]['components'] ) ) : ?>
		<div class="wordpify-product-single__bundle">
			<h3 class="wordpify-product-single__bundle-title"><?php esc_html_e( "What's included", 'wordpify' ); ?></h3>
			<ul class="wordpify-product-single__bundle-items">
				<?php foreach ( $variants[0]['components'] as $component ) : ?>
				<li>
					<?php
					$c_title = $component['product_title'];
					if ( ! empty( $component['variant_title'] ) && $component['variant_title'] !== 'Default Title' ) {
						$c_title .= ' — ' . $component['variant_title'];
					}
					echo esc_html( (int) $component['quantity'] . ' × ' . $c_title );
					?>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endif; ?>

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

		<label class="wordpify-product-single__qty-label" for="wordpify-qty-<?php echo esc_attr( $product->ID ); ?>"><?php esc_html_e( 'Quantity', 'wordpify' ); ?></label>
		<div class="wordpify-product-single__purchase">
			<div class="wordpify-qty-stepper">
				<button type="button" class="wordpify-qty-stepper__btn" data-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'wordpify' ); ?>">&minus;</button>
				<input type="number" id="wordpify-qty-<?php echo esc_attr( $product->ID ); ?>" class="wordpify-qty-input" value="1" min="1" max="999" step="1" inputmode="numeric" />
				<button type="button" class="wordpify-qty-stepper__btn" data-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'wordpify' ); ?>">+</button>
			</div>
			<button class="wordpify-btn wordpify-btn--primary wordpify-add-to-cart"
				data-variant-id="<?php echo esc_attr( $variants[0]['id'] ?? '' ); ?>"
				data-product-id="<?php echo esc_attr( $product->ID ); ?>"
				data-quantity="1">
				<?php esc_html_e( 'Add to Cart', 'wordpify' ); ?>
			</button>
		</div>
	</div>
</div>
