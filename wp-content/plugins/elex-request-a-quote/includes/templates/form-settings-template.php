<?php
/**
 *
 * Form Settings.
 *
 * @package Elex Request a Quote
 */

// To check whether accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$nonce = wp_create_nonce( 'form_data' );
?>
<p><?php esc_html_e( 'You can customize your Request a Quote form here. Click the Add Field button to add a new field. You can choose type of field from the dropdown. You can also specify whether the field is required or optional. Please Note: It is recommended that email field should be made required and also please ensure the correct field type is chosen for fields.', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></p>
<tr valign="top" >
	<td class="forminp" colspan="2" style="padding-left:0px">
		<table class="fields_adjustment widefat" id="elex_request_a_quote_field_adjustment_options">
		<input type="hidden" name="nounce_verify" value="<?php echo esc_attr($nonce); ?>">
			<thead>
				<th class="sort">&nbsp;</th>
				<th><?php esc_html_e( 'Label', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th><?php esc_html_e( 'Type', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th><?php esc_html_e( 'Placeholder', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th><?php esc_html_e( 'Connected To', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th><?php esc_html_e( 'Required', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
				<th><?php esc_html_e( 'Remove', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></th>
			</thead>
			<tbody id='elex_raq_table_body'>
				<?php
				$default_form_fields     = array(
					0 => array(
						'input_label'            => 'First Name',
						'input_type'             => 'text',
						'input_placeholder'      => 'Add your first name.',
						'input_connect_field_to' => 'billing_first_name',
						'input_is_required'      => 'on',
					),
					1 => array(
						'input_label'            => 'Last Name',
						'input_type'             => 'text',
						'input_placeholder'      => 'Add your surname.',
						'input_connect_field_to' => 'billing_last_name',
						'input_is_required'      => 'on',
					),
					2 => array(
						'input_label'            => 'Email',
						'input_type'             => 'email',
						'input_placeholder'      => 'Add your email.',
						'input_connect_field_to' => 'billing_email',
						'input_is_required'      => 'on',
					),
					3 => array(
						'input_label'            => 'Phone',
						'input_type'             => 'tel',
						'input_placeholder'      => '',
						'input_connect_field_to' => 'billing_phone',
						'input_is_required'      => 'on',
					),
					4 => array(
						'input_label'            => 'Message',
						'input_type'             => 'textarea',
						'input_placeholder'      => 'Leave your message here.',
						'input_connect_field_to' => 'order_comments',
					),
				);
				$form_adjustment_fields  = ! empty( get_option( 'elex_request_a_quote_field_adjustment_options' ) ) ? get_option( 'elex_request_a_quote_field_adjustment_options' ) : $default_form_fields;
				$field_conected_to_array = array( '', 'order_comments', 'billing_first_name', 'billing_last_name', 'billing_company', 'billing_country', 'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state', 'billing_postcode', 'billing_phone', 'billing_email' );
				$field_type_array        = array( '', 'text', 'textarea', 'email', 'date', 'tel', 'url', 'number', 'color' );
				if ( ! empty( $form_adjustment_fields ) ) {
					foreach ( $form_adjustment_fields as $key => $value ) {

						$check_var = '';
						if ( ! empty( $value['input_is_required'] ) && 'on' === $value['input_is_required'] ) {
							$check_var = 'checked';
						}

						echo "<tr>
                            <td style='width: 5%;' class='sort'>
                                <input type='hidden' class='order' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . "]' value='" . esc_attr( $key ) . "' />
                            </td>
                            <td style='width: 20%;'>
                                <input type='text' class='eraq_input_label' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . "][input_label]' value='" . esc_attr( $value['input_label'] ) . "' required>
                            </td>
                            <td style='width: 20%;'>
                                <select class='eraq_input_type' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . "][input_type]' style='width: 100%;' required>";

						foreach ( $field_type_array as $k => $v ) {
							if ( $value['input_type'] === $v ) {
								echo '<option value=' . esc_attr( $v ) . ' selected>' . esc_attr( $v ) . '</option>';
							} else {
								echo '<option value=' . esc_attr( $v ) . '>' . esc_attr( $v ) . '</option>';
							}
						}

						echo "</select>
                            </td>
                            <td style='width: 20%;'>
                                <input class='eraq_input_placeholder' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . "][input_placeholder]' type='text' value='" . esc_attr( isset($value['input_placeholder'])? $value['input_placeholder'] : '' ) . "'>
                            </td>
                            <td style='width: 20%;'>
                                <select class='eraq_input_connect_field_to' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . "][input_connect_field_to]' style='width: 100%;' required>";


						foreach ( $field_conected_to_array as $k => $v ) {
							if ( $value['input_connect_field_to'] === $v ) {
								echo '<option value=' . esc_attr( $v ) . ' selected>' . esc_attr( $v ) . '</option>';
							} else {
								echo '<option value=' . esc_attr( $v ) . '>' . esc_attr( $v ) . '</option>';
							}
						}

						echo "</select>
								</td>
                                <td style='width: 5%;'><input type='checkbox' class='eraq_input_is_required' name='elex_request_a_quote_field_adjustment_options[" . esc_attr( $key ) . '][input_is_required]\'' . esc_attr( $check_var ) . "/></td>
								<td class='remove_icon' style='text-align:center; width: 5%;'>
                                </td>
                            </tr>";

					}
				}
				?>

			</tbody>
			<tr>
				<td></td>
				<td>
					<br>
					<button type="button" id="elex_raq_add_field"  ><?php esc_html_e( 'Add Field', 'ELEX_RAQ_PLUGIN_DOMAIN' ); ?></button>
				</td>
			</tr>
		</table>
	</td>
</tr>
<script type="text/javascript">
	jQuery("#elex_raq_table_body").on('click', '.remove_icon', function () {
		jQuery(this).closest("tr").remove();
	});
	jQuery('#elex_raq_add_field').click( function() {
		var tbody = jQuery('.fields_adjustment').find('tbody');
		var size = tbody.find('tr').size();
		var code = '<tr >\
					<td style="width: 5%;" class="sort"><input type="hidden" class="order" name="elex_request_a_quote_field_adjustment_options['+size+']"/></td>\
					<td style="width: 20%;"><input type="text" class="eraq_input_label" name="elex_request_a_quote_field_adjustment_options['+size+'][input_label]" required></td>\
					<td style="width: 20%;"><select class="eraq_input_type" name="elex_request_a_quote_field_adjustment_options['+size+'][input_type]" style="width: 100%;" required><option value=""></option><option value="text">text</option><option value="textarea">textarea</option><option value="email">email</option><option value="date">date</option><option value="tel">tel</option><option value="url">url</option><option value="number">number</option><option value="color">color</option></select></td>\
					<td style="width: 20%;"><input class="eraq_input_placeholder" name="elex_request_a_quote_field_adjustment_options['+size+'][input_placeholder]" type="text"></td>\
					<td style="width: 20%;"><select class="eraq_input_connect_field_to" name="elex_request_a_quote_field_adjustment_options['+size+'][input_connect_field_to]" style="width: 100%;" required><option value=""></option><option value="order_comments">order_comments</option><option value="billing_first_name">billing_first_name</option><option value="billing_last_name">billing_last_name</option><option value="billing_company">billing_company</option><option value="billing_country">billing_country</option><option value="billing_address_1">billing_address_1</option><option value="billing_address_2">billing_address_2</option><option value="billing_city">billing_city</option><option value="billing_state">billing_state</option><option value="billing_postcode">billing_postcode</option><option value="billing_phone">billing_phone</option><option value="billing_email">billing_email</option></select></td>\
					<td style="width: 5%;"><input class="eraq_input_is_required" name="elex_request_a_quote_field_adjustment_options['+size+'][input_is_required]" type="checkbox"></td>\
					<td class="remove_icon" style="text-align:center; width: 5%;"></td>\
					</tr>';
					jQuery('#elex_raq_table_body').append( code );
					return false;
	});
</script>

<style type="text/css">
	.fields_adjustment td {
		vertical-align: middle;
		padding: 4px 7px;
	}
	.fields_adjustment th {
		padding: 9px 7px;
	}
	.fields_adjustment td input {
		margin-right: 4px;
	}
	.fields_adjustment .check-column {
		vertical-align: middle;
		text-align: left;
		padding: 0 7px;
	}
	.woocommerce table.form-table .select2-container {
	min-width: 257px!important;
}
	.fields_adjustment th.sort {
		width: 16px;
		padding: 0 16px;
	}
	.fields_adjustment td.sort {
		width: 16px;
		padding: 0 16px;
		cursor: move;
		background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;   
		}
	.fields_adjustment td.remove_icon  {
		width: 16px;
		padding: 0 16px;
		cursor: pointer;
		background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABIAAAASCAYAAABWzo5XAAAAA3NCSVQICAjb4U/gAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAEnQAABJ0Ad5mH3gAAAAZdEVYdFNvZnR3YXJlAHd3dy5pbmtzY2FwZS5vcmeb7jwaAAAB70lEQVQ4T61UPUhbURT+7n0vLxE1vmqFEBTR6lS7uHaTYpfqopsu0qkgODh0EURwadFBEJy62a3t0BbBIm5dXdTJP/whiFr7EpUmL3m5nnNixR80EfLBXe79vu+ce865VxkCbsHf2Ud6eQWZzS1k1tZlL/y8DeFnLYh0vIDT1CB713HDKPBS8D5/xemPX8hns1COA2VbcmZyAYzvQ4dCqO7ugtvfC8uNyhnjyiibOMDByDhyngdFZKW1EG7D5PMwFMR2XcSmxhCKx2RfjIJkCol375E7IZMwZaGUHN4Hjm0yPuxaF/HZD7BqopCw3twXBH9LM2Ewh7msYS1D+zt7OP25CNh0HdqQaCUsCUca1rKHTi+vIk9FVFrR/YmUTsP8K7KYQ1zWsJY91OHHGXO29Fu6Y7k1iPa8ptwlNY55F3x1Okp9X6AuJ6WbVZ0voXYHh01w9AegbjitzYhPT1wqHkZieBT+xjYVR8OqrysUuxwo39WS3+bN8cwnWFWVhWL7GSE+CPJSTliKHZyd4+nQW+hIRzs0PYX/XVCRCFRFkcWcyy6ztuDR1IjqN6+AXFYSkWErYUnSpGEte0ix3YE+WE9cGXsetmKQoSQua1jLECN+K7HJMdhsRgPGD/M+yKMlDnNZw1pG+b+R63j8xwZcADXJQNHUd268AAAAAElFTkSuQmCC) no-repeat center
	}
	.fields_adjustment #elex_raq_add_field {
		cursor: pointer;
	}   
	tr td {
	white-space: nowrap;
} 
body {
	background:#FCFCFC;
	color:#222;
	font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Open Sans,Helvetica Neue,sans-serif;
}
</style>
