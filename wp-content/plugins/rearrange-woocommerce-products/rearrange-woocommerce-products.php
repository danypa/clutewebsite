<?php
/*
**************************************************************************

Plugin Name: Rearrange Woocommerce Products	
Plugin URI: https://wordpress.org/plugins/rearrange-woocommerce-products/
Description: a plugin to Rearrange Woocommerce Products listed on the Shop page
Version: 2.3.0
Author: Aslam Doctor
Author URI: https://aslamdoctor.com/	
Developer: Aslam Doctor
Developer URI: https://aslamdoctor.com/
Text Domain:  rwpp
*
* WC requires at least: 3.7	
* WC tested up to: 4.1.0
* 
* License: GNU General Public License v3.0
* License URI: http://www.gnu.org/licenses/gpl-3.0.html

**************************************************************************

Rearrange Woocommerce Products is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Rearrange Woocommerce Products is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Rearrange Woocommerce Products. If not, see <http://www.gnu.org/licenses/>.

**************************************************************************
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

//====== Add Admin Menu ======
add_action('admin_menu', 'rwpp_register_my_custom_submenu_page', 99);

function rwpp_register_my_custom_submenu_page() {
	if( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$role = ( array ) $user->roles;
		
		if(in_array('administrator', $role)){
			add_submenu_page( 
				'edit.php?post_type=product', 
				__('Rearrange Products', 'rwpp'), 
				__('Rearrange Products', 'rwpp'), 
				'manage_options', 
				'rwpp-page', 
				'rwpp_callback' 
			);   
		}
		else if(in_array('shop_manager', $role)){
			add_submenu_page( 
				'edit.php?post_type=product', 
				__('Rearrange Products', 'rwpp'), 
				__('Rearrange Products', 'rwpp'), 
				'shop_manager', 
				'rwpp-page', 
				'rwpp_callback' 
			);   
		}
		else if(current_user_can('manage_woocommerce')){
			add_submenu_page( 
				'edit.php?post_type=product', 
				__('Rearrange Products', 'rwpp'), 
				__('Rearrange Products', 'rwpp'), 
				'shop_manager', 
				'rwpp-page', 
				'rwpp_callback' 
			);   
		}
	}
}



//====== Load Admin Page View ======
function rwpp_callback() {
	include "inc/functions.php";
	include "view.php";
}


//====== Add Scripts and Styles ======
add_action( 'admin_enqueue_scripts', 'rwpp_enqueue' );
function rwpp_enqueue($hook) {
    if ( 'product_page_rwpp-page' != $hook ) {
        return;
    }

    // Stylesheets
	wp_register_style('rwpp_app_css', (plugin_dir_url( __FILE__ )."/css/app.css"), false);
	wp_enqueue_style('rwpp_app_css');

	// Javascripts
	wp_register_script('rwpp_app_js', (plugin_dir_url( __FILE__ )."/js/app.js"), array(), false, true);
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('rwpp_app_js');
}



//====== Submit Form Action ======
add_action('wp_ajax_save_rwpp', 'rwpp_handle_form_action');
add_action('wp_ajax_nopriv_save_rwpp', 'rwpp_handle_form_action'); 
function rwpp_handle_form_action(){
	if ( ! empty( $_POST ) ) {
		// Sanitize post values
		$product_ids = isset( $_POST['product_id'] ) ? 
					array_map( 'sanitize_text_field', wp_unslash( $_POST['product_id'] ) ):
					array();
					
		$current_menu_orders = isset( $_POST['current_menu_order'] ) ? 
					array_map( 'sanitize_text_field', wp_unslash( $_POST['current_menu_order'] ) ):
					array();

		$new_menu_orders = isset( $_POST['new_menu_order'] ) ? 
					array_map( 'sanitize_text_field', wp_unslash( $_POST['new_menu_order'] ) ):
					array();
		
		$i=0;


		if ( is_array( $product_ids ) && is_array( $current_menu_orders ) && is_array( $new_menu_orders )) {
			global $wpdb;

			$sql_query = "UPDATE {$wpdb->prefix}posts
				SET menu_order = ( CASE ";
			$fields_in = '';

			foreach($product_ids as $product_id){
				$product_id = intval($product_id);
				$current_menu_order = intval($current_menu_orders[$i]);
				$new_menu_order = intval($new_menu_orders[$i]);
				
				// Update record
				$my_post = array(
					'ID'           => $product_id,
					'menu_order'   => $new_menu_order
				);
				//wp_update_post( $my_post );

				$sql_query.="WHEN ID = '".$product_id."' THEN '".$new_menu_order."' ";
				$fields_in.=$product_id.',';

				// Reset post array
				$my_post="";
				
				$i++;
			}

			$fields_in = rtrim($fields_in, ',');

			$sql_query.="END ) 
			WHERE ID IN(".$fields_in.")";

			$wpdb->query($sql_query);
			
			_e('<div class="notice notice-success is-dismissible">
			<p><strong>All products are rearranged now.</strong></p>
			</div>', 'rwpp'); 
		}
	}
	
	exit;
}


//====== Check if woocommerce is installed and activated before activating plugin ======
add_action( 'admin_init', 'rwpp_check_required_plugin' );
function rwpp_check_required_plugin() {
    if ( is_admin() && current_user_can( 'activate_plugins' ) && !class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'rwpp_plugin_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function rwpp_plugin_notice(){
	_e('<div class="error"><p>Please activate Woocommerce plugin before using <strong>Rearrange Woocommerce Products</strong> plugin.</p></div>', 'rwpp');
}