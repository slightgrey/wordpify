<?php
/**
 * Template: Address Management
 *
 * @var array $addresses  List of Shopify address objects.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wordpify-addresses">
	<h3><?php esc_html_e( 'My Addresses', 'wordpify' ); ?></h3>

	<div class="wordpify-address-list">
		<?php if ( empty( $addresses ) ) : ?>
		<p><?php esc_html_e( 'No addresses saved yet.', 'wordpify' ); ?></p>
		<?php else : ?>
		<?php foreach ( $addresses as $addr ) : ?>
		<div class="wordpify-address-card" data-address-id="<?php echo esc_attr( $addr['id'] ); ?>">
			<p><?php echo esc_html( trim( ( $addr['firstName'] ?? '' ) . ' ' . ( $addr['lastName'] ?? '' ) ) ); ?></p>
			<p><?php echo esc_html( $addr['address1'] ?? '' ); ?><?php echo $addr['address2'] ? ', ' . esc_html( $addr['address2'] ) : ''; ?></p>
			<p><?php echo esc_html( implode( ', ', array_filter( [ $addr['city'] ?? '', $addr['province'] ?? '', $addr['zip'] ?? '', $addr['country'] ?? '' ] ) ) ); ?></p>
			<div class="wordpify-address-card__actions">
				<button class="wordpify-btn wordpify-btn--secondary wordpify-address-edit"
					data-address-id="<?php echo esc_attr( $addr['id'] ); ?>">
					<?php esc_html_e( 'Edit', 'wordpify' ); ?>
				</button>
				<button class="wordpify-btn wordpify-btn--danger wordpify-address-delete"
					data-address-id="<?php echo esc_attr( $addr['id'] ); ?>">
					<?php esc_html_e( 'Delete', 'wordpify' ); ?>
				</button>
			</div>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>

	<button class="wordpify-btn wordpify-btn--primary" id="wordpify-add-address">
		<?php esc_html_e( '+ Add Address', 'wordpify' ); ?>
	</button>

	<div class="wordpify-form wordpify-address-form" id="wordpify-address-form" hidden>
		<?php wp_nonce_field( 'wp_rest', '_wpnonce' ); ?>
		<input type="hidden" name="address_id" id="wordpify-address-id" value="" />
		<?php foreach ( [ 'firstName' => 'First Name', 'lastName' => 'Last Name', 'address1' => 'Address 1', 'address2' => 'Address 2', 'city' => 'City', 'province' => 'Province/State', 'zip' => 'ZIP/Postal Code', 'country' => 'Country', 'phone' => 'Phone' ] as $field => $label ) : ?>
		<div class="wordpify-form__row">
			<label for="wordpify-addr-<?php echo esc_attr( $field ); ?>"><?php echo esc_html( $label ); ?></label>
			<input type="text" id="wordpify-addr-<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( $field ); ?>" />
		</div>
		<?php endforeach; ?>
		<div class="wordpify-form__actions">
			<button type="button" class="wordpify-btn wordpify-btn--primary" id="wordpify-address-save"><?php esc_html_e( 'Save Address', 'wordpify' ); ?></button>
			<button type="button" class="wordpify-btn wordpify-btn--secondary" id="wordpify-address-cancel"><?php esc_html_e( 'Cancel', 'wordpify' ); ?></button>
		</div>
		<div class="wordpify-form__message" id="wordpify-address-message" aria-live="polite"></div>
	</div>
</div>
