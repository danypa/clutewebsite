<?php
/**
 *
 * File to implement settings tab.
 *
 * @package Elex Request a Quote
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WP_PLUGIN_DIR . '/woocommerce/includes/admin/settings/class-wc-settings-page.php';

/** Class - For settings */
class Eraq_Settings extends WC_Settings_Page {
	/**
	 * Array of settings stored in database.
	 *
	 * @var array $all_settings Array of settings stored in database.
	 */
	protected $all_settings;

	/** Constructor. */
	public function __construct() {
		$this->all_settings  = get_option( 'elex_request_a_quote_settings' );
		$this->form_settings = get_option( 'elex_request_a_quote_field_adjustment_options' );
		$this->id            = 'elex_request_a_quote';
		$this->elex_request_a_quote_setup();

	}

	/**
	 * Get an option set in our settings tab.
	 *
	 * @param string $key Key.
	 */
	public function elex_request_a_quote_get_option( $key ) {
		$fields = $this->elex_request_a_quote_get_fields();
		return apply_filters( 'wc_option_' . $key, elex_request_a_quote_get_option( 'wc_settings_elex_request_a_quote_' . $key, ( ( isset( $fields[ $key ] ) && isset( $fields[ $key ]['default'] ) ) ? $fields[ $key ]['default'] : '' ) ) );
	}

