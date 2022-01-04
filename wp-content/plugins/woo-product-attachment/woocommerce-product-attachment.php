<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.multidots.com/
 * @since             1.0.0
 * @package           Woocommerce_Product_Attachment
 *
 * @wordpress-plugin
 * Plugin Name: WooCommerce Product Attachment
 * Plugin URI:        https://www.thedotstore.com/
 * Description:       WooCommerce Product Attachment Plugin will help you to attach/ upload any kind of files for a customer orders.You can attach any type of file like Images, documents, videos and many more..
 * Version:           1.5.4
 * Author:            theDotstore
 * Author URI:        https://profiles.wordpress.org/dots
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-product-attachment
 * Domain Path:       /languages
 * WC tested up to: 4.0
 */
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}

if ( function_exists( 'wpap_fs' ) ) {
    wcfdg_fs()->set_basename( false, __FILE__ );
    return;
}

add_action( 'plugins_loaded', 'wcpoa_initialize_plugin' );
$wc_active = in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ), true );

if ( true === $wc_active ) {
    
    if ( !function_exists( 'wpap_fs' ) ) {
        // Create a helper function for easy SDK access.
        function wpap_fs()
        {
            global  $wpap_fs ;
            
            if ( !isset( $wpap_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $wpap_fs = fs_dynamic_init( array(
                    'id'              => '3473',
                    'slug'            => 'woo-product-attachment',
                    'type'            => 'plugin',
                    'public_key'      => 'pk_eac499ce039e8334a8d30870fd1fd',
                    'is_premium'      => false,
                    'premium_suffix'  => 'Premium',
                    'has_addons'      => false,
                    'has_paid_plans'  => true,
                    'has_affiliation' => 'selected',
                    'menu'            => array(
                    'slug'       => 'woocommerce_product_attachment',
                    'first-path' => 'admin.php?page=woocommerce_product_attachment&tab=wcpoa-plugin-getting-started',
                    'contact'    => false,
                    'support'    => false,
                ),
                    'is_live'         => true,
                ) );
            }
            
            return $wpap_fs;
        }
        
        // Init Freemius.
        wpap_fs();
        // Signal that SDK was initiated.
        do_action( 'wpap_fs_loaded' );
        wpap_fs()->get_upgrade_url();
        wpap_fs()->add_action( 'after_uninstall', 'wpap_fs_uninstall_cleanup' );
    }
    
    if ( !defined( 'WCPOA_PLUGIN_URL' ) ) {
        define( 'WCPOA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
    }
    if ( !defined( 'WCPOA_PLUGIN_VERSION' ) ) {
        define( 'WCPOA_PLUGIN_VERSION', '1.5.4' );
    }
    if ( !defined( 'WCPOA_PLUGIN_BASENAME' ) ) {
        define( 'WCPOA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-woocommerce-product-attachment-activator.php
     */
    function activate_woocommerce_product_attachment()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-attachment-activator.php';
        Woocommerce_Product_Attachment_Activator::activate();
    }
    
    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-woocommerce-product-attachment-deactivator.php
     */
    function deactivate_woocommerce_product_attachment()
    {
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-attachment-deactivator.php';
        Woocommerce_Product_Attachment_Deactivator::deactivate();
    }
    
    register_activation_hook( __FILE__, 'activate_woocommerce_product_attachment' );
    register_deactivation_hook( __FILE__, 'deactivate_woocommerce_product_attachment' );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-attachment.php';
    /**
     * Define all constants
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-attachment-constants.php';
    /**
     * User feedback class inclded
     */
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-attachment-user-feedback.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function convert_array_to_int( $arr )
    {
        foreach ( $arr as $key => $value ) {
            $arr[$key] = (int) $value;
        }
        return $arr;
    }
    
    function run_woocommerce_product_attachment()
    {
        $plugin = new Woocommerce_Product_Attachment();
        $plugin->run();
    }

}

/**
 * Check Initialize plugin in case of WooCommerce plugin is missing.
 *
 * @since    1.0.0
 */
function wcpoa_initialize_plugin()
{
    $wc_active = in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ), true );
    
    if ( current_user_can( 'activate_plugins' ) && $wc_active !== true || $wc_active !== true ) {
        add_action( 'admin_notices', 'wcpoa_plugin_admin_notice' );
    } else {
        run_woocommerce_product_attachment();
    }

}

/**
 * Show admin notice in case of WooCommerce plugin is missing.
 *
 * @since    1.0.0
 */
function wcpoa_plugin_admin_notice()
{
    $vpe_plugin = esc_html__( 'WooCommerce Product Attachment', 'woocommerce-product-attachment' );
    $wc_plugin = esc_html__( 'WooCommerce', 'woocommerce-product-attachment' );
    ?>
    <div class="error">
        <p>
            <?php 
    echo  sprintf( esc_html__( '%1$s requires %2$s to be installed & activated!', 'woocommerce-product-attachment' ), '<strong>' . esc_html( $vpe_plugin ) . '</strong>', '<a href="' . esc_url( 'https://wordpress.org/plugins/woocommerce/' ) . '" target="_blank"><strong>' . esc_html( $wc_plugin ) . '</strong></a>' ) ;
    ?>
        </p>
    </div>
    <?php 
}
