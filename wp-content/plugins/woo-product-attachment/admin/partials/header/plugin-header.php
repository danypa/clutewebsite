<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$plugin_url = WCPOA_PLUGIN_URL;
$plugin_name = WCPOA_PLUGIN_NAME;
$plugin_text_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
global  $wpap_fs ;
$version_label = 'Free Version';
?>
<div id="dotsstoremain">
    <div class="all-pad">
        <header class="dots-header">
            <div class="dots-logo-main">
                <img  src="<?php 
echo  esc_url( $plugin_url ) . '/admin/images/woo-product-att-logo.png' ;
?>">
            </div>
            <div class="dots-header-right">
                <div class="logo-detail">
                    <strong><?php 
esc_html_e( $plugin_name, $plugin_text_domain );
?></strong>
                    <span><?php 
esc_html_e( $version_label, $plugin_text_domain );
?> <?php 
echo  esc_html( WCPOA_PLUGIN_VERSION ) ;
?></span>
                </div>

                <div class="button-group">
                    <?php 
?>
                        <div class="button-dots-left">
                            <span class="support_dotstore_image"><a target="_blank" href="<?php 
echo  esc_url( $wpap_fs->get_upgrade_url() ) ;
?>">
                                <img src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/upgrade_new.png' ;
?>"></a>
                            </span>
                        </div>
                    <?php 
?>
                    <div class="button-dots">
                        <span class="support_dotstore_image"><a target="_blank" href="<?php 
echo  esc_url( 'http://www.thedotstore.com/support/' ) ;
?>">
                            <img src="<?php 
echo  esc_url( WCPOA_PLUGIN_URL ) . 'admin/images/support_new.png' ;
?>"></a>
                        </span>
                    </div>
                </div>
            </div>
            <?php 
$about_plugin_setting_menu_enable = '';
$wcpoa_bulk_attachment = '';
$about_plugin_get_started = '';
$about_plugin_quick_info = '';
$dotstore_setting_menu_enable = '';
$wcpoa_plugin_setting_page = '';
$tab_menu = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
$page_menu = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
if ( isset( $tab_menu ) && $tab_menu === 'wcpoa_plugin_setting_page' ) {
    $wcpoa_plugin_setting_page = "active";
}
if ( empty($tab_menu) && $page_menu === 'woocommerce_product_attachment' ) {
    $wcpoa_plugin_setting_page = "active";
}

if ( !empty($tab_menu) && $tab_menu === 'wcpoa-plugin-getting-started' ) {
    $about_plugin_setting_menu_enable = "active";
    $about_plugin_get_started = "active";
}


if ( !empty($tab_menu) && $tab_menu === 'wcpoa-plugin-quick-info' ) {
    $about_plugin_setting_menu_enable = "active";
    $about_plugin_quick_info = "active";
}

?>

            <div class="dots-menu-main">
                <nav>
                    <ul>
                        <li><a class="dotstore_plugin <?php 
echo  esc_attr( $wcpoa_plugin_setting_page ) ;
?>" href="<?php 
echo  esc_url( site_url( 'wp-admin/admin.php?page=woocommerce_product_attachment&tab=wcpoa_plugin_setting_page' ) ) ;
?>"><?php 
esc_html_e( 'Settings', $plugin_text_domain );
?></a></li>
                        <?php 
?>    
                        <li>
                            <a class="dotstore_plugin <?php 
echo  esc_attr( $about_plugin_setting_menu_enable ) ;
?>" href="<?php 
echo  esc_url( site_url( 'wp-admin/admin.php?page=woocommerce_product_attachment&tab=wcpoa-plugin-getting-started' ) ) ;
?>"><?php 
esc_html_e( 'About Plugin', $plugin_text_domain );
?></a>
                        <ul class="sub-menu">
                            <li>
                                <a class="dotstore_plugin <?php 
echo  esc_attr( $about_plugin_get_started ) ;
?>" href="<?php 
echo  esc_url( site_url( 'wp-admin/admin.php?page=woocommerce_product_attachment&tab=wcpoa-plugin-getting-started' ) ) ;
?>"><?php 
esc_html_e( 'Getting Started', $plugin_text_domain );
?></a></li>
                            <li>
                                <a class="dotstore_plugin <?php 
echo  esc_attr( $about_plugin_quick_info ) ;
?>" href="<?php 
echo  esc_url( site_url( 'wp-admin/admin.php?page=woocommerce_product_attachment&tab=wcpoa-plugin-quick-info' ) ) ;
?>">Introduction</a>
                            </li>
                            <li><a target="_blank" href="https://www.thedotstore.com/suggest-a-feature/">Suggest A Feature</a></li>
                        </ul>

                        </li>
                        <li>
                            <a class="dotstore_plugin <?php 
echo  esc_attr( $dotstore_setting_menu_enable ) ;
?>"  href="#">Dotstore</a>
                            <ul class="sub-menu">
                                <li><a target="_blank" href="https://www.thedotstore.com/woocommerce-plugins/"><?php 
esc_html_e( 'WooCommerce Plugins', $plugin_text_domain );
?></a></li>
                                <li><a target="_blank" href="https://www.thedotstore.com/wordpress-plugins/"><?php 
esc_html_e( 'Wordpress Plugins', $plugin_text_domain );
?></a></li><br>
                                <li><a target="_blank" href="https://www.thedotstore.com/support"><?php 
esc_html_e( 'Contact Support', $plugin_text_domain );
?></a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </header>