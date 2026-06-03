<?php
/**
 * Template: Checkout (iframe)
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wordpify-checkout-wrap" id="wordpify-checkout-wrap">
	<p class="wordpify-checkout-wrap__loading" id="wordpify-checkout-loading">
		<?php esc_html_e( 'Loading checkout…', 'wordpify' ); ?>
	</p>
	<iframe
		id="wordpify-checkout-iframe"
		class="wordpify-checkout-iframe"
		allow="payment"
		style="display:none"
		title="<?php esc_attr_e( 'Checkout', 'wordpify' ); ?>"
	></iframe>
</div>
