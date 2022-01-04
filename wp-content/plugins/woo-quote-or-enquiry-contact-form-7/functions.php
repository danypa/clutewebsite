<?php 
if(!defined('ABSPATH')) exit;

if(!defined("WQOECF_PLUGIN_DIR_PATH"))
	
	define("WQOECF_PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));	
	
if(!defined("WQOECF_PLUGIN_URL"))
	
	define("WQOECF_PLUGIN_URL",plugins_url().'/'.basename(dirname(__FILE__)));	


// Get options
function wqoecf_quote_enquiry_options()
{
	return get_option('wqoecf_quote_or_enquiry_settings');
}

// Get form postmeta
function get_option_quote_wqoecf_disable_form($post_id){	
	return get_post_meta($post_id, '_wqoecf_disable_form', true );
}

// Success message
function  success_option_msg_wqoecf($msg)
{
	
	return ' <div class="notice notice-success acttf-success-msg is-dismissible"><p>'. $msg . '</p></div>';		
	
}

// Error message
function  failure_option_msg_wqoecf($msg)
{

	return '<div class="notice notice-error acttf-error-msg is-dismissible"><p>' . $msg . '</p></div>';		
	
}




?>