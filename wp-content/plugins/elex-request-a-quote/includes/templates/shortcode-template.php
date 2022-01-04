<?php
/**
 *
 * File to implement shortcode.
 *
 * @package Elex Request a Quote
 */

// to check whether accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings = get_option( 'elex_request_a_quote_settings' );

if ( ! empty( $cart_items ) ) {
	$form_fields = get_option( 'elex_request_a_quote_field_adjustment_options' );
	?>
		<style>

			td.eraq_remove_icon  {
				width: 16px;
				padding: 0 16px;
				cursor: pointer;
				background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAA3NCSVQICAjb4U/gAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAAAZdEVYdFNvZnR3YXJlAHd3dy5pbmtzY2FwZS5vcmeb7jwaAAAB70lEQVQ4T61UPUhbURT+7n0vLxE1vmqFEBTR6lS7uHaTYpfqopsu0qkgODh0EURwadFBEJy62a3t0BbBIm5dXdTJP/whiFr7EpUmL3m5nnNixR80EfLBXe79vu+ce865VxkCbsHf2Ud6eQWZzS1k1tZlL/y8DeFnLYh0vIDT1CB713HDKPBS8D5/xemPX8hns1COA2VbcmZyAYzvQ4dCqO7ugtvfC8uNyhnjyiibOMDByDhyngdFZKW1EG7D5PMwFMR2XcSmxhCKx2RfjIJkCol375E7IZMwZaGUHN4Hjm0yPuxaF/HZD7BqopCw3twXBH9LM2Ewh7msYS1D+zt7OP25CNh0HdqQaCUsCUca1rKHTi+vIk9FVFrR/YmUTsP8K7KYQ1zWsJY91OHHGXO29Fu6Y7k1iPa8ptwlNY55F3x1Okp9X6AuJ6WbVZ0voXYHh01w9AegbjitzYhPT1wqHkZieBT+xjYVR8OqrysUuxwo39WS3+bN8cwnWFWVhWL7GSE+CPJSTliKHZyd4+nQW+hIRzs0PYX/XVCRCFRFkcWcyy6ztuDR1IjqN6+AXFYSkWErYUnSpGEte0ix3YE+WE9cGXsetmKQoSQua1jLECN+K7HJMdhsRgPGD/M+yKMlDnNZw1pG+b+R63j8xwZcADXJQNHUd268AAAAAElFTkSuQmCC) no-repeat center
			}
			span.eraq_remove_icon  {
				cursor: pointer;
			}

			.eraq_product_image img{
				display: inline-block;
				max-width: 120px;
			}

			.eraq_product_quantity{
				padding: auto;
			}
			input[type='number'].eraq_product_quantity_input_value{
				display: inline-block;
				width: 80px;
				height: 50px;
			}

			/** Responsive */
			@media(max-width: 360px){
				table thead{
					display: none;
				}

				table, table tbody, table tr, table td {
					display: block;
					width: 100%;
				}

				.empty_cell {
					display: none;
				}

				.eraq_product_image img{
					max-width: 120px;
					display:block;
					margin-left: auto;
					margin-right:auto;
				}

				table tr {
					margin-bottom: 15px;
				}

				table td {
					text-align: right;
					padding-left: 50%;
					position: relative;
				}

				tr td:nth-of-type(1),tr td:nth-of-type(2){
					border: none;
				}

				tr td:nth-of-type(2){
					padding: 10px;
				}

				table td:before {
					content: attr(data-label);
					position: absolute;
					left:0;
					width: 50%;
					text-align: left;
					font-weight: bold;
				}
			}		
			</style>

		<table id='eraq_quote_list_table'>
		<?php
		foreach ( $cart_items as $key => $value ) {
			$product = wc_get_product( $value['product_id'] );
			if ( $product instanceof WC_Product ) {

				$price=$product->get_price();
			}
		}
		
		if (empty($price)) {
			?>
			<thead>
				<th class="quoted_product_remove_icon"></th>
				<th class="quoted_product_image"></th>
				<th class="quoted_product_title"><?php esc_html_e( 'Product', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th class="quoted_product_quantity"><?php esc_html_e( 'Quantity', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
			</thead>
			<?php
		} else {
			?>
			<thead>
				<th class="quoted_product_remove_icon"></th>
				<th class="quoted_product_image"></th>
				<th class="quoted_product_title"><?php esc_html_e( 'Product', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th class="quoted_product_price"><?php esc_html_e( 'Price', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th class="quoted_product_quantity"><?php esc_html_e( 'Quantity', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th class="quoted_product_subtotal"><?php esc_html_e( 'Subtotal', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
			</thead>
			<?php
		}
		?>
			<tbody id="elex_raq_cart_table_body">
	<?php
	foreach ( $cart_items as $key => $value ) {
		if ( ! empty( $value['variation_id'] ) && ! empty( $value['product_id'] ) && ! empty( $value['Qty'] ) ) { // It's a Variation.
			$variation = new WC_Product_Variation( $value['variation_id'] );
			if ( ! empty( $variation ) ) {
				if (empty($price)) {
				echo "
				<tr class='eraq_quote_items' data-pid=" . esc_attr( $value['product_id'] ) . ' data-vid=' . esc_attr( $value['variation_id'] ) . ">
					<td class='eraq_remove_icon'></td>
					<td class='eraq_product_image'>" . wp_kses_post( $variation->get_image() ) . "</td>
					<td class='eraq_product_name'>
						<a href=" . esc_attr( get_permalink( $value['product_id'] ) ) . '>' . wp_kses_post( $variation->get_formatted_name() ) . "</a>
					</td>
					<td class='eraq_product_quantity'>" .
						"<input type='number' class='eraq_product_quantity_input_value' min='1' value=" . esc_attr( $value['Qty'] ) . '>' .
					'</td>
				</tr>';
				} else {
					echo "
				<tr class='eraq_quote_items' data-pid=" . esc_attr( $value['product_id'] ) . ' data-vid=' . esc_attr( $value['variation_id'] ) . ">
					<td class='eraq_remove_icon'></td>
					<td class='eraq_product_image'>" . wp_kses_post( $variation->get_image() ) . "</td>
					<td class='eraq_product_name'>
						<a href=" . esc_attr( get_permalink( $value['product_id'] ) ) . '>' . wp_kses_post( $variation->get_formatted_name() ) . "</a>
					</td>
					<td class='eraq_product_price'>
						" . esc_attr( get_woocommerce_currency_symbol() ) . '<span>' . esc_attr( $variation->get_price() ) . "</span>
					</td>
					<td class='eraq_product_quantity'>" .
						"<input type='number' class='eraq_product_quantity_input_value' min='1' value=" . esc_attr( $value['Qty'] ) . '>' .
					"</td>
					<td class='eraq_product_subtotal'>" . esc_attr( get_woocommerce_currency_symbol() ) . '<span><span></td>
				</tr>';
					
				}
			}
		} elseif ( ! empty( $value['product_id'] ) && ! empty( $value['Qty'] ) ) {
			// Its a simple/external product.
			$product = wc_get_product( $value['product_id'] );
			if ( ! empty( $product ) ) { // to check if product is removed from store.
				if (empty($price)) {
				echo "
				<tr class='eraq_quote_items' data-pid=" . esc_attr( $value['product_id'] ) . ">
					<td class='eraq_remove_icon'></td>
					<td class='eraq_product_image'>" . wp_kses_post( $product->get_image() ) . "</td>
					<td class='eraq_product_name'>
						<a href=" . esc_attr( get_permalink( $value['product_id'] ) ) . '>' . esc_attr( $product->get_name() ) . "</a>
					</td>
					<td class='eraq_product_quantity'>
						<input type='number' class='eraq_product_quantity_input_value' min='1' value=" . esc_attr( $value['Qty'] ) . '>
					</td>
				</tr>';
				} else {
					echo "
				<tr class='eraq_quote_items' data-pid=" . esc_attr( $value['product_id'] ) . ">
					<td class='eraq_remove_icon'></td>
					<td class='eraq_product_image'>" . wp_kses_post( $product->get_image() ) . "</td>
					<td class='eraq_product_name'>
						<a href=" . esc_attr( get_permalink( $value['product_id'] ) ) . '>' . esc_attr( $product->get_name() ) . "</a>
					</td>
					<td class='eraq_product_price'>" . esc_attr( get_woocommerce_currency_symbol() ) . '<span>' . esc_attr( $product->get_price() ) . "</span>
					</td>
					<td class='eraq_product_quantity'>
						<input type='number' class='eraq_product_quantity_input_value' min='1' value=" . esc_attr( $value['Qty'] ) . ">
					</td>
					<td class='eraq_product_subtotal'>" . esc_attr( get_woocommerce_currency_symbol() ) . '<span></span></td>
				</tr>';
				}
			}
		}
	}
	if (!empty($price)) {
	echo "
	<tr>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><strong>Total:</strong></td>
		<td class='eraq_product_total'>" . esc_attr( get_woocommerce_currency_symbol() ) . '<span></span></td>
	</tr>';
	}
	?>
	</tbody>
	</table> 
	<h3><center><?php esc_html_e( 'Fill Your Details', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?><center></h3>
	<form method="GET" id="quote_details_form">
	<?php
	if ( ! empty( $form_fields ) ) {

		foreach ( $form_fields as $keys => $values ) {
			$is_required = false;
			if ( isset( $values['input_is_required'] ) && 'on' === $values['input_is_required'] ) {
				$is_required = true;
			}
			if ( in_array( $values['input_type'], array( 'text', 'email', 'date', 'url', 'number', 'color' ), true ) ) {
				if ( $is_required ) {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . "</strong><abbr style='color:#b22222; text-decoration:none; title='required'>*</abbr></p>";
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' type=" . esc_attr( $values['input_type'] ) . " placeholder='" . esc_attr( $values['input_placeholder'] ) . "' required></p>";
				} else {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . ' (Optional)</strong></p>';
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' type=" . esc_attr( $values['input_type'] ) . " placeholder='" . esc_attr( $values['input_placeholder'] ) . "'></p>";
				}
			} elseif ( 'tel' === $values['input_type'] ) {
				if ( $is_required ) {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . "</strong><abbr style='color:#b22222; text-decoration:none;' title='required'>*</abbr></p>";
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' pattern='[0-9]*' type=" . esc_attr( $values['input_type'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '" required></p>';
				} else {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . ' (Optional)</strong></p>';
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' pattern='[0-9]*' type=" . esc_attr( $values['input_type'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '"></p>';
				}
			} elseif ( 'textarea' === $values['input_type'] ) {
				if ( $is_required ) {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . "</strong><abbr style='color:#b22222; text-decoration:none; title='required'>*</abbr></p>";
					echo '<p><textarea id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '" required></textarea></p>';
				} else {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . ' (Optional)</strong></p>';
					echo '<p><textarea id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '"></textarea></p>';
				}
			} elseif ( 'url' === $values['input_type'] ) {
				if ( $is_required ) {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . "</strong><abbr style='color:#b22222; text-decoration:none; title='required'>*</abbr></p>";
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' pattern='https://.*' type=" . esc_attr( $values['input_type'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '" required></p>';
				} else {
					echo '<p><strong>' . esc_attr( $values['input_label'] ) . ' (Optional)</strong></p>';
					echo '<p><input id=' . esc_attr( $values['input_connect_field_to'] ) . ' name=' . esc_attr( $values['input_connect_field_to'] ) . " style='width: 100%;' pattern='https://.*' type=" . esc_attr( $values['input_type'] ) . ' placeholder="' . esc_attr( $values['input_placeholder'] ) . '"></p>';
				}
			}
		}
		?>
		<p class="form-row form-row-wide">
			<input class="button eraq-send-request" type="submit">
		</p>
	</form>
		<?php
	}
} else {
	?>
	<h3><?php esc_html_e( 'Your Cart is Currently Empty', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></h3>
	<?php
}
