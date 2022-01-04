<?php
/*
Plugin Name: REHub Framework
Plugin URI: https://1.envato.market/JZgzN
Description: Framework plugin for REHub - Price Comparison, Affiliate Marketing, Multi Vendor Store, Community Theme.
Version: 4.1
Author: Sizam
Author URI: https://wpsoul.com/
Text Domain: rehub-framework
Domain Path: /lang/
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/* Costants */
if ( ! defined( 'RH_PLUGIN_VER' ) ){
	define( 'RH_PLUGIN_VER', '4.1' );
}

if ( ! defined( 'RH_FRAMEWORK_ABSPATH' ) ) {
	define( 'RH_FRAMEWORK_ABSPATH', dirname( __FILE__ ) );
}

if ( ! defined( 'RH_FRAMEWORK_URL' ) ) {
	define( 'RH_FRAMEWORK_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
}

if ( get_template() === 'rehub-theme' ) {	
	
	/* Include the main Rehub Framework class. */
	if ( ! class_exists( 'REHub_Framework' ) ) {
		include_once RH_FRAMEWORK_ABSPATH .'/includes/class-rehub.php';
	}
	
	// run the plugin
	REHub_Framework::instance();

	//run Gutenberg
	require_once RH_FRAMEWORK_ABSPATH .'/class-autoload.php';
	\Rehub\Gutenberg\Init::instance();	

}
else {
	add_action( 'admin_notices', 'rh_admin_notice_warning' );
}


/* Show notice in the admin dashboard if the REHub theme is not active */
function rh_admin_notice_warning() {
	if ( is_plugin_active( plugin_basename( __FILE__ ) ) ){
		// deactivate_plugins( plugin_basename( __FILE__ ) );
		?>
		<div class="notice notice-warning">
			<p><?php printf( esc_html__( 'Sorry, REHub Framework plugin works only with REHub themes. Please, deactivate Rehub Framework plugin or activate REHub theme %s', 'rehub-framework' ), '<a href="https://1.envato.market/JZgzN" target="_blank">here</a>' ) ; ?></p>
		</div>
		<?php
	}
}