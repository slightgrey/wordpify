<?php
/**
 * Template: Account Details
 *
 * @var array $customer  Shopify customer data.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wordpify-account-details">
	<h3><?php esc_html_e( 'My Account', 'wordpify' ); ?></h3>
	<form class="wordpify-form" id="wordpify-account-form">
		<?php wp_nonce_field( 'wp_rest', '_wpnonce' ); ?>
		<div class="wordpify-form__row">
			<label for="wordpify-first-name"><?php esc_html_e( 'First Name', 'wordpify' ); ?></label>
			<input type="text" id="wordpify-first-name" name="firstName" value="<?php echo esc_attr( $customer['firstName'] ?? '' ); ?>" />
		</div>
		<div class="wordpify-form__row">
			<label for="wordpify-last-name"><?php esc_html_e( 'Last Name', 'wordpify' ); ?></label>
			<input type="text" id="wordpify-last-name" name="lastName" value="<?php echo esc_attr( $customer['lastName'] ?? '' ); ?>" />
		</div>
		<div class="wordpify-form__row">
			<label for="wordpify-email"><?php esc_html_e( 'Email', 'wordpify' ); ?></label>
			<input type="email" id="wordpify-email" name="email" value="<?php echo esc_attr( $customer['email'] ?? '' ); ?>" />
		</div>
		<div class="wordpify-form__row">
			<label for="wordpify-phone"><?php esc_html_e( 'Phone', 'wordpify' ); ?></label>
			<input type="tel" id="wordpify-phone" name="phone" value="<?php echo esc_attr( $customer['phone'] ?? '' ); ?>" />
		</div>
		<div class="wordpify-form__message" id="wordpify-account-message" aria-live="polite"></div>
		<button type="submit" class="wordpify-btn wordpify-btn--primary"><?php esc_html_e( 'Save Changes', 'wordpify' ); ?></button>
	</form>
</div>
