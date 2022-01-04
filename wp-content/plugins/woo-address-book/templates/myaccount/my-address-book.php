<div style="padding: 10px">
<?php
/**
 * Woo Address Book
 *
 * @package WooCommerce Address Book/Templates
 * @version 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$wc_address_book = WC_Address_Book::get_instance();

$woo_address_book_customer_id  = get_current_user_id();
$woo_address_book_address_book = $wc_address_book->get_address_book( $woo_address_book_customer_id );

// Do not display on address edit pages.
if ( ! $type ) : ?>

	<?php

	$woo_address_book_shipping_address = get_user_meta( $woo_address_book_customer_id, 'shipping_calle', true );//BY PALK
	
	// Only display if primary addresses are set and not on an edit page.
	if ( ! empty( $woo_address_book_shipping_address ) ) :
	
	?>

		<hr class="hraddress" /><!-- vanessa -->

		<div class="address_book">
			<h3><?php esc_html_e( 'Shipping Address Book', 'woo-address-book' ); ?></h3>

			<p class="myaccount_address">
				<?php echo esc_html( apply_filters( 'woocommerce_my_account_my_address_book_description', __( 'The following addresses are available during the checkout process.', 'woo-address-book' ) ) ); ?>
			</p>

			<?php
			if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
				echo '<div class="col2-set addresses address-book">';
			}

			foreach ( $woo_address_book_address_book as $woo_address_book_name => $woo_address_book_fields ) :
			
				// Prevent default shipping from displaying here.
				if ( 'shipping' === $woo_address_book_name || 'billing' === $woo_address_book_name ) {
					continue;
				}
				
				$woo_address_book_address = apply_filters(
					'woocommerce_my_account_my_address_formatted_address',
					array(
						'first_name' => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_first_name', true ),
						'last_name'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_last_name', true ),
						'company'    => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_company', true ),
					    'calle'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_calle', true ),//BY PALK
					    
					    'department'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_department', true ),//BY PALK
					    'district'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_district', true ),//BY PALK
					    
						//'address_1'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_address_1', true ),
						'address_2'  => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_address_2', true ),
						'city'       => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_city', true ),
						'state'      => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_state', true ),
						'postcode'   => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_postcode', true ),
						'country'    => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_country', true ),
						'departamento'    => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_departamento', true ),
						'provincia'    => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_provincia', true ),
						'distrito'    => get_user_meta( $woo_address_book_customer_id, $woo_address_book_name . '_distrito', true ),
					),
					$woo_address_book_customer_id,
					$woo_address_book_name
				);

				$woo_address_book_formatted_address = WC()->countries->get_formatted_address( $woo_address_book_address );

				//echo json_encode($woo_address_book_address);
				
				if ( $woo_address_book_formatted_address ) :
					?>

					<div class="wc-address-book-address">
						<div class="wc-address-book-meta">
						<table>
						<tbody>
						<tr>
						<td><a href="<?php echo esc_url( $wc_address_book->get_address_book_endpoint_url( $woo_address_book_name ) ); ?>" class="wc-address-book-edit"><img style="margin-right: 5px; margin-left: 5px;" title="Editar Dirección" src="https://clute.com.pe/wp-content/uploads/2020/03/edit.png" alt="<?php echo esc_attr__( 'Edit', 'woo-address-book' ); ?>" width="24" height="24" /></a></td>
						<td><a id="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-delete"><img style="margin-right: 5px; margin-left: 5px;" title="Eliminar Dirección" src="https://clute.com.pe/wp-content/uploads/2020/03/bin.png" alt="<?php echo esc_attr__( 'Delete', 'woo-address-book' ); ?>" width="24" height="24" /></a></td>
						<td><a id="<?php echo esc_attr( $woo_address_book_name ); ?>" class="wc-address-book-make-primary"><img style="margin-right: 5px; margin-left: 5px;" title="Convertir en Principal" src="https://clute.com.pe/wp-content/uploads/2020/03/choose.png" alt="<?php echo esc_attr__( 'Make Primary', 'woo-address-book' ); ?>" width="24" height="24" /></a></td>
						</tr>
						</tbody>
						</table>
						</div>
						<address class="address"><!-- vanessa -->
							<?php echo wp_kses( $woo_address_book_formatted_address, array( 'br' => array() ) ); ?>
						</address><br>
					</div>

				<?php endif; ?>

			<?php endforeach; ?>
			<br><br>
			<?php
			if ( ! wc_ship_to_billing_address_only() && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) {
				echo '</div>';
			}
			?>

		</div>
		</div>
		<?php 
	endif;
endif;

