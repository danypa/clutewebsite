<?php
/**
 *
 * File to implement quote cart functionality.
 *
 * @package Elex Request a Quote
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  Cart Handler Class.
 */
class Eraq_Cart_Handler {
	/**
	 * Array of settings stored in database.
	 *
	 * @var array $all_settings Array of settings stored in database.
	 */
	protected $all_settings;

	/** Constructor */
	public function __construct() {
		$this->all_settings = get_option( 'elex_request_a_quote_settings' );
		add_action( 'wp_ajax_eraq_cart_contents', array( $this, 'eraq_cart_contents' ) );
		add_action( 'wp_ajax_nopriv_eraq_cart_contents', array( $this, 'eraq_cart_contents' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'raq_add_quote_scripts' ) );
	}

	/** Enqueue add to quote scripts. */
	public function raq_add_quote_scripts() {

		wp_enqueue_script( 'raq_add_quote_scripts', plugins_url( '../assets/js/raq_add_to_quote_scripts.js', __FILE__ ), array( 'jquery' ), time(), true );
		wp_localize_script(
			'raq_add_quote_scripts',
			'elex_request_a_quote_scripts_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'eraq_nonce' ),
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

	/** Manage Add to cart data. */
	public function eraq_cart_contents() {

		check_ajax_referer( 'eraq_nonce' );
		$current_user_id  = get_current_user_id();
		$posted_cart_item = ! empty( $_POST['cart_item'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['cart_item'] ) ) : array();
		if ( 0 === $current_user_id ) { // Unregistered user.

			$unregistered_user_cart_items = get_option( 'elex_raq_unregistered_users_cart_array' );
			$ip                           = $this->get_ip_address();
			$ip_hash                      = hash( 'ripemd160', $ip );

			if ( isset( $unregistered_user_cart_items[ $ip_hash ] ) && ! empty( $unregistered_user_cart_items[ $ip_hash ] ) ) { // Already has items in cart so push the new cart item.

				if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

					foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {
						if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.

							$unregistered_user_cart_items[ $ip_hash ][ $key ]['Qty'] += $posted_cart_item['Qty'];
							update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
							die( 'Success' );

						}
					}

					// Add product variation to quote cart.
					array_push( $unregistered_user_cart_items[ $ip_hash ], $posted_cart_item );
					update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
					die( 'Success' );

				} else { // It is a simple/external product.

					foreach ( $unregistered_user_cart_items[ $ip_hash ] as $key => $value ) {

						if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.

							$unregistered_user_cart_items[ $ip_hash ][ $key ]['Qty'] += $posted_cart_item['Qty'];
							update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
							die( 'Success' );

						}
					}

					// Add product to quote cart.
					array_push( $unregistered_user_cart_items[ $ip_hash ], $posted_cart_item );
					update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
					die( 'Success' );

				}
			} else { // Doesn't have any items in the cart, create a cart and save item.

				$unregistered_user_cart_items[ $ip_hash ] = array();
				array_push( $unregistered_user_cart_items[ $ip_hash ], $posted_cart_item );
				update_option( 'elex_raq_unregistered_users_cart_array', $unregistered_user_cart_items );
				die( 'Success' );

			}
		} else { // Registered user.

			$registered_user_cart_items = get_option( 'elex_raq_registered_users_cart_array' );
			if ( isset( $registered_user_cart_items[ $current_user_id ] ) && ! empty( $registered_user_cart_items[ $current_user_id ] ) ) { // Already has items in cart so push the new cart item.

				if ( ! empty( $posted_cart_item['variation_id'] ) ) { // It is a variable product.

					foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

						if ( $posted_cart_item['product_id'] === $value['product_id'] && $posted_cart_item['variation_id'] === $value['variation_id'] ) { // Product variation exists in quote cart, update quantity.

							$registered_user_cart_items[ $current_user_id ][ $key ]['Qty'] += $posted_cart_item['Qty'];
							update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
							die( 'Success' );

						}
					}

					// Add product variation to quote cart.
					array_push( $registered_user_cart_items[ $current_user_id ], $posted_cart_item );
					update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
					die( 'Success' );

				} else { // It is a simple/external product.

					foreach ( $registered_user_cart_items[ $current_user_id ] as $key => $value ) {

						if ( $posted_cart_item['product_id'] === $value['product_id'] ) { // Product exists in quote cart, update quantity.

							$registered_user_cart_items[ $current_user_id ][ $key ]['Qty'] += $posted_cart_item['Qty'];
							update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
							die( 'Success' );

						}
					}

					// Add product to quote cart.
					array_push( $registered_user_cart_items[ $current_user_id ], $posted_cart_item );
					update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
					die( 'Success' );

				}
			} else { // Doesn't have any items in the cart, create a cart and save item.

				$registered_user_cart_items[ $current_user_id ] = array();
				array_push( $registered_user_cart_items[ $current_user_id ], $posted_cart_item );
				update_option( 'elex_raq_registered_users_cart_array', $registered_user_cart_items );
				die( 'Success' );

			}
		}
	}
}
