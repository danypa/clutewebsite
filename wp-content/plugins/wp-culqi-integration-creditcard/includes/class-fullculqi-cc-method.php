<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FullCulqi_CcMethod {

	public function __construct() {

		if( !FullCulqi_CcLicense::is_active() )
			return;

		add_action( 'init', [ $this, 'init' ] );
	}


	public function init() {

		if ( ! class_exists( 'FullCulqi' ) )
			return;

		$method = fullculqi_get_woo_settings();

		add_filter( 'fullculqi/method/form_fields', [$this, 'add_admin_fields'] );

		if( !isset($method['oclick_type']) )
			return;

		add_filter( 'fullculqi/global/get_default', [$this, 'add_default_fields']);
		add_filter( 'fullculqi/method/redirect', [$this, 'add_redirect'], 10, 3 );
		add_filter( 'fullculqi/method/multi_html', [$this, 'add_multi_html'] );

		switch( $method['oclick_type'] ) {
			case 'modal':
				add_action( 'fullculqi/method/enqueue_scripts', [$this, 'add_scripts'], 10, 2 );
				add_action( 'fullculqi/receipt_page/notify', [$this, 'add_notify'], 10, 1 );
				add_action( 'fullculqi/checkout/simple_success', [$this, 'remove_meta_order'], 10, 3 );
				break;

			case 'form' :
				add_filter( 'fullculqi/method/has_fields', '__return_true');
				add_filter( 'fullculqi/method/validate', [ $this, 'validate_fields' ], 10, 2 );
				add_filter( 'fullculqi/method/supports', [ $this, 'add_supports' ], 10, 1 );
				add_action( 'fullculqi/method/payment_fields', [$this, 'add_form'], 10, 1 );
				//add_filter( 'woocommerce_credit_card_form_fields', [$this, 'add_fields'], 10, 2 );
				break;
		}
	}


	public function add_admin_fields($fields = []) {
		$fields['oclick_title'] = [
										'title'	=> __('ONCLICK SETTING','fullculqi'),
										'type'	=> 'title'
									];

		$fields['oclick_type'] = [
										'title'		=> __('Payment Panel at Checkout','fullculqi'),
										'type'		=> 'radio',
										'default'	=> 'modal',
										'options'	=> [
														'modal'	=> __('Modal/Popup','fullculqi'),
														'form'	=> __('Embedded web form','fullculqi'),
													],
									];

		return $fields;
	}

	public function add_default_fields($default) {
		$default['oclick_type'] = 'modal';
		return $default;
	}

	public function add_supports($supports) {

		$supports[] = 'default_credit_card_form';

		return $supports;
	}


	public function add_form($method) {
		$cc_form           = new WC_Payment_Gateway_CC();
		$cc_form->id       = $method->id;
		$cc_form->supports = $method->supports;
		$cc_form->form();
	}

	public function validate_fields() {

		if( isset( $_POST[ 'payment_method' ] ) && $_POST[ 'payment_method' ] == 'fullculqi' ) {

			$status = true;
			
			if(!isset($_POST['fullculqi-card-number']) || empty($_POST['fullculqi-card-number'])) {
				$status = false;
				wc_add_notice(  __('Card number is required', 'fullculqi'), 'error' );
			}

			if(!isset($_POST['fullculqi-card-expiry']) || empty($_POST['fullculqi-card-expiry'])) {
				$status = false;
				wc_add_notice(  __('Card expiry is required', 'fullculqi'), 'error' );
			}

			if(!isset($_POST['fullculqi-card-cvc']) || empty($_POST['fullculqi-card-cvc'])) {
				$status = false;
				wc_add_notice(  __('Card CVC is required', 'fullculqi'), 'error' );
			}

			return $status;
		}

		return true;
	}


	/*public function add_fields($fields, $method_id) {

		$new_fields = array(
			'card-number-field' => '<p class="form-row form-row-wide">
				<label for="card[number]">' . esc_html__( 'Card number', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>
				<input class="input-text wc-credit-card-form-card-number" inputmode="numeric" autocomplete="cc-number" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" name=" '. esc_attr($method_id.'-card-number') .'" data-culqi="card[number]" id="card[number]" />
			</p>',
			'card-expiry-field' => '<p class="form-row form-row-first">
				<label for="' . esc_attr( $method_id ) . '-card-expiry">' . esc_html__( 'Expiry (MM/YY)', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>
				<input class="input-text wc-credit-card-form-card-expiry-month" inputmode="numeric" autocomplete="cc-exp-month" autocorrect="no" autocapitalize="no" spellcheck="no" type="text" placeholder="' . esc_attr__( 'MM', 'woocommerce' ) . '" ' . esc_attr($method_id.'-card-expiry-month') . ' data-culqi="card[exp_month]" id="card[exp_month]" />
				<input class="input-text wc-credit-card-form-card-expiry-year" inputmode="numeric" autocomplete="cc-exp-year" autocorrect="no" autocapitalize="no" spellcheck="no" type="text" placeholder="' . esc_attr__( 'YY', 'woocommerce' ) . '" ' . esc_attr($method_id.'-card-expiry-year') . ' data-culqi="card[exp_year]" id="card[exp_year]" />
			</p>',
			'card-cvc-field' => '<p class="form-row form-row-last">
				<label for="card[cvv]">' . esc_html__( 'Card code', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>
				<input class="input-text wc-credit-card-form-card-cvc" inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="text" maxlength="4" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" ' . esc_attr($method_id.'-card-cvc') . ' style="width:100px" data-culqi="card[cvv]" id="card[cvv]" />
			</p>',
			'card-email-field' => '<p class="form-row form-row-wide">
				<label for="card[email]">' . esc_html__( 'Card code', 'woocommerce' ) . '&nbsp;<span class="required">*</span></label>
				<input class="input-text wc-credit-card-form-card-email" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="email" placeholder="' . esc_attr__( 'Email', 'woocommerce' ) . '" ' . esc_attr($method_id.'-card-email') . ' style="width:100px" data-culqi="card[email]" id="cardcard[email]" />
			</p>'
		);

		return $new_fields;
	}*/


	function add_scripts() {
		if( is_checkout() &&
			! ( is_wc_endpoint_url( 'order-pay' ) || is_wc_endpoint_url( 'order-received' ) )
		) {
			$settings 	= fullculqi_get_settings();
			$method 	= fullculqi_get_woo_settings();
			$pnames = array();

			// Disabled from thirds
			$method['installments'] = apply_filters('fullculqi_cc/method/disabled_installments', false, WC()->cart, 'cart') ? 'no' : $method['installments'];

			
			foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product = $cart_item['data'];
				$pnames[] = $_product->get_name();
			}

			//$js_checkout	= 'https://checkout.culqi.com/v2';
			$js_checkout	= 'https://checkout.culqi.com/js/v3';
			$js_fullculqi	= FULLCULQI_CC_URL . 'public/assets/js/fullculqi-cc.js';

			wp_enqueue_script('fullcheckout-cc-js', $js_checkout, array('jquery'), false, true);
			wp_enqueue_script('fullculqi-cc-js', $js_fullculqi, array('jquery', 'wc-checkout', 'fullcheckout-cc-js'), false, true);

			wp_localize_script( 'fullculqi-cc-js', 'fullculqi_cc',
				array(
					'url_ajax_validate'	=> admin_url('admin-ajax.php?action=fullculqi_validated_fields'),
					'public_key'		=> $settings['public_key'],
					'installments'		=> sanitize_title($method['installments']),
					//'multipayment'	=> sanitize_title($method['multipayment']),
					'commerce'			=> $settings['commerce'],
					'url_logo'			=> $settings['logo_url'],
					'currency'			=> get_woocommerce_currency(),
					'description'		=> substr(str_pad(implode(', ', $pnames), 5, '_'), 0, 80),
				)
			);
		}
	}

	public function add_redirect($redirect, $order, $method) {

		$success = get_post_meta($order->get_id(), '_fullculqi_cc_success', true);

		if( isset($success) && $success == 1 ) {
			$redirect['redirect'] = $order->get_checkout_order_received_url();
		}

		return $redirect;
	}

	public function add_notify($order_id) {
		$method = fullculqi_get_woo_settings();

		$error = get_post_meta($order_id, '_fullculqi_cc_error', true);
		
		//if( $order->meta_exists('_fullculqi_cc_error') )
		if( isset($error) && $error == 1 )
			echo '<p style="color:#e54848; font-weight:bold">'.$method['msg_fail'].'</p>';
	}

	public function add_multi_html() {
		return '<span style="color:red;">'.__('Multipayment is disabled because Woocommerce One Click plugin is activated','fullculqi').'</span>';
	}


	public function remove_meta_order($order, $log, $provider_payment) {

		$error = get_post_meta($order->get_id(), '_fullculqi_cc_error', true);

		if( isset($error) && $error == 1 )
			delete_post_meta($order->get_id(), '_fullculqi_cc_error');
	}
}

