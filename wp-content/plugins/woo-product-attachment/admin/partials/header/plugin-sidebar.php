<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$image_url = WCPOA_PLUGIN_URL . 'admin/images/right_click.png';
$review_url = '';
$plugin_at = '';
$review_url = esc_url( 'https://wordpress.org/plugins/woo-product-attachment/#reviews' );
$plugin_at = 'WP.org';
?>
<div class="dotstore_plugin_sidebar">

    <div class="dotstore-important-link">
		<div class="image_box">
			<img src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL . 'admin/images/rate-us.png' ) ;
?>" alt="">
		</div>
		<div class="content_box">
			<h3>Like This Plugin?</h3>
			<p>Your Review is very important to us as it helps us to grow more.</p>
			<a class="btn_style" href="<?php 
echo  $review_url ;
?>" target="_blank">Review Us on <?php 
echo  $plugin_at ;
?></a>
		</div>
	</div>
     <div class="dotstore-important-link">
        <h2><span class="dotstore-important-link-title"><?php 
esc_html_e( 'Important link', WCPOA_PLUGIN_TEXT_DOMAIN );
?></span></h2>
        <div class="video-detail important-link">
            <ul>
                <li>
                    <img src="<?php 
echo  esc_url( $image_url ) ;
?>">
                    <a target="_blank" href="<?php 
echo  esc_url( 'http://www.thedotstore.com/docs/plugin/woocommerce-product-attachment/' ) ;
?>"><?php 
esc_html_e( 'Plugin documentation', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li> 
                <li>
                    <img src="<?php 
echo  esc_url( $image_url ) ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/support"><?php 
esc_html_e( 'Support platform', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img src="<?php 
echo  esc_url( $image_url ) ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/suggest-a-feature"><?php 
esc_html_e( 'Suggest A Feature', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img src="<?php 
echo  esc_url( $image_url ) ;
?>">
                    <a  target="_blank" href="<?php 
echo  esc_url( 'https://www.thedotstore.com/woocommerce-product-attachment#tab-changelog' ) ;
?>"><?php 
esc_html_e( 'Changelog', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- html for popular plugin !-->
    <div class="dotstore-important-link">
        <h2><span class="dotstore-important-link-title"><?php 
esc_html_e( 'OUR POPULAR PLUGINS', WCPOA_PLUGIN_TEXT_DOMAIN );
?></span></h2>
        <div class="video-detail important-link">
            <ul>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/advance-flat-rate.png' ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/advanced-flat-rate-shipping-method-for-woocommerce"><?php 
esc_html_e( 'Advanced Flat Rate Shipping Method', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li> 
                <li>
                    <img class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/wc-conditional-product-fees.png' ;
?>">
                    <a  target="_blank" href="https://www.thedotstore.com/woocommerce-conditional-product-fees-checkout"><?php 
esc_html_e( 'Conditional Product Fees for WooCommerce', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/advance-menu-manager.png' ;
?>">
                    <a  target="_blank" href="https://www.thedotstore.com/advance-menu-manager-wordpress"><?php 
esc_html_e( 'Advance Menu Manager', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/wc-enhanced-ecommerce-analytics-integration.png' ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/woocommerce-enhanced-ecommerce-analytics-integration-with-conversion-tracking"><?php 
esc_html_e( 'Woo Enhanced Ecommerce Analytics Integration', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img  class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/advanced-product-size-charts.png' ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/woocommerce-advanced-product-size-charts"><?php 
esc_html_e( 'Advanced Product Size Charts', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                <li>
                    <img  class="sidebar_plugin_icone" src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/blocker.png' ;
?>">
                    <a target="_blank" href="https://www.thedotstore.com/woocommerce-blocker-prevent-fake-orders-blacklist-fraud-customers"><?php 
esc_html_e( 'Blocker – Prevent Fake Orders And Blacklist Fraud Customers for WooCommerce', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
                </li>
                </br>
            </ul>
        </div>
        <div class="view-button">
            <a class="view_button_dotstore" target="_blank" href="https://www.thedotstore.com/plugins"><?php 
esc_html_e( 'VIEW ALL', WCPOA_PLUGIN_TEXT_DOMAIN );
?></a>
        </div>
    </div>
</div>
</div>
</body>
</html>