<?php
/**
 * Template: Mini Cart
 *
 * @var int    $item_count
 * @var bool   $show_count
 * @var string $cart_url
 */
defined( 'ABSPATH' ) || exit;

$badge = $show_count
	? '<span class="wordpify-mini-cart__count" id="wordpify-cart-count">' . esc_html( $item_count ) . '</span>'
	: '';

$icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
	<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/>
	<path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
</svg>';
?>
<div class="wordpify-mini-cart" id="wordpify-mini-cart">
	<?php if ( $cart_url ) : ?>
	<a href="<?php echo esc_url( $cart_url ); ?>" class="wordpify-mini-cart__trigger" id="wordpify-mini-cart-trigger" aria-label="<?php esc_attr_e( 'Cart', 'wordpify' ); ?>">
		<?php echo $icon . $badge; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</a>
	<?php else : ?>
	<button class="wordpify-mini-cart__trigger" id="wordpify-mini-cart-trigger" aria-label="<?php esc_attr_e( 'Cart', 'wordpify' ); ?>">
		<?php echo $icon . $badge; // phpcs:ignore WordPress.Security.EscapeOutput ?>
	</button>
	<?php endif; ?>
</div>
