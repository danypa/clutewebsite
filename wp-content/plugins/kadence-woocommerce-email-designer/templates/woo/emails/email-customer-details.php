<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">

	<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
		<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">

	<?php endif; ?>

	<h3 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h3><!-- vanessa -->

	<table  width="100%" border="1" bordercolor="#CCC" style="margin-bottom: 20px;background: #fcfcfc;"><tr><td style="padding: 20px; border-color: #19428A;font-size:13px "><!-- vanessa -->
		<?php  
        	//echo wp_kses_post( $order->get_formatted_billing_address( esc_html__( 'N/A', 'woocommerce' ) ) ); 
            ?>

		<?php if ( $order->get_billing_first_name() ) : ?>
			<p style="margin-bottom: 10px"><strong>Nombre: </strong><?php echo esc_html( $order->get_billing_first_name().' '.$order->get_billing_last_name() ); ?></p><!-- vanessa -->
		<?php endif; ?>
        
		<?php if ( $order->get_billing_phone() ) : ?>
			<p style="margin-bottom: 10px"><strong>Teléfono: </strong><?php echo esc_html( $order->get_billing_phone() ); ?></p><!-- vanessa -->
		<?php endif; ?>
        
		<?php if ( $order->get_billing_email() ) : ?>
			<p style="margin-bottom: 10px"><strong>Email: </strong><?php echo esc_html( $order->get_billing_email() ); ?></p><!-- vanessa -->
		<?php endif; ?>
        
		<?php if ( $order->get_billing_address_1() ) : ?>
			<p style="margin-bottom: 10px"><strong>Dirección de Facturación: </strong><?php echo esc_html( $order->get_billing_address_1() ).', '.
			 			getDistritoByidDist(get_post_meta( $order->id, '_billing_district', true )).', '.
			 			getProvinciaByidProv(explode("-",get_post_meta( $order->id, '_billing_city', true ))[0]).', '.
			 			getDepartamentoByidDepa(get_post_meta( $order->id, '_billing_department', true )); ?></p><!-- vanessa -->
		<?php endif; ?> 
        
	</td></tr></table>

	<?php if ( true ) : ?>

		<!-- </div><!-- /.col-1 --> 

		<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
			<h3 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h3> <!--vanessa-->
			<table width="100%" border="1" bordercolor="#CCC" style="margin-bottom: 40px;background: #fcfcfc;"><tr><td style="padding: 20px; border-color: #19428A;">

<?php 
    $calle = get_post_meta( $order->id, '_shipping_calle', true );

    if ( $calle ) : ?>
			<p style="font-size: 13px;"><strong>Dirección de Envío: </strong><?php echo esc_html( $calle ).', '.
			 			getDistritoByidDist(get_post_meta( $order->id, '_shipping_district', true )).', '.
			 			getProvinciaByidProv(explode("-",get_post_meta( $order->id, '_shipping_city', true ))[0]).', '.
			 			getDepartamentoByidDepa(get_post_meta( $order->id, '_shipping_department', true )); ?></p>
<?php else : ?> 
			<p style="font-size: 13px;"><strong>Dirección de Envío: </strong><?php echo esc_html( $order->get_billing_address_1() ).', '.
			 			getDistritoByidDist(get_post_meta( $order->id, '_billing_district', true )).', '.
			 			getProvinciaByidProv(explode("-",get_post_meta( $order->id, '_billing_city', true ))[0]).', '.
			 			getDepartamentoByidDepa(get_post_meta( $order->id, '_billing_department', true )); ?></p><!-- vanessa -->
		
  
		<?php endif; ?> 		

		
		
<?php 
	//echo '<br><p style="font-size: 18px;"><strong>'.__('Dirección de Envío').':</strong> ' . 
	//wp_kses_post( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'woocommerce' ) ) ) . '</p>'; 
?>

</td></tr></table>
		</div><!-- /.col-2 -->

	</section><!-- /.col2-set -->

	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

</section>
