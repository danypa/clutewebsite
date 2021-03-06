<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Gateway_FullCulqi extends WC_Payment_Gateway {

	public function __construct() {

		$this->id 					= 'fullculqi';
		$this->method_title			= __('Culqi Full Integration','letsgo');
		$this->method_description 	= __( 'Allows payments by Card Credit. This payment method will decide if it is a simple payment or subscription or other', 'letsgo' );
		$this->icon 				= FULLCULQI_PLUGIN_URL . 'public/assets/images/cards.png';
		
		// Define user set variables
		$this->has_fields		= apply_filters('fullculqi/method/has_fields', false);
		$this->title			= $this->get_option( 'title' );
		$this->installments 	= $this->get_option( 'installments', 'no' );
		$this->multipayment 	= $this->get_option( 'multipayment', 'no' );
		$this->multi_duration	= $this->get_option( 'multi_duration', 24 );
		$this->description		= $this->get_option( 'description' );
		$this->msg_fail			= $this->get_option( 'msg_fail' );
		$this->time_modal		= $this->get_option( 'time_modal', 0 );
		$this->settings			= fullculqi_get_settings();

		$this->supports = apply_filters('fullculqi/method/supports',
								[ 'products', 'refunds', 'pre-orders' ]
							);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Actions
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action('woocommerce_receipt_' . $this->id, [ $this, 'receipt_page' ] );
		add_action('woocommerce_thankyou_' . $this->id, [ $this, 'thankyou_page' ] );

		// JS and CSS
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}


	function enqueue_scripts() {
		if( is_checkout_pay_page() ) {

			global $wp;

			if( !isset($wp->query_vars['order-pay']) ) return;

			$pnames = array();
			$order_id = $wp->query_vars['order-pay'];
			$order = new WC_Order( $order_id );

			$settings = fullculqi_get_settings();

			foreach ($order->get_items() as $item ) {
				$product = $item->get_product();
				$pnames[] = $product->get_name();
			}


			// Disabled from thirds
			$this->multipayment = apply_filters('fullculqi/method/disabled_multipayments', false, $order, 'order') ? 'no' : $this->multipayment;

			$this->installments = apply_filters('fullculqi/method/disabled_installments', false, $order, 'order') ? 'no' : $this->installments;
			

			if( $this->multipayment == 'yes' ) {

				$multi_order = get_post_meta($order_id, 'culqi_order', true);

				if( !$multi_order ) {
					$log = new FullCulqi_Logs();
					$log->set_settings_payment($order_id);
					$multi_order = FullCulqi_Checkout::create_order($order, $this->multi_duration, $pnames, $log);

					update_post_meta($order_id, 'culqi_order', $multi_order);
				}
			}
			

			$js_checkout	= 'https://checkout.culqi.com/js/v3';
			$js_fullculqi	= FULLCULQI_PLUGIN_URL . 'public/assets/js/fullculqi.js';
			$js_waitme		= FULLCULQI_PLUGIN_URL . 'public/assets/js/waitMe.min.js';
			$css_waitme		= FULLCULQI_PLUGIN_URL . 'public/assets/css/waitMe.min.css';

			wp_enqueue_script('fullcheckout-js', $js_checkout, array('jquery'), false, true);
			wp_enqueue_script('fullculqi-js', $js_fullculqi, array('jquery', 'fullcheckout-js'), false, true);
			wp_enqueue_script('waitme-js', $js_waitme, array('jquery'), false, true);
			wp_enqueue_style('waitme-css', $css_waitme );

			wp_localize_script( 'fullculqi-js', 'fullculqi',
				apply_filters('fullculqi/method/localize',
				[
					'url_payment'	=> site_url('wc-api/fullculqi_create_payment/'),
					'url_order'		=> site_url('wc-api/fullculqi_create_order/'),
					'url_success'	=> $order->get_checkout_order_received_url(),
					'public_key'	=> sanitize_text_field($settings['public_key']),
					'installments'	=> sanitize_title($this->installments),
					'multipayment'	=> sanitize_title($this->multipayment),
					'multi_order'	=> $this->multipayment == 'yes' ? $multi_order : '',
					'lang'			=> fullculqi_get_language(),
					'time_modal'	=> absint($this->time_modal*1000),
					'order_id'		=> absint($order_id),
					'commerce'		=> sanitize_text_field($settings['commerce']),
					'url_logo'		=> esc_url($settings['logo_url']),
					'currency'		=> get_woocommerce_currency(),
					'description'	=> substr(str_pad(implode(', ', $pnames), 5, '_'), 0, 80),
					'loading_text'	=> __('Cargando. Por favor espere.','letsgo'),
					'total'			=> fullculqi_format_total($order->get_total()),
					'msg_fail'		=> sanitize_text_field($this->msg_fail),
					'msg_error'		=> __('Hubo un problema en el proceso de compra. Por favor intente de nuevo.','letsgo'),
					'wpnonce'		=> wp_create_nonce('fullculqi'),
				], $this)
			);
		}

		do_action('fullculqi/method/enqueue_scripts' );
	}
	

	function init_form_fields() {

		$this->form_fields = apply_filters('fullculqi/method/form_fields',
							[
								'basic_section' => [
									'title' => __('BASIC SETTING','letsgo'),
									'type'  => 'title'
								],

								'enabled' => [
									'title'		=> __( 'Enable/Disable', 'letsgo' ),
									'type'		=> 'checkbox',
									'label'		=> __( 'Enable Culqi', 'letsgo' ),
									'default'	=> 'yes',
								],
								'installments' => [
									'title'			=> __('Installments', 'letsgo'),
									'description'	=> __('If checked, a selection field will appear in the modal with the available installments.','letsgo'),
									'class'			=> '',
									'type'			=> 'checkbox',
									'label'			=> __('Enable Installments', 'letsgo'),
									'default'		=> 'no',
									'desc_tip'		=> true,
								],
								'title' => [
									'title'			=> __( 'Title', 'letsgo' ),
									'type'			=> 'text',
									'description'	=> __( 'This controls the title which the user sees during checkout.', 'letsgo' ),
									'desc_tip'		=> true,
								],
								'description' => [
									'title'			=> __('Description', 'letsgo'),
									'description'	=> __('Brief description of the payment gateway. This message will be seen by the buyer','letsgo'),
									'class'			=> '',
									'type'			=> 'textarea',
									'desc_tip'		=> true,
								],

								'multi_section' => [
									'title'			=> __('MULTIPAYMENT SETTING','letsgo'),
									'type'			=> 'title',
									'description'	=> apply_filters('fullculqi/method/multi_html',''),
								],

								'multipayment' => [
									'title'			=> __('Enable', 'letsgo'),
									'description'	=> __('If checked several tabs will appear in the modal with other payments','letsgo'),
									'class'			=> '',
									'type'			=> 'checkbox',
									'label'			=> __('Enable Multipayment', 'letsgo'),
									'default'		=> 'no',
									'desc_tip'		=> true,
								],
								'multi_duration' => [
									'title'			=> __('Duration', 'letsgo'),
									'description'	=> __('If enable Multipayment option, you must choose the order duration. This is the time you give the customer to make the payment.','letsgo'),
									'class'			=> '',
									'type'			=> 'select',
									'options'		=> [
														'1'		=> __('1 Hora','letsgo'),
														'2'		=> __('2 Horas','letsgo'),
														'4'		=> __('4 Horas','letsgo'),
														'8'		=> __('8 Horas','letsgo'),
														'12'	=> __('12 Horas','letsgo'),
														'24'	=> __('1 D??a','letsgo'),
														'48'	=> __('2 D??as','letsgo'),
														'96'	=> __('4 D??as','letsgo'),
														'168'	=> __('7 D??as','letsgo'),
														'360'	=> __('15 D??as','letsgo'),
													],
									'default'		=> '24',
									'desc_tip'		=> true,
								],
								'multi_url' => [
									'title' => __('Webhook URL','letsgo'),
									'type' => 'multiurl',
									'description' => __('If you have enabled the multipayment, so you need configure the webhooks usign this URL','letsgo'),
									'desc_tip' => true,
									'default' => 'yes',
								],

								'additional_section' => [
									'title' => __('ADDITIONAL SETTING','letsgo'),
									'type'  => 'title'
								],

								'status_success' => [
									'title' => __('Success Status','letsgo'),
									'type' => 'select',
									'class'       => 'wc-enhanced-select',
									'description' => __('If the purchase is success, apply this status to the order','letsgo'),
									'default' => 'wc-processing',
									'desc_tip' => true,
									'options'  => wc_get_order_statuses(),
								],
								'msg_fail' => [
									'title'			=> __('Failed Message', 'letsgo'),
									'description'	=> __('This is the message will be shown to the customer if there is a error in the payment','letsgo'),
									'class'			=> '',
									'type'			=> 'textarea',
									'desc_tip'		=> false,
									'default'		=> __('Ocurri?? un error al realizar el pago. Verifique que los datos de su tarjeta sean correctos e intente nuevamente.','letsgo'),
								],
								'time_modal' => [
									'title'			=> __('Popup/Modal Time','letsgo'),
									'type'			=> 'text',
									'description'	=> __('If you want the modal window to appear after a while without clicking "buy", put the seconds here. (Warning: may it not work in Safari). If you do not want to, leave it at zero.','letsgo'),
									'default'		=> '0',
									'placeholder'	=> '0',
									'desc_tip'		=> false,
								],
							]
						);
	}


	function payment_fields() {
		do_action('fullculqi/method/payment_fields', $this);
	}


	function thankyou_page( $order_id ) {

		$order = new WC_Order( $order_id );
	}

	function receipt_page( $order_id ) {

		$order = new WC_Order( $order_id );	

		$args = array(
					'src_image'		=> $this->icon,
					'url_cancel'	=> esc_url( $order->get_cancel_order_url() ),
					'order_id'		=> $order_id,
				);

		do_action('fullculqi/form-receipt/before', $order);

		wc_get_template('public/layouts/form-receipt.php', $args, false, FULLCULQI_PLUGIN_DIR );

		do_action('fullculqi/form-receipt/after', $order);
	}


	function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		// Mark as on-hold (we're awaiting the cheque)
		//$order->update_status( 'pending', __('Order pending confirmation','letsgo'));

		return apply_filters('fullculqi/method/redirect', array(
					'result'   => 'success',
					'redirect' => $order->get_checkout_payment_url(true),
				), $order, $this);
	}


	function validate_fields() {
		return apply_filters('fullculqi/method/validate', true, $this);
	}


	function generate_radio_html( $key, $data ) {

		$field_key = $this->get_field_key( $key );
		$defaults  = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'type'              => 'radio',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'options'           => array(),
		);
		$data = wp_parse_args( $data, $defaults );
		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					
					<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
						<label for="<?php echo esc_attr( $option_key ); ?>">
							<input type="radio" value="<?php echo esc_attr( $option_key ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $option_key ); ?>" <?php checked( $this->get_option( $key ), $option_key ); ?> /><?php echo esc_attr( $option_value ); ?>
						</label>
						<br />
					<?php endforeach; ?>

					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	function generate_multiurl_html($key, $data) {
		ob_start();
		?>

		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<b><?php echo site_url('?wc-api=fullculqi_update_order'); ?></b>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>

		<?php
		return ob_get_clean();
	}
}

?>
