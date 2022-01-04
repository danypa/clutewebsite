<?php
class FullCulqi_CcAjax {

	public function __construct() {

		add_action('wp_ajax_fullculqi_validated_fields', [ $this, 'validated_fields'] );
		add_action('wp_ajax_nopriv_fullculqi_validated_fields', [ $this, 'validated_fields']);
	}

	public function validated_fields() {

		wc_maybe_define_constant( 'WOOCOMMERCE_CHECKOUT', true );

		$checkout = new FullCulqi_CcWoocommerce();

		$errors      = new WP_Error();
		$posted_data = $checkout->get_posted_data();

		// Update session for customer and totals.
		$checkout->WooUpdateSession( $posted_data );
		
		// Validate posted data and cart items before proceeding.
		$checkout->WooValidateCheckout( $posted_data, $errors );

		if( ( method_exists($errors, 'has_errors') && $errors->has_errors() ) ||
			( method_exists($errors, 'get_error_codes') && count( $errors->get_error_codes() ) > 0 )
		) {

			foreach ( $errors->get_error_messages() as $message )
				wc_add_notice( $message, 'error' );

			$messages = wc_print_notices( true );

			$response = array(
				'result'   => 'failure',
				'messages' => isset( $messages ) ? $messages : '',
			);

			wp_send_json( $response );
		}

		$response = array('result' => true);
		wp_send_json( $response );

		die();
	}
}