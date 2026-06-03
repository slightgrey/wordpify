<?php
/**
 * Template: Full Cart
 *
 * @var array $cart  Shopify cart object.
 */
defined( 'ABSPATH' ) || exit;
$lines = $cart['lines']['nodes'] ?? [];
$total = $cart['cost']['totalAmount'] ?? [];
?>
<div class="wordpify-cart" id="wordpify-cart">
	<h3><?php esc_html_e( 'Your Cart', 'wordpify' ); ?></h3>

	<?php if ( empty( $lines ) ) : ?>
	<p class="wordpify-cart__empty"><?php esc_html_e( 'Your cart is empty.', 'wordpify' ); ?></p>
	<?php else : ?>
	<div class="wordpify-cart__items" id="wordpify-cart-items">
		<?php foreach ( $lines as $line ) :
			$merch = $line['merchandise'] ?? [];
			$product = $merch['product'] ?? [];
			$image   = $merch['image'] ?? [];
		?>
		<div class="wordpify-cart-item" data-line-id="<?php echo esc_attr( $line['id'] ); ?>">
			<?php if ( ! empty( $image['url'] ) ) : ?>
			<img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $product['title'] ?? '' ); ?>" />
			<?php endif; ?>
			<div class="wordpify-cart-item__details">
				<p class="wordpify-cart-item__name"><?php echo esc_html( $product['title'] ?? '' ); ?></p>
				<p class="wordpify-cart-item__variant"><?php echo esc_html( $merch['title'] ?? '' ); ?></p>
				<p class="wordpify-cart-item__price">
					<?php echo esc_html( ( $merch['price']['amount'] ?? '' ) . ' ' . ( $merch['price']['currencyCode'] ?? '' ) ); ?>
				</p>
			</div>
			<div class="wordpify-cart-item__qty">
				<button class="wordpify-qty-btn wordpify-qty-minus" data-line-id="<?php echo esc_attr( $line['id'] ); ?>" data-quantity="<?php echo esc_attr( max( 0, $line['quantity'] - 1 ) ); ?>">−</button>
				<span><?php echo esc_html( $line['quantity'] ); ?></span>
				<button class="wordpify-qty-btn wordpify-qty-plus" data-line-id="<?php echo esc_attr( $line['id'] ); ?>" data-quantity="<?php echo esc_attr( $line['quantity'] + 1 ); ?>">+</button>
			</div>
			<button class="wordpify-cart-item__remove" data-line-id="<?php echo esc_attr( $line['id'] ); ?>">&times;</button>
		</div>
		<?php endforeach; ?>
	</div>

	<div class="wordpify-cart__totals">
		<span><?php esc_html_e( 'Total:', 'wordpify' ); ?></span>
		<strong><?php echo esc_html( ( $total['amount'] ?? '' ) . ' ' . ( $total['currencyCode'] ?? '' ) ); ?></strong>
	</div>

	<a href="<?php echo esc_url( home_url( '/checkout/' ) ); ?>" class="wordpify-btn wordpify-btn--primary wordpify-checkout-btn">
		<?php esc_html_e( 'Proceed to Checkout', 'wordpify' ); ?>
	</a>
	<?php endif; ?>
</div>
