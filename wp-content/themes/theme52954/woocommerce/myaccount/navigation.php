<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation">
	<ul style="list-style: none;list-style-type: none;list-style-position: initial;list-style-image: initial;margin-block-start: 1em;margin-block-end: 1em;margin-inline-start: 0px;margin-inline-end: 0px;color: #235dc5;"><!-- vanessa -->
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>" style="
    border-top: 1px solid #eee;
    border-color: #9ea3a8;
    padding: .462em 0;
    
    border-right: 1px solid #9ea3a8;
    font-size:13px;
    color: #19428a !important;
    background: white;"><!-- vanessa -->

				<a class="navigationc" href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" ><img width="30px" src="<?php echo esc_url( get_site_url()."/wp-content/uploads/".($label=="Inicio"?"inicio_icon.png":($label=="Cambiar Contraseña"?"candado_icon.png":($label=="Pedidos"?"pedidos_icon.png":"ubicacion_icon.png"))) ); ?>" style="padding:0px 5px 0px 5px"><?php echo esc_html( $label ); ?></a><!-- vanessa -->
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>

