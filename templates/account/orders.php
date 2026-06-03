<?php
/**
 * Template: Order History
 *
 * @var array $orders  List of Shopify order objects.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wordpify-orders">
	<h3><?php esc_html_e( 'Order History', 'wordpify' ); ?></h3>
	<?php if ( empty( $orders ) ) : ?>
	<p><?php esc_html_e( 'No orders found.', 'wordpify' ); ?></p>
	<?php else : ?>
	<table class="wordpify-orders__table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'Order', 'wordpify' ); ?></th>
				<th><?php esc_html_e( 'Date', 'wordpify' ); ?></th>
				<th><?php esc_html_e( 'Status', 'wordpify' ); ?></th>
				<th><?php esc_html_e( 'Total', 'wordpify' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $orders as $order ) : ?>
			<tr>
				<td>#<?php echo esc_html( $order['orderNumber'] ?? '' ); ?></td>
				<td><?php echo esc_html( isset( $order['processedAt'] ) ? date_i18n( get_option( 'date_format' ), strtotime( $order['processedAt'] ) ) : '' ); ?></td>
				<td>
					<span class="wordpify-order-status wordpify-order-status--<?php echo esc_attr( strtolower( $order['financialStatus'] ?? '' ) ); ?>">
						<?php echo esc_html( ucfirst( strtolower( $order['financialStatus'] ?? '' ) ) ); ?>
					</span>
				</td>
				<td>
					<?php
					$total = $order['currentTotalPrice'] ?? [];
					echo esc_html( ( $total['amount'] ?? '' ) . ' ' . ( $total['currencyCode'] ?? '' ) );
					?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
</div>
