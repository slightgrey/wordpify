<?php
/**
 * Template: Full Cart
 *
 * @var array $cart  Shopify cart object.
 */
defined( 'ABSPATH' ) || exit;
$lines = $cart['lines']['nodes'] ?? [];
$total = $cart['cost']['totalAmount'] ?? [];

$currency_symbols = [
	'USD' => '$', 'AUD' => '$', 'CAD' => '$', 'SGD' => '$', 'HKD' => '$', 'MXN' => '$',
	'EUR' => '€',
	'GBP' => '£',
	'JPY' => '¥',
	'NZD' => '$',
];
$shop = (array) get_option( 'wordpify_shop_settings', [] ) + [
	'gst_cart_enabled'        => true,
	'gst_text'                => '+ GST',
	'checkout_notice_enabled' => true,
	'checkout_notice'         => 'Shipping calculated at checkout',
];
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
				<?php $variant_title = $merch['title'] ?? ''; ?>
				<?php if ( $variant_title && $variant_title !== 'Default Title' ) : ?>
				<p class="wordpify-cart-item__variant"><?php echo esc_html( $variant_title ); ?></p>
				<?php endif; ?>
				<p class="wordpify-cart-item__price">
					<?php
					$item_currency = strtoupper( $merch['price']['currencyCode'] ?? 'USD' );
					$item_symbol   = $currency_symbols[ $item_currency ] ?? ( $item_currency . ' ' );
					$item_price    = $item_symbol . number_format( (float) ( $merch['price']['amount'] ?? 0 ), 2 ) . ' ' . $item_currency;
					echo esc_html( $item_price );
					?>
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
		<strong>
			<?php
			$total_currency = strtoupper( $total['currencyCode'] ?? 'USD' );
			$total_symbol   = $currency_symbols[ $total_currency ] ?? ( $total_currency . ' ' );
			$total_price    = $total_symbol . number_format( (float) ( $total['amount'] ?? 0 ), 2 ) . ' ' . $total_currency;
			echo esc_html( $total_price );
			?><?php if ( ! empty( $shop['gst_cart_enabled'] ) ) : ?> <small><?php echo esc_html( $shop['gst_text'] ); ?></small><?php endif; ?>
		</strong>
	</div>
	<?php if ( ! empty( $shop['checkout_notice_enabled'] ) && ! empty( $shop['checkout_notice'] ) ) : ?>
	<p class="wordpify-cart__shipping-note" style="text-align:right;"><?php echo esc_html( $shop['checkout_notice'] ); ?></p>
	<?php endif; ?>

	<a href="<?php echo esc_url( home_url( '/checkout/' ) ); ?>" class="wordpify-btn wordpify-btn--primary wordpify-checkout-btn">
		<?php esc_html_e( 'Proceed to Checkout', 'wordpify' ); ?>
	</a>
	<?php endif; ?>
</div>
