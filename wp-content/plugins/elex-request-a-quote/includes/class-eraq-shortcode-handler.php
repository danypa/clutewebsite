<?php
/**
 *
 * File to implement shortcode.
 *
 * @package Elex Request a Quote
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Shortcode Handler. */
class Eraq_Shortcode_Handler {
	/**
	 * Array of settings stored in database.
	 *
	 * @var array $all_settings Array of settings stored in database.
	 */
	protected $all_settings;

	/** Constructor */
	public function __construct() {

		$this->all_settings = get_option( 'elex_request_a_quote_settings' );
		add_action( 'wp_enqueue_scripts', array( $this, 'eraq_shortcode_handler_scripts' ) );
		// Register shortcode.
		add_shortcode( 'elex_quote_request_list', array( $this, 'elex_quote_request_list_shortcode' ) );
		add_shortcode( 'elex_quote_received_page', array( $this, 'elex_quote_received_page_shortcode' ) );
		add_action( 'wp_ajax_eraq_update_cart_contents', array( $this, 'eraq_update_cart_contents' ) );
		add_action( 'wp_ajax_nopriv_eraq_update_cart_contents', array( $this, 'eraq_update_cart_contents' ) );
		add_action( 'wp_ajax_eraq_place_order', array( $this, 'eraq_place_order' ) );
		add_action( 'wp_ajax_nopriv_eraq_place_order', array( $this, 'eraq_place_order' ) );
	}



	/** Place Order. */
	public function eraq_place_order() {
		check_ajax_referer( 'eraq_nonce' );
		global $woocommerce;
		if ( ! empty( $_POST['cart_item'] ) ) {
			$current_user_id = get_current_user_id();
			parse_str( wp_unslash( $_POST['cart_item'] ), $formdata ); // This will convert the string to array.
			$address = array();
			if ( isset( $formdata['billing_first_name'] ) ) {
				$address['first_name'] = $formdata['billing_first_name'];
			}
			if ( isset( $formdata['billing_last_name'] ) ) {
				$address['last_name'] = $formdata['billing_last_name'];
			}
			if ( isset( $formdata['billing_company'] ) ) {
				$address['company'] = $formdata['billing_company'];
			}
			if ( isset( $formdata['billing_email'] ) ) {
				$address['email'] = $formdata['billing_email'];
			}
			if ( isset( $formdata['billing_phone'] ) ) {
				$address['phone'] = $formdata['billing_phone'];
			}
			if ( isset( $formdata['billing_address_1'] ) ) {
				$address['address_1'] = $formdata['billing_address_1'];
			}
			if ( isset( $formdata['billing_address_2'] ) ) {
				$address['address_2'] = $formdata['billing_address_2'];
			}
			if ( isset( $formdata['billing_city'] ) ) {
				$address['city'] = $formdata['billing_city'];
			}
			if ( isset( $formdata['billing_state'] ) ) {
				$address['state'] = $formdata['billing_state'];
			}
			if ( isset( $formdata['billing_postcode'] ) ) {
				$address['postcode'] = $formdata['billing_postcode'];
			}
			if ( isset( $formdata['billing_country'] ) ) {
				$address['country'] = $formdata['billing_country'];
			}

			$order = wc_create_order();
			if ( 0 !== $current_user_id ) {
				$order->set_customer_id( $current_user_id );
			}
			$order->set_address( $address, 'billing' );
			$order->set_address( $address, 'shipping' );
			if ( isset( $formdata['order_comments'] ) ) {
				$order->add_order_note( $formdata['order_comments'] );
			}

			if ( 0 === $current_user_id ) { // Unregistered user.

				$unregistered_user_cart_items = get_option( 'elex_raq_unregistered_users_cart_array' );
				$ip                           = $this->get_ip_address();
				$ip_hash                      = hash( 'ripemd160', $ip );
				foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {
					if ( ! empty( $value['product_id'] ) && ! empty( $value['variation_id'] ) && ! empty( $value['Qty'] ) ) { // Product variation exists in quote cart, update quantity.

						$product_variation = new WC_Product_Variation( $value['variation_id'] );
						if ( ! empty( $product_variation ) ) { // If product is removed from shop dont add it to order.
							$args = array();
							foreach ( $product_variation->get_variation_attributes() as $attribute => $attribute_value ) {
									$args['variation'][ $attribute ] = $attribute_value;
							}
							$order->add_product( $product_variation, $value['Qty'], $args );
						}
					} elseif ( ! empty( $value['product_id'] ) && ! empty( $value['Qty'] ) ) {
						$product_simple_external = wc_get_product( $value['product_id'] );
						if ( ! empty( $product_simple_external ) ) { // If product is removed from shop dont add it to order.
							$order->add_product( $product_simple_external, $value['Qty'] );
						}
					}
				}
				$order->calculate_totals();
				$order->update_status( 'quote-requested', 'Quote Requested', true );
				unset( $unregistered_user_cart_items[ $ip_hash ] );
				update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
				die( 'Success' );
			} else {
				$registered_user_cart_items = get_option( 'elex_raq_registered_users_cart_array' );
				foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {
					if ( ! empty( $value['product_id'] ) && ! empty( $value['variation_id'] ) && ! empty( $value['Qty'] ) ) { // Product variation exists in quote cart, update quantity.

						$product_variation = new WC_Product_Variation( $value['variation_id'] );
						if ( ! empty( $product_variation ) ) {
							$args = array();
							foreach ( $product_variation->get_variation_attributes() as $attribute => $attribute_value ) {
									$args['variation'][ $attribute ] = $attribute_value;
							}
							$order->add_product( $product_variation, $value['Qty'], $args );
						}
					} elseif ( ! empty( $value['product_id'] ) && ! empty( $value['Qty'] ) ) {
						$product_simple_external = wc_get_product( $value['product_id'] );
						if ( ! empty( $product_simple_external ) ) { // If product is removed from shop dont add it to order.
							$order->add_product( $product_simple_external, $value['Qty'] );
						}
					}
				}
				$order->calculate_totals();
				$order->update_status( 'quote-requested', 'Quote Requested', true );
				unset( $registered_user_cart_items[ $current_user_id ] );
				update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
				die( 'Success' );
			}
		}

	}