	/** Setup the WooCommerce settings */
	public function elex_request_a_quote_setup() {
		// Filters for adding tabs and sections.
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'elex_request_a_quote_add_settings_tab' ), 70 );
		add_filter( 'woocommerce_sections_elex_request_a_quote', array( $this, 'output_sections' ) );
		add_filter( 'woocommerce_settings_elex_request_a_quote', array( $this, 'elex_request_a_quote_output_section' ) );
		add_action( 'woocommerce_update_options_elex_request_a_quote', array( $this, 'elex_request_a_quote_update_section' ) );
		add_action( 'woocommerce_settings_tabs_elex_request_a_quote', array( $this, 'elex_request_a_quote_tab_content' ) );
		add_action( 'woocommerce_admin_field_activate_box', array( $this, 'generate_activate_box_html' ) );
		add_action(
			'init',
			function() {
				$raq_page_title = ! empty( $this->all_settings['raq_quote_list_page_title'] ) ? $this->all_settings['raq_quote_list_page_title'] : 'Quotes List';
				$path_object    = get_page_by_path( '/add-to-quote-product-list' );
				if ( is_object( $path_object ) ) {
					$my_post = array(
						'ID'         => $path_object->ID,
						'post_title' => $raq_page_title,
					);
					wp_update_post( $my_post );
				}
			}
		);
	}

	/** Add Address Validation settings tab to the settings page.
	 *
	 * @param array $settings_tabs Settings Tabs.
	 */
	public function elex_request_a_quote_add_settings_tab( $settings_tabs ) {
		$settings_tabs['elex_request_a_quote'] = __( 'Request a Quote', 'ELEX_RAQ_PLUGIN_DOMAIN' );
		return $settings_tabs;
	}

	/** To add sections to a tab. */
	public function get_sections() {
		$sections = array(
			''                      => __( 'General', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
			'form-settings'         => __( 'Form Settings', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
			'notification-settings' => __( 'Notifications', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
			'hide-add-to-cart'      => __( 'Hide Add to Cart', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
			'elex-go-premium'  => __( 'Go Premium', 'ELEX_RAQ_PLUGIN_DOMAIN' )
		);
		return apply_filters( 'woocommerce_get_sections_elex_request_a_quote', $sections );
	}
	public function output_sections() {
		global $current_section;
		$sections = $this->get_sections();
		if ( empty( $sections ) || 1 === count( $sections ) ) {
			return;
		}
		echo '<ul class="subsubsub">';
		$array_keys = array_keys( $sections );
		foreach ( $sections as $id => $label ) {
			if ('Go Premium' ==  $label ) {
				echo '<li><a href="' . esc_attr( admin_url( 'admin.php?page=wc-settings&tab=elex_request_a_quote&section=' . sanitize_title( $id ) )) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '"> <li><strong><font color="green">' . esc_attr( $label ) . '</font></strong></li></a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			} else {
				echo '<li><a href="' . esc_attr( admin_url( 'admin.php?page=wc-settings&tab=elex_request_a_quote&section=' . sanitize_title( $id ) )) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . esc_attr( $label ) . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
			}
			
		}
		echo '</ul><br class="clear" />';
	}

	/** Function to change section when clicked on them. */
	public function elex_request_a_quote_output_section() {
		global $current_section;
		if ( '' === $current_section ) {
			$settings = $this->elex_request_a_quote_general_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
		if ( 'form-settings' === $current_section ) {
			include_once 'templates/form-settings-template.php';
		}
		if ( 'notification-settings' === $current_section ) {
			$settings = $this->elex_request_a_quote_notification_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
		if ( 'hide-add-to-cart' === $current_section ) {
			$settings = $this->elex_request_a_quote_advanced_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
		if ( 'elex-go-premium' === $current_section ) {	
			
			wp_enqueue_style( 'elex-bootstrap', plugins_url( '../assets/css/elex-market-styles.css', __FILE__ ), false, true );	
			include 'market.php';	
		}
	}

	/** Function to update settings.
	 *
	 * @param string $current_section Current Section.
	 */
	public function elex_request_a_quote_update_section( $current_section ) {
		global $current_section;
		if ( '' === $current_section ) {
			$options = $this->elex_request_a_quote_general_settings( $current_section );
			woocommerce_update_options( $options );
		}
		if ( 'notification-settings' === $current_section ) {
			$options = $this->elex_request_a_quote_notification_settings( $current_section );
			woocommerce_update_options( $options );
		}
		if ( 'form-settings' === $current_section ) {
			$this->elex_request_a_quote_form_settings();
		}
		if ( 'hide-add-to-cart' === $current_section ) {
			$options = $this->elex_request_a_quote_advanced_settings( $current_section );
			woocommerce_update_options( $options );
		}
	}

	/** General settings. */
	public function elex_request_a_quote_general_settings() {
		global $wpdb;
		$page_list    = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_title, guid FROM $wpdb->posts WHERE post_type = %s AND post_status = %s",
				'page',
				'publish'
			)
		);
		$page_options = array();
		foreach ( $page_list as $k => $v ) {
			$page_guid = $v->guid;
			if ( ! preg_match( '/(\/quote-received-page\/|\/cart\/|\/shop\/|\/my-account\/|\/checkout\/)/i', $page_guid ) ) { // Avoid paths listed in the expression.
				$page_options[ $page_guid ] = $v->post_title;
			}
		}

		global $woocommerce;
		$settings = array(
			'section_title'             => array(
				'name' => '',
				'type' => 'title',
			),

			'raq_button_text'           => array(
				'title'       => __( 'Add to Quote Button Text', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'        => 'text',
				'placeholder' => __( 'Add To Quote', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'desc'        => __( 'Enter the text to be displayed on the Add To Quote button', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'          => 'elex_request_a_quote_settings[raq_button_text]',
				'desc_tip'    => true,
			),

			'raq_button_color'          => array(
				'title'    => __( 'Add to Quote Button Color', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'color',
				'css'      => 'width:22.5rem;',
				'desc'     => __( 'Choose the color of the Add To Quote button', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_button_color]',
				'desc_tip' => true,
			),

			'raq_button_action'         => array(
				'title'    => __( 'Add to Quote Button Action', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'select',
				'desc'     => __( 'Choose the action to be performed when the customer clicks on the add to quote button.', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_button_action]',
				'options'  => array(
					'raq_page' => __( 'Open Request a Quote Page', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
					'lightbox' => __( 'Open in a Lightbox', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				),
				'desc_tip' => true,
				'class'    => 'elex_raq_add_to_quote_button_action_select',
			),

			'raq_lightbox_width'        => array(
				'title'       => __( 'Lightbox Width (in px)', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'        => 'number',
				'placeholder' => __( '600', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'desc'        => __( 'Enter Lightbox Width', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'          => 'elex_request_a_quote_settings[raq_lightbox_width]',
				'desc_tip'    => true,
				'class'       => 'elex_raq_lightbox_width',
			),

			'raq_lightbox_height'       => array(
				'title'       => __( 'Lightbox Height (in px)', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'        => 'number',
				'placeholder' => __( '550', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'desc'        => __( 'Enter Lightbox Height', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'          => 'elex_request_a_quote_settings[raq_lightbox_height]',
				'desc_tip'    => true,
				'class'       => 'elex_raq_lightbox_height',
			),

			'raq_quote_list_page'       => array(
				'title'    => __( 'Choose Quote List Page', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'select',
				'default'  => '/add-to-quote-product-list/',
				'desc'     => __( 'Choose the page from this list on which customers will see their quote requests.<br>Please note: If you choose a page different from the default one (Quotes List), you will need to insert the following shortcode [elex_quote_request_list] on that page', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_quote_list_page]',
				'options'  => $page_options,
				'desc_tip' => true,
			),

			'raq_quote_list_page_title' => array(
				'title'       => __( 'Quotes List Page Title', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'        => 'text',
				'placeholder' => __( 'Quotes List', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'desc'        => __( 'Product Quote List Title', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'          => 'elex_request_a_quote_settings[raq_quote_list_page_title]',
				'desc_tip'    => true,
			),

			'section_end'               => array(
				'type' => 'sectionend',
			),
		);
		return apply_filters( 'elex_request_a_quote_general_settings', $settings );
	}

	/** Form Setings. */
	public function elex_request_a_quote_form_settings() {

		if (isset($_POST['nounce_verify'])) {
			$verify = wp_verify_nonce( sanitize_text_field( $_POST['nounce_verify']), 'form_data');
		}
		if ( isset( $_POST['elex_request_a_quote_field_adjustment_options'] ) && $verify ) {
			update_option( 'elex_request_a_quote_field_adjustment_options', array_values( wp_unslash( $_POST['elex_request_a_quote_field_adjustment_options'] ) ) );
		}
	}

	/** Notification Setings. */
	public function elex_request_a_quote_notification_settings() {
		$settings = array(
			'section_title'                            => array(
				'name' => '',
				'type' => 'title',
			),
			'raq_notification_email'                   => array(
				'title'       => __( 'Notification Email Address', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'        => 'textarea',
				'placeholder' => get_option( 'admin_email' ),
				'desc'        => __( 'Enter the email addresses to receive a notification when the customer places a quote request. Leave it empty to send a notification to the default administrator email id. Multiple email ids should be seperated by comma.', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'          => 'elex_request_a_quote_settings[raq_notification_email]',
				'desc_tip'    => true,
			),
			'raq_customer_notification_order_statuses' => array(
				'title'    => __( 'Select Customer Notification Order Statuses', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'multiselect',
				'class'    => 'chosen_select wf_address_autocomplete_validation_restrict_countries',
				'desc_tip' => true,
				'desc'     => __( 'Select the order statuses for which customers should receive an email notification.', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_customer_notification_order_statuses]',
				'css'      => 'width: 300px;',
				'default'  => '',
				'options'  => array(
					'quote_requested' => __( 'Quote Requested', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
					'quote_approved'  => __( 'Quote Approved', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
					'quote_rejected'  => __( 'Quote Rejected', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				),
			),

			'raq_notification_debug_log'               => array(
				'title'    => __( 'Debug Log', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'desc_tip' => __( 'Find email logs here (wp-content\uploads\wc-logs).', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_notification_debug_log]',
				'desc'     => __( 'Enable.', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
			),

			'section_end'                              => array(
				'type' => 'sectionend',
			),
		);
		return apply_filters( 'elex_request_a_quote_notification_settings', $settings );
	}

	/** General settings. */
	public function elex_request_a_quote_advanced_settings() {
		global $woocommerce, $wp_roles;

		$user_roles                 = $wp_roles->role_names;
		$user_roles['unregistered'] = 'Unregistered';

		// Get Product tags.
		$tag_terms = get_terms( 'product_tag' );
		$tag_array = array();
		foreach ( $tag_terms as $index => $term_obj ) {
			$tag_array[ $term_obj->slug ] = $term_obj->name;
		}

		// Get Product Name.
		$product_args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
		);
		$loop         = new WP_Query( $product_args );
		$name_array   = array();
		while ( $loop->have_posts() ) :
			$loop->the_post();
			global $product;
			$name_array[ $product->get_id() ] = $product->get_name();
		endwhile;
		wp_reset_postdata();

		// Get Product Category.
		$category_array = array();
		$category_terms = get_terms( 'product_cat' );
		foreach ( $category_terms as $index => $term_obj ) {
			$category_array[ $term_obj->slug ] = $term_obj->name;
		}

		$settings = array(
			'section_title'                       => array(
				'name' => '',
				'type' => 'title',
				'desc' => __('Here you can apply restrictions to the Add to cart functionality.', 'ELEX_RAQ_PLUGIN_DOMAIN'),
			),

			'raq_remove_add_to_cart'              => array(
				'title'    => __( 'Hide Add to Cart', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'type'     => 'checkbox',
				'default'  => 'no',
				'desc'     => __( 'Enable', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'       => 'elex_request_a_quote_settings[raq_remove_add_to_cart]',
				'desc_tip' => __( 'Check to hide Add to Cart Button.', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'class'    => 'elex_raq_remove_add_to_cart',
			),

			'raq_remove_add_to_cart_shop_page'    => array(
				'desc'          => __( 'Shop Page', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'            => 'elex_request_a_quote_settings[raq_remove_add_to_cart_shop_page]',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'start',
				'autoload'      => false,
				'class'         => 'elex_raq_remove_add_to_cart_product_shop_page',
			),

			'raq_remove_add_to_cart_product_page' => array(
				'desc'          => __( 'Product Page', 'ELEX_RAQ_PLUGIN_DOMAIN' ),
				'id'            => 'elex_request_a_quote_settings[raq_remove_add_to_cart_product_page]',
				'default'       => 'yes',
				'type'          => 'checkbox',
				'checkboxgroup' => 'end',
				'autoload'      => false,
				'class'         => 'elex_raq_remove_add_to_cart_product_shop_page',
			),

			'section_end'                         => array(
				'name' => '',
				'type' => 'sectionend',
			),
		);
		return apply_filters( 'elex_request_a_quote_advanced_settings', $settings );
	}

	/** Output the tab content. */
	public function elex_request_a_quote_tab_content() {
		wc_enqueue_js(
			"
			jQuery('.elex_raq_remove_add_to_cart').on('change',function() {
				if(jQuery('.elex_raq_remove_add_to_cart').is(':checked')) {
					jQuery('.elex_raq_remove_add_to_cart_product_shop_page').closest('tr').show();
				}
				else{
					jQuery('.elex_raq_remove_add_to_cart_product_shop_page').closest('tr').hide();
				}
			}).change();
			jQuery('.elex_raq_add_to_quote_button_action_select').on('change',function() {
				if(jQuery('.elex_raq_add_to_quote_button_action_select').val() == 'lightbox') {
					jQuery('.elex_raq_lightbox_height').closest('tr').show();
					jQuery('.elex_raq_lightbox_width').closest('tr').show();
				}
				else{
					jQuery('.elex_raq_lightbox_height').closest('tr').hide();
					jQuery('.elex_raq_lightbox_width').closest('tr').hide();
				}
			}).change();
			"
		);
	}
}
