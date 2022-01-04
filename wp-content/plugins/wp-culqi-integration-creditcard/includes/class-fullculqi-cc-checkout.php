<?php
class FullCulqi_CcCheckout {

	public function __construct() {

		if( !FullCulqi_CcLicense::is_active() )
			return;
		
		add_action('woocommerce_checkout_order_processed', [$this, 'do_payment'], 10, 3);
	}

	public function do_payment($order_id, $posted_data, $order) {

		if( $order->get_payment_method() != 'fullculqi' )
			return;

		$method = fullculqi_get_woo_settings();

		if( !isset($method['oclick_type']) )
			return;

		// Logs
		$log = new FullCulqi_Logs();
		$log->set_settings_payment($order_id);

		switch($method['oclick_type']) {
			case 'modal' :
				$token_id 		= isset($_POST['fullculqi_token_id']) ? sanitize_text_field($_POST['fullculqi_token_id']) : '';
				$installments 	= isset($_POST['fullculqi_installments']) && !empty($_POST['fullculqi_installments']) ? intval($_POST['fullculqi_installments']) : 0;
				break;

			case 'form' :
				$billing_email = sanitize_email($order->get_billing_email());

				$card_expire = explode( '/', $_POST['fullculqi-card-expiry'] );
				list($card_month, $card_year) = array_map('intval', array_map( 'trim', $card_expire ) );

				$card_number = esc_html( trim( str_replace( ' ', '', $_POST['fullculqi-card-number'] ) ) );
				$card_cvc	= esc_html( trim( $_POST['fullculqi-card-cvc'] ) );

				$args = [
							'card_number'		=> $card_number,
							'cvv'				=> $card_cvc,
							'email'				=> $billing_email,
							'expiration_month'	=> $card_month,
							'expiration_year'	=> $card_year,
							'fingerprint'		=> uniqid(),
						];

				$provider_token = FullCulqi_Provider::create_token($args);

				if( $provider_token['status'] == 'ok' ) {
					$token_id = $provider_token['data']->id;
					$installments = 0;
					$country_code = $provider_token['data']->client->ip_country_code;
					
					$log->set_msg_payment('notice', sprintf(__('Culqi Token created: %s','fullculqi'), $token_id ) );

				} else {
					$log->set_msg_payment('error', sprintf(__('Culqi Provider Token error : %s','fullculqi'), $provider_token['msg'] ) );
				}
			break;
		}

		if( !isset($token_id) || empty($token_id) )
			return;

		$provider_payment = [];

		if( apply_filters('fullculqi/do_payment/conditional', false, $order, $log) ) {
					
			$provider_payment = apply_filters('fullculqi/do_payment/create', $provider_payment, compact('token_id', 'installments', 'country_code'), $log, $order);

		} else {

			$provider_payment = FullCulqi_Checkout::simple($order, compact('token_id', 'installments'), $log );
		}


		// If empty
		if( count($provider_payment) == 0 ) {

			//$order->update_meta_data( '_fullculqi_cc_error', 1 );
			update_post_meta($order_id, '_fullculqi_cc_error', 1);

			$log->set_msg_payment('error', __('Culqi Provider Payment error : There is not set a type payment','fullculqi') );

		} else {

			if( $provider_payment['status'] == 'ok' ) {
				update_post_meta($order_id, '_fullculqi_cc_success', 1);
			
			} else {
				update_post_meta($order_id, '_fullculqi_cc_error', 1);
				wc_add_notice( $method['msg_fail'], 'error' );

			}
		}

	}
}