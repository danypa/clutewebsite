<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.3.0
 */

if (!defined('ABSPATH')) {
	exit;
}

//Get admin options
$options = get_option('arg-mc-options');

if (empty($options)) :
    $options = array();
endif;

$showImage = !empty($options['show_product_image']) ? true : false;

?>

<div class="woocommerce-checkout-review-order-table">

<style>
.shadow1 {
  -webkit-box-shadow: 1px 1px 3px 3px #eee;  /* Safari 3-4, iOS 4.0.2 - 4.2, Android 2.3+ */
  -moz-box-shadow:    1px 1px 3px 3px #eee;  /* Firefox 3.5 - 3.6 */
  box-shadow:         1px 1px 3px 3px #eee;  /* Opera 10.5, IE 9, Firefox 4+, Chrome 6+, iOS 5 */
  
}
.contentOrderReview{
	padding:20px;

   font-size:13px
}
</style>

<div class="shadow1">
<div class="contentOrderReview"> 

<?php
  $numArticulos = 0;
		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

				$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

							if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
					$numArticulos = $numArticulos+$cart_item['quantity'];
			}

		}


		?>


	<table style="line-height:normal;"><tr>
			<td style="padding-right:10px"><strong>CARRITO: </strong></td>
			<td style="color: red;"><?php wc_cart_totals_order_total_html(); ?></td>
		</tr>
<tr><td colspan="2" style="color:#666"> 
		<?php echo $numArticulos; ?> artículo(s)
</td></tr>
		</table>

<div style="padding-top:10px"></div>
<table class="shop_table argorder-show-image" style="border:0">
	
	<tbody>
		<?php
		do_action('woocommerce_review_order_before_cart_contents');

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
			$_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

			if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
				?>
				<tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
					<th class="product-name" style="background-color:white; border:0">
						<div class="arg-product-image">
							<?php echo $showImage === true ? apply_filters('woocommerce_in_cart_product_thumbnail', $_product->get_image(), $cart_item, $cart_item_key) : ''; ?>
						</div>
						
					</th>
					<td class="product-total" style="border:0">

<div class="arg-product-desc">	
							<?php echo apply_filters('woocommerce_cart_item_name', ' <strong class="product-quantity">'. $_product->get_name()  . '</strong>', $cart_item, $cart_item_key) . '&nbsp;'; ?> <?php echo apply_filters('woocommerce_checkout_cart_item_quantity', ' <strong class="product-quantity">' . sprintf('&times; %s', $cart_item['quantity']) . '</strong>', $cart_item, $cart_item_key); ?>

							<div class="arg-product-qwt">
								<?php echo wc_get_formatted_cart_item_data($cart_item); ?>
							</div>
						</div>


						<?php /*echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);*/ ?>
					</td>
				</tr>
<tr><td colspan="2" style="border-bottom: 1px solid #ddd;">  </td></tr>
				<?php
			}
		}

		do_action('woocommerce_review_order_after_cart_contents');
		?>
	</tbody>
	<tfoot>

		<!--<tr class="cart-subtotal">
			<th><?php _e('Subtotal', 'woocommerce'); ?></th>
			<td><?php wc_cart_totals_subtotal_html(); ?></td>
		</tr>-->

		<?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title($code)); ?>">
				<th><?php wc_cart_totals_coupon_label($coupon); ?></th>
				<td><?php wc_cart_totals_coupon_html($coupon); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>

			<?php //do_action('woocommerce_review_order_before_shipping'); ?>

			<?php //wc_cart_totals_shipping_html(); ?>

			<?php //do_action('woocommerce_review_order_after_shipping'); ?>

		<?php endif; ?>

		<?php foreach (WC()->cart->get_fees() as $fee) : ?>
			<tr class="fee">
				<th><?php echo esc_html($fee->name); ?></th>
				<td><?php wc_cart_totals_fee_html($fee); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
			<?php if ('itemized' === get_option( 'woocommerce_tax_total_display')) : ?>
				<?php foreach (WC()->cart->get_tax_totals() as $code => $tax) : ?>
					<tr class="tax-rate tax-rate-<?php echo sanitize_title($code); ?>">
						<th><?php echo esc_html($tax->label); ?></th>
						<td><?php echo wp_kses_post($tax->formatted_amount); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		

	</tfoot>
</table>
</div>
</div>

</div>
