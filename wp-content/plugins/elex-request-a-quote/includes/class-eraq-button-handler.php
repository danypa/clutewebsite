<?php
/**
 *
 * File to handle button settings.
 *
 * @package Elex Request a Quote
 */

/** Button Handler. */
class Eraq_Button_Handler {
	/**
	 * Array of settings stored in database.
	 *
	 * @var array $all_settings Array of settings stored in database.
	 */
	protected $all_settings;

	/** Constructor */
	public function __construct() {
		$this->all_settings                      = get_option( 'elex_request_a_quote_settings' );
		$this->request_a_quote_button_text       = ! empty( $this->all_settings['raq_button_text'] ) ? $this->all_settings['raq_button_text'] : 'Add to Quote';
		$this->request_a_quote_button_text_tran	 = __('Add to Quote', 'ELEX_RAQ_PLUGIN_DOMAIN');
		$this->request_a_quote_button_color      = ! empty( $this->all_settings['raq_button_color'] ) ? "background-color:{$this->all_settings['raq_button_color']};" : '';
		$this->request_a_quote_page_redirect_url = ! empty( $this->all_settings['raq_quote_list_page'] ) ? $this->all_settings['raq_quote_list_page'] : '/add-to-quote-product-list';
		$this->request_a_quote_button_action     = ! empty( $this->all_settings['raq_button_action'] ) ? $this->all_settings['raq_button_action'] : 'raq_page';
		$this->lightbox_width                    = ! empty( $this->all_settings['raq_lightbox_width'] ) ? $this->all_settings['raq_lightbox_width'] : 600;
		$this->lightbox_height                   = ! empty( $this->all_settings['raq_lightbox_height'] ) ? $this->all_settings['raq_lightbox_height'] : 550;
		add_action( 'woocommerce_product_meta_start', array( $this, 'elex_request_a_quote_button_product_page' ) );
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'elex_request_a_quote_button_shop_page' ) );
		add_action( 'woocommerce_before_single_variation', array( $this, 'elex_single_variation_selected' ) );
		add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'elex_conditional_add_to_cart_shop_page' ), 10, 2 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'elex_conditional_add_to_cart_product_page' ) );
	}
	/** Conditional Add to Cart Product Page. */
	public function elex_conditional_add_to_cart_product_page() {
		global $post;
		$product = wc_get_product( $post->ID );

		$is_product_page_checked = ! empty( $this->all_settings['raq_remove_add_to_cart_product_page'] ) ? $this->all_settings['raq_remove_add_to_cart_product_page'] : 'no';
		if ( isset( $this->all_settings['raq_remove_add_to_cart'] ) && 'yes' === $this->all_settings['raq_remove_add_to_cart'] ) {
			if ( isset( $this->all_settings['raq_remove_add_to_cart_product_page'] ) && 'yes' === $this->all_settings['raq_remove_add_to_cart_product_page'] ) {
				if ($product->get_type()=='variable') {

					remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
		
				} else {
					wc_enqueue_js(
						"
						jQuery('button[name=add-to-cart]').remove();
						jQuery('button.single_add_to_cart_button:nth-of-type(2)').remove();
					"
					);
				}
			}
		}
	}

	/** Conditional Add to Cart Shop Page.
	 *
	 * @param var $add_to_cart_html add to cart html.
	 * @param var $product product.
	 */
	public function elex_conditional_add_to_cart_shop_page( $add_to_cart_html, $product ) {
		if ( isset( $this->all_settings['raq_remove_add_to_cart'] ) && 'yes' === $this->all_settings['raq_remove_add_to_cart'] ) {
			if ( isset( $this->all_settings['raq_remove_add_to_cart_shop_page'] ) && 'yes' === $this->all_settings['raq_remove_add_to_cart_shop_page'] ) {
				return '';
			}
		}
		return $add_to_cart_html;
	}

	/** Single Variation Selected. */
	public function elex_single_variation_selected() {
		wc_enqueue_js(
			"(function($){
			$('form.variations_form').on('show_variation', function(event, data){
				localStorage.setItem('currently_selected_variation_id', data.variation_id);
				//Enable quote button when variation is selected
				$('.request-a-quote-button' ).removeClass('disabled');
				$('.request-a-quote-button' ).css('opacity','');
				$('.request-a-quote-button' ).removeAttr('disabled');	
			});
			//Disable quote button when reset variation is triggered
			$('.reset_variations').click(function() {
				$('.request-a-quote-button').addClass('disabled');
				$('.request-a-quote-button').css('opacity','0.5');
				$('.request-a-quote-button').attr('disabled', true); 
			});
			})(jQuery);"
		);
	}

	/** Function to handle working of Request a Quote Button in Product Page. */
	public function elex_request_a_quote_button_product_page() {

		global $product;
		

		if ( 'raq_page' === $this->request_a_quote_button_action ) {
			if ( $product->get_type() !== 'variable' ) {

				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button button' onclick='add_to_quote_product_shop_page_simple_external_products(" . esc_attr( $product->get_id() ) . ", event);' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';

			} else {
				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button single_add_to_cart_button button alt disabled' onclick='add_to_cart_product_page_variable_products(" . esc_attr( $product->get_id() ) . ", event);' style=" . esc_attr( $this->request_a_quote_button_color ) . 'opacity:0.5; disabled>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';

			}
		} else {
			if ( $product->get_type() !== 'variable' ) {

				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button button ' onclick='add_to_quote_product_shop_page_simple_external_products(" . esc_attr( $product->get_id() ) . ", event);' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link thickbox' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '?TB_iframe=true&width=' . esc_attr( $this->lightbox_width ) . '&height=' . esc_attr( $this->lightbox_height ) . '>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';

			} else {
				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button single_add_to_cart_button button alt disabled' onclick='add_to_cart_product_page_variable_products(" . esc_attr( $product->get_id() ) . ", event);' style=" . esc_attr( $this->request_a_quote_button_color ) . 'opacity:0.5; disabled>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link thickbox' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '?TB_iframe=true&width=' . esc_attr( $this->lightbox_width ) . '&height=' . esc_attr( $this->lightbox_height ) . '>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';
			}
		}
	}


	/** Function to handle working of Request a Quote Button in Shop Page. */
	public function elex_request_a_quote_button_shop_page() {

		global $product;

		if ( 'raq_page' === $this->request_a_quote_button_action ) {
			if ( $product->get_type() !== 'variable' ) {

				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button button' onclick='add_to_quote_product_shop_page_simple_external_products(" . esc_attr( $product->get_id() ) . ");' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '/>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';
			} else {

				echo '<a href=' . esc_attr( get_permalink( $product->get_id() ) ) . '><button id=' . esc_attr( $product->get_id() ) . " class='single_add_to_cart_button' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html('Select Options', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</button></a>';

			}
		} else { // Open Lightbox.
			add_thickbox();
			if ( $product->get_type() !== 'variable' ) {
				echo '<button id=' . esc_attr( $product->get_id() ) . " class='request-a-quote-button button' onclick='add_to_quote_product_shop_page_simple_external_products(" . esc_attr( $product->get_id() ) . ");' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html( $this->request_a_quote_button_text, 'ELEX_RAQ_PLUGIN_DOMAIN' ) . "</button><a class='add_to_quote_clicked_message_link thickbox' style='display:none;' href=" . esc_attr( $this->request_a_quote_page_redirect_url ) . '?TB_iframe=true&width=' . esc_attr( $this->lightbox_width ) . '&height=' . esc_attr( $this->lightbox_height ) . '>' . esc_html('View the quote list', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</a>';
			} else {
				echo '<a href=' . esc_attr( get_permalink( $product->get_id() ) ) . '><button id=' . esc_attr( $product->get_id() ) . " class='single_add_to_cart_button' style=" . esc_attr( $this->request_a_quote_button_color ) . '>' . esc_html('Select Options', 'ELEX_RAQ_PLUGIN_DOMAIN') . '</button></a>';
			}
		}
	}
}