	/** Update Cart Contents. */
	public function eraq_update_cart_contents() {
		check_ajax_referer( 'eraq_nonce' );
		$current_user_id = get_current_user_id();
		if ( ! empty( $_POST['cart_item'] ) ) {
			$posted_cart_item = array_map( 'sanitize_text_field', wp_unslash( $_POST['cart_item'] ) );
			if ( 'delete' === $posted_cart_item['operation'] ) {
				if ( 0 === $current_user_id ) { // Unregistered user.

					$unregistered_user_cart_items = get_option( 'elex_raq_unregistered_users_cart_array' );
					$ip                           = $this->get_ip_address();
					$ip_hash                      = hash( 'ripemd160', $ip );

					if ( isset( $unregistered_user_cart_items[ $ip_hash ] ) && ! empty( $unregistered_user_cart_items[ $ip_hash ] ) ) { // Already has items in cart so push the new cart item.

						if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

							foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {
								if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.

									array_splice( $unregistered_user_cart_items[ $ip_hash ], $key, 1 );
									update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
									die( 'Success' );

								}
							}
						} else { // It is a simple/external product.

							foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.

									array_splice( $unregistered_user_cart_items[ $ip_hash ], $key, 1 );
									update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
									die( 'Success' );

								}
							}
						}
					}
				} else { // Registered user.

					$registered_user_cart_items = get_option( 'elex_raq_registered_users_cart_array' );
					if ( isset( $registered_user_cart_items[ $current_user_id ] ) && ! empty( $registered_user_cart_items[ $current_user_id ] ) ) { // Already has items in cart so push the new cart item.

						if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

							foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.
									array_splice( $registered_user_cart_items[ $current_user_id ], $key, 1 );
									update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
									die( 'Success' );

								}
							}
						} else { // It is a simple/external product.

							foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.
									array_splice( $registered_user_cart_items[ $current_user_id ], $key, 1 );
									update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
									die( 'Success' );

								}
							}
						}
					}
				}
			} elseif ( 'update' === $posted_cart_item['operation'] ) {
				if ( 0 === $current_user_id ) { // Unregistered user.

					$unregistered_user_cart_items = get_option( 'elex_raq_unregistered_users_cart_array' );
					$ip                           = $this->get_ip_address();
					$ip_hash                      = hash( 'ripemd160', $ip );

					if ( isset( $unregistered_user_cart_items[ $ip_hash ] ) && ! empty( $unregistered_user_cart_items[ $ip_hash ] ) ) { // Already has items in cart so push the new cart item.

						if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

							foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {
								if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.

									$unregistered_user_cart_items[ $ip_hash ][ $key ]['Qty'] = $posted_cart_item['current_quantity'];
									update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
									die( 'Success' );

								}
							}
						} else { // It is a simple/external product.

							foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.

									$unregistered_user_cart_items[ $ip_hash ][ $key ]['Qty'] = $posted_cart_item['current_quantity'];
									update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
									die( 'Success' );

								}
							}
						}
					}
				} else { // Registered user.

					$registered_user_cart_items = get_option( 'elex_raq_registered_users_cart_array' );
					if ( isset( $registered_user_cart_items[ $current_user_id ] ) && ! empty( $registered_user_cart_items[ $current_user_id ] ) ) { // Already has items in cart so push the new cart item.

						if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

							foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.

									$registered_user_cart_items[ $current_user_id ][ $key ]['Qty'] = $posted_cart_item['current_quantity'];
									update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
									die( 'Success' );

								}
							}
						} else { // It is a simple/external product.

							foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

								if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.

									$registered_user_cart_items[ $current_user_id ][ $key ]['Qty'] = $posted_cart_item['current_quantity'];
									update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
									die( 'Success' );

								}
							}
						}
					}
				}
			}
		}
	}

	/** Shortcode Handler Scripts. */
	public function eraq_shortcode_handler_scripts() {
		wp_enqueue_script( 'raq_shortcode_handler_scripts', plugins_url( '../assets/js/raq_shortcode_handler_scripts.js', __FILE__ ), array( 'jquery' ), time(), true );
		$decimal = wc_get_price_decimals();
		//wp_enqueue_style('wp-jquery-ui-dialog');
		wp_localize_script(
			'raq_shortcode_handler_scripts',
			'eraq_shortcode_handler_scripts_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'eraq_nonce' ),
				'round_price_value' => $decimal,
			)
		);
	}

	/** Function to get ip address. */
	public function get_ip_address() {

		// whether ip is from the share internet.
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = filter_var( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ), FILTER_VALIDATE_IP );
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // whether ip is from the proxy.
			$ip = filter_var( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ), FILTER_VALIDATE_IP );
		} elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) { // whether ip is from the remote address.
			$ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );
		}
		return $ip;

	}

	/** Function that runs when shortcode is called.  */
	public function elex_quote_request_list_shortcode() {
		$cart_items = array();

		// Get whether the user is registered or unregistered.
		$current_user_id = get_current_user_id();
		if ( 0 === $current_user_id ) { // Unregistered user.
			$unregistered_user_cart_items = get_option( 'elex_raq_unregistered_users_cart_array' );
			$ip                           = $this->get_ip_address();
			$ip_hash                      = hash( 'ripemd160', $ip );
			if ( isset( $unregistered_user_cart_items[ $ip_hash ] ) && ! empty( $unregistered_user_cart_items[ $ip_hash ] ) ) {
				$cart_items = $unregistered_user_cart_items[ $ip_hash ];
			}
		} else { // Registered user.
			$registered_user_cart_items = get_option( 'elex_raq_registered_users_cart_array' );
			if ( isset( $registered_user_cart_items[ $current_user_id ] ) && ! empty( $registered_user_cart_items[ $current_user_id ] ) ) {
				$cart_items = $registered_user_cart_items[ $current_user_id ];
			}
		}
		ob_start();
		include_once 'templates/shortcode-template.php';
		$content = ob_get_clean();
		return $content;
	}

	/** Quote Received Page Shortcode. */
	public function elex_quote_received_page_shortcode() {
		return '<h3>Your request has been sent successfully</h3><center><a href=' . get_permalink( wc_get_page_id( 'shop' ) ) . '>< Return to shop</a></center>';
	}
}
