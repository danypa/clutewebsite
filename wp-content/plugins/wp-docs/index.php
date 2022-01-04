<?php defined( 'ABSPATH' ) or die( 'No fankari bachay!' );
/*
Plugin Name: WP Docs
Plugin URI: http://androidbubble.com/blog/wp-docs
Description: A documents management tool for education portals.
Author: Fahad Mahmood
Version: 1.4.7
Text Domain: wp-docs
Domain Path: /languages
Author URI: https://profiles.wordpress.org/fahadmahmood/
License: GPL2
	
This WordPress Plugin is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later version. This free software is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with this software. If not, see http://www.gnu.org/licenses/gpl-2.0.html.	
*/

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if(!function_exists('pre')){
	function pre($data){
			if(isset($_GET['debug'])){
				pree($data);
			}
		}	 
	} 	
	if(!function_exists('pree')){
	function pree($data){
				echo '<pre>';
				print_r($data);
				echo '</pre>';	
		
		}	 
	} 
	
	global $wpdocs_data, $wpdocs_pro, $wpdocs_premium_link, $wpdocs_dir, $wpdocs_levels, $wpdocs_versions_type, $wpdocs_url, $icon_sub_path, $wpdocs_options;
	
	$wpdocs_data = get_plugin_data(__FILE__);
	$wpdocs_dir = plugin_dir_path( __FILE__ );
    $wpdocs_url = plugin_dir_url( __FILE__ );
	$wpdocs_versions_type = get_option('wpdocs_versions_type', 'old');
    $icon_sub_path = 'img/filetype-icons/';
    $wpdocs_options = get_option('wpdocs_options', array());


    $wpdocs_premium_link = 'http://shop.androidbubbles.com/product/wp-docs-pro';
	
	
	$wpdocs_pro_file = $wpdocs_dir.'pro/wp-docs-pro.php';

	
	include_once 'inc/common.php';
	
    $wpdocs_pro = file_exists($wpdocs_pro_file);
    if($wpdocs_pro){
        include($wpdocs_pro_file);
    }	
	
	include_once('inc/functions.php');


	