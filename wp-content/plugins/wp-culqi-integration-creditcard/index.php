<?php
/*
Plugin Name: Culqi Full Integration One Click
Plugin URI: https://www.letsgodev.com/products/woocommerce-culqi-pago-con-un-click
Description: This plugin allows enable the credit card form in the checkout form
Version: 1.3.7
Author: Lets Go Dev
Author URI: https://www.letsgodev.com/
Developer: Alexander Gonzales
Developer URI: https://vcard.gonzalesc.org/
Text Domain: culqi, woocommerce, integration
Requires at least: 5.2
Tested up to: 5.4.1
Stable tag: 5.2
WC requires at least: 3.9.0
WC tested up to: 4.2.0
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('FULLCULQI_CC_API_URL', 'https://www.letsgodev.com/index.php');
define('FULLCULQI_CC_PRODUCT_ID', 'PLFULLCULQI2');
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define('FULLCULQI_CC_INSTANCE', str_replace($protocol, "", get_bloginfo('wpurl')));
define('FULLCULQI_CC_EMAIL_SUPPORT','support@letsgodev.com');
define('FULLCULQI_CC_VERSION', '1.3.7');

define('FULLCULQI_CC_DIR' , plugin_dir_path(__FILE__));
define('FULLCULQI_CC_URL' , plugin_dir_url(__FILE__));
define('FULLCULQI_CC_BASE' , plugin_basename( __FILE__ ));

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc.php';

/**
 * Store the plugin global
 */
global $fullculqi_cc;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */

function fullculqi_cc() {
	return FullCulqi_CardCredit::instance();
}

$GLOBALS['fullculqi_cc'] = fullculqi_cc();
?>