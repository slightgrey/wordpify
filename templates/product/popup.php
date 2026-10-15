<?php
/**
 * Template: Product Popup (grid + [wordpify_product] on the same page)
 *
 * @var string $content Rendered single product HTML, or '' when no product is requested.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="wordpify-modal" id="wordpify-product-modal" <?php echo $content === '' ? 'hidden' : ''; ?>>
	<div class="wordpify-modal__backdrop" data-wordpify-close></div>
	<div class="wordpify-modal__dialog" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Product details', 'wordpify' ); ?>">
		<button type="button" class="wordpify-modal__close" data-wordpify-close aria-label="<?php esc_attr_e( 'Close', 'wordpify' ); ?>">&times;</button>
		<div class="wordpify-modal__body">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in single.php. ?>
		</div>
	</div>
</div>
