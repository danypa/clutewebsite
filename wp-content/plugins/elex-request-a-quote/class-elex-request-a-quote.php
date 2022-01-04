<?php
/**
 *
 * Main File.
 *
 * @package Elex Request a Quote
 */

/*
Plugin Name:          ELEX WooCommerce Request a Quote
Plugin URI:           https://elextensions.com/plugin/
Description:          Create Request a Quote option for your WooCommerce products. You can also create and customize request a quote forms to be displayed on the frontend. The plugin will also send automated email notifications for quote submissions, approvals, and rejections.
Version: 			  1.1.5
WC requires at least: 2.6.0
WC tested up to:      6.0
Author:               ELEXtensions
Author URI:           https://elextensions.com/
Developer:            ELEXtensions
Developer URI:        https://elextensions.com/
License:              GPLv2
Text Domain:          elex-request-a-quote
*/

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

// WooCommerce Active Check.
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {
	if (class_exists('Elex_Request_A_Quote_Premium')) {
		add_action(
			'admin_notices',
			function () {
				?>
			<div class="notice notice-error is-dismissible">
				<p>
					<?php
						$allowed_html = array(
							'a'      => array(
								'href'  => array(),
								'title' => array(),
							),
							'br'     => array(),
							'em'     => array(),
							'strong' => array(),
						);
						echo wp_kses(__("You already have the basic version of <strong>Elex WooCommerce Request a Quote</strong> installed. Please disable & remove the Basic Version before using Premium Version. For any issues, kindly contact our <a target='_blank' href='https://elextensions.com/support/'>support</a>.", 'elex-request-a-quote'), $allowed_html);
					?>
				</p>
			</div>
				<?php
				deactivate_plugins(plugin_basename(__FILE__));
			}
		);
	} elseif (! class_exists('Elex_Request_A_Quote')) {
		/**
		 *  Request a Quote Class.
		 */
		class Elex_Request_A_Quote {
		
			/**
			 * Array of settings stored in database.
			 *
			 * @var array $all_settings Array of settings stored in database.
			 */
			protected $all_settings;

			/** Constructor */
			public function __construct() {
				register_activation_hook(__FILE__, array( $this, 'elex_quote_request_add_navigation_menu' ));
				register_deactivation_hook(__FILE__, array( $this, 'elex_quote_request_flush_data' ));
				define('ELEX_RAQ_PLUGIN_DOMAIN', 'elex-request-a-quote');

				include_once 'includes/class-eraq-settings.php';
				$this->settings = new Eraq_Settings();

				include_once 'includes/class-eraq-shortcode-handler.php';
				$this->cart_handler = new Eraq_Shortcode_Handler();

				include_once 'includes/class-eraq-button-handler.php';
				$this->settings_implementor = new Eraq_Button_Handler();

				include_once 'includes/class-eraq-cart-handler.php';
				include_once 'includes/elex-raq-add-order-statuses.php';
				include_once 'includes/elex-raq-send-notification-email.php';
				$this->cart_handler = new Eraq_Cart_Handler();
				add_filter('plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'elex_request_a_quote_plugin_action_links' ));
			}
			/**
			 * Flush Data on Deactivation.
			 */
			public function elex_quote_request_flush_data() {
				$path_object_1    = get_page_by_path( '/add-to-quote-product-list' );
				if ( is_object( $path_object_1 ) ) {
					$path_object_1->post_status ='draft';
					wp_update_post( $path_object_1 );
				}

				$path_object_2 = get_page_by_path('/quote-received-page');
				if ( is_object( $path_object_2 ) ) {
					$path_object_2->post_status ='draft';
					wp_update_post( $path_object_2 );
				}
			}
			/** Add Navigation Menu. */
			public function elex_quote_request_add_navigation_menu() {
				$path_object_1    = get_page_by_path( '/add-to-quote-product-list' );
				if ( is_object( $path_object_1 ) ) {

					$path_object_1->post_status ='publish';

					wp_update_post( $path_object_1 );
				} else {
	
					$quote_request_page = array(
						'post_title'   => __('Quotes List', 'ELEX_RAQ_PLUGIN_DOMAIN'),
						'post_content' => '[elex_quote_request_list]',
						'post_status'  => 'publish',
						'post_name'    => 'add-to-quote-product-list',
						'post_type'    => 'page',
					);

					wp_insert_post($quote_request_page);
				}

				$path_object_2 = get_page_by_path('/quote-received-page');
				if ( is_object( $path_object_2 ) ) {
					$path_object_2->post_status ='publish';

					wp_update_post( $path_object_2 );
	
				} else {
				
					// Create post object.
					$quote_received_page = array(
						'post_title'   => __('Quote Received', 'ELEX_RAQ_PLUGIN_DOMAIN'),
						'post_content' => '[elex_quote_received_page]',
						'post_status'  => 'publish',
						'post_name'    => 'quote-received-page',
						'post_type'    => 'page',
					);

					// Insert the post into the database.
					wp_insert_post($quote_received_page);
					
				}			}

			/**
			 * To add settings url near plugin under installed plugin.
			 *
			 * @param array $links Array of Links.
			 */
			public function elex_request_a_quote_plugin_action_links( $links) {
				$plugin_links = array(
					'<a href="' . admin_url('admin.php?page=wc-settings&tab=elex_request_a_quote') . '">' . __('Settings', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>',
					'<a href="https://elextensions.com/plugin/woocommerce-request-a-quote-plugin/" target="_blank">' . __('Premium Upgrade', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>',
					'<a href="https://elextensions.com/support/" target="_blank">' . __('Support', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>',
				);
				return array_merge($plugin_links, $links);
			}
		}
		new Elex_Request_A_Quote();
	}
} else {
	add_action(
		'admin_notices',
		function () {
			?>
		<div class="notice notice-error is-dismissible">
			<p>
				<?php
					$allowed_html = array(
						'strong' => array(),
					);
					echo wp_kses(__('<strong>WooCommerce</strong> plugin must be active for <strong>Elex WooCommerce Request a Quote - Basic</strong> to work.', 'ELEX_RAQ_PLUGIN_DOMAIN'), $allowed_html);
					deactivate_plugins(plugin_basename(__FILE__));
				?>
			</p>
		</div>
			<?php
		}
	);
}
add_action(
	'woocommerce_init',
	function () {
		wc_enqueue_js(
			"
			jQuery('a[href*=\"quote-received-page\"]').closest('li').remove();
	"
		);
	}
);

/** Load Plugin Text Domain. */
function elex_raq_load_plugin_textdomain() {
	load_plugin_textdomain('ELEX_RAQ_PLUGIN_DOMAIN', false, basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'elex_raq_load_plugin_textdomain' );
