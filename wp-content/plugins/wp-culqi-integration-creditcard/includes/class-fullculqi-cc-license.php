<?php
class FullCulqi_CcLicense {

	protected $slug;
	protected $plugin;
	protected $plugin_name;

	public static $transient_hours = 24;
	public static $error_title;
	public static $error_desc;

	function __construct() {

		$this->plugin_name	= __('Wordpress Culqi Integration Card Credit', 'letsgo');
		$this->slug 		= dirname( FULLCULQI_CC_BASE );
		$this->plugin 		= FULLCULQI_CC_BASE;

		add_action( 'wp_ajax_fullculqi_cc_get_status', [ $this, 'get_ajax_status' ] );
		add_action( 'wp_ajax_nopriv_fullculqi_cc_get_status', [ $this, 'get_ajax_status' ] );
		add_action( 'wp_ajax_fullculqi_cc_set_unassign', [ $this, 'set_ajax_unassign'] );
		add_action( 'wp_ajax_nopriv_fullculqi_cc_set_unassign', [ $this, 'set_ajax_unassign'] );

		add_action( 'admin_enqueue_scripts', [ $this, 'adminscripts'] );
		add_action( 'admin_init', [ $this, 'verify' ] );

		add_action( 'after_setup_theme', [ $this, 'plugin_update' ] );
		add_action( 'admin_footer', [ $this, 'print_box' ], 1 );
	}

	function plugin_update() {
		// Take over the update check
		add_filter( 'pre_set_site_transient_update_plugins', [ $this, 'check_update'] );

		// Take over the Plugin info screen
		add_filter( 'plugins_api', [ $this, 'plugins_api_call' ], 10, 3);
	}


	function verify() {

		$license = get_option('letsgo_fullculqi_cc_license', '');

		if( isset($_POST['letsgo_fullculqi_cc_license_key']) )
			$this->activate_license( $_POST['letsgo_fullculqi_cc_license_key'] );

		if( ! self::is_active() ) {
			add_action( 'admin_notices', [ $this, 'get_license_html' ] );
		}
	}


	public static function is_active() {
		
		$is_active 		= get_transient('letsgo_fullculqi_cc_is_active_license');
		$license_key 	= get_option('letsgo_fullculqi_cc_license', '');

		if( !isset($license_key) || empty($license_key) ) {
			delete_transient('letsgo_fullculqi_cc_is_active_license');
			return false;
		}

		if( isset($is_active) && $is_active == 1 )
			return true;

		$args = array(
					'woo_sl_action'		=> 'status-check',
					'licence_key'		=> $license_key,
					'product_unique_id'	=> FULLCULQI_CC_PRODUCT_ID,
					'domain'			=> FULLCULQI_CC_INSTANCE
				);
	
		$request_uri    = FULLCULQI_CC_API_URL . '?' . http_build_query( $args );
		$data           = wp_remote_get( $request_uri );

		if(is_wp_error( $data ) || $data['response']['code'] != 200) {
			set_transient('letsgo_fullculqi_cc_is_active_license', 1, self::$transient_hours * HOUR_IN_SECONDS);
			return true;
		}

		//wp_mail( FULLCULQI_EMAIL_SUPPORT, sprintf(__('Letsgodev is down right (from %s) - Checking','letsgo'),site_url()), print_r($data,true) );

		$data_body = json_decode($data['body']);

		if(isset($data_body[0]->status)) {

			if($data_body[0]->status == 'success' && $data_body[0]->status_code == 's205') {
				set_transient('letsgo_fullculqi_cc_is_active_license', 1, self::$transient_hours * HOUR_IN_SECONDS);
				return true;
			}

			//[status] => success [status_code] => s203 [message] => Licence Is Inactive
			$array_error = array(
								'datetime'	=> date('Y-m-d H:i:s'),
								'topic'		=> 'otherstatus',
								'status'	=> $data_body[0]->status,
								'code'		=> $data_body[0]->status_code,
								'message'	=> $data_body[0]->message
							);
			
			update_option('letsgo_fullculqi_cc_error', $array_error);
			
			self::$error_title = __('There was a problem checking the license','letsgo');
			self::$error_desc = print_r($array_error,true);

			return false;	
		}

		$array_error = array(
							'datetime'	=> date('Y-m-d H:i:s'),
							'topic'		=> 'nostatus',
							'message'	=> __('There was a problem establishing a connection to the API server','letsgo')
						);
		
		update_option('letsgo_fullculqi_error', $array_error);

		self::$error_title = __('There was a problem checking the license','letsgo');
		self::$error_desc = print_r($array_error,true);

		return false;
	}


	function activate_license($license_key) {

		$args = array(
					'woo_sl_action'		=> 'activate',
					'licence_key'		=> $license_key,
					'product_unique_id'	=> FULLCULQI_CC_PRODUCT_ID,
					'domain'			=> FULLCULQI_CC_INSTANCE
				);
		
		$request_uri    = FULLCULQI_CC_API_URL . '?' . http_build_query( $args );
		$data           = wp_remote_get( $request_uri );

		if(is_wp_error( $data ) || $data['response']['code'] != 200) {
			
			self::$error_title = __('Server is down right. An email was sent to '.FULLCULQI_CC_EMAIL_SUPPORT,'letsgo');
			self::$error_desc = print_r($data,true);

			wp_mail( FULLCULQI_CC_EMAIL_SUPPORT, sprintf(__('Letsgodev is down right (from %s) - Activating','letsgo'),site_url()), print_r($data,true));
		} else {

			$data_body = json_decode($data['body']);
			
			if( isset($data_body[0]->status) ) {
				if($data_body[0]->status == 'success' && $data_body[0]->status_code == 's100') {

					set_transient('letsgo_fullculqi_cc_is_active_license', 1, self::$transient_hours * HOUR_IN_SECONDS);
					update_option('letsgo_fullculqi_cc_license', $license_key);
					wp_redirect($_SERVER['REQUEST_URI']);
					exit;

				} else {
					update_option('letsgo_fullculqi_cc_license', $license_key);
					self::$error_title = __('There was a problem activating the license','letsgo');
					self::$error_desc = print_r($data_body,true);
				}
			} else {

				self::$error_title = __('There was a problem establishing a connection to the API server','letsgo');
				self::$error_desc = print_r($data_body,true);
			}
		}
	}


	function get_license_html() {
		// Open container
		$html = '<div class="updated" style="display: block;">';

		// Title
		$html .= '<h1>' . $this->plugin_name . '</h1>';

		// Description
		$html .= '<div style="margin-bottom: 0.6em; font-size: 13px;">' . __('Please enter your license code','letsgo') . '</div>';

		// Error
		if ( !empty(self::$error_title) ) {
			$html .= '<div style="margin-bottom: 0.6em; font-size: 13px; color: red;">'.self::$error_title.'</div>';
			$html .= '<code>'.print_r(self::$error_desc,true).'</code>';
		}

		// Open form
		$html .= '<form method="post" style="margin-bottom: 0.6em;">';

		// Field
		$html .= '<input type="text" name="letsgo_fullculqi_cc_license_key" value="" placeholder="' . __('License Code', 'letsgo') . '" style="width: 50%; margin-right: 10px;">';

		// Button
		$html .= '<button type="submit" class="button button-primary" title="' . __('Submit', 'letsgo') . '">' . __('Submit', 'letsgo') . '</button>';

		// Close form
		$html .= '</form>';

		// Note
		$html .= '<p><small><a href="https://www.letsgodev.com/documentation/where-do-i-find-my-license-code/" rel="noopener" target="_blank">' . __('Where do I find my License Code?', 'letsgo') . '</a></small></p>';

		// Close container
		$html .= '</div>';

		echo $html;
	}


	function check_update($checked_data) {
		
		if (empty($checked_data->response) )
			return $checked_data;

		$request_string = $this->prepare_request('plugin_update');
		
		if($request_string === FALSE)
			return $checked_data;

		// Start checking for an update
		$request_uri = FULLCULQI_CC_API_URL . '?' . http_build_query( $request_string , '', '&');

		$data = wp_remote_get( $request_uri );

		if(is_wp_error( $data ) || $data['response']['code'] != 200)
			return $checked_data;

		$response_block = json_decode($data['body']);
 
		if(!is_array($response_block) || count($response_block) < 1) {
			return $checked_data;
		}

		//retrieve the last message within the $response_block
		$response_block = $response_block[count($response_block) - 1];

		$response = isset($response_block->message) ? $response_block->message : '';

		if (is_object($response) && !empty($response)) { // Feed the update data into WP update

			//include slug and plugin data
			$response->slug = $this->slug;
			$response->plugin = $this->plugin;
			$response->url = $response->homepage;

			if( isset( $response->sections ) )
				$response->sections = (array)$response->sections;

			if( isset( $response->banners ) )
				$response->banners = (array)$response->banners;

			if( isset( $response->icons ) )
				$response->icons = (array)$response->icons;

			$checked_data->response[$this->plugin] = $response;
		}

		return $checked_data;
	}



	public function prepare_request($action, $args = array()) {

		global $wp_version;

		$license_key = get_option('letsgo_fullculqi_cc_license', '');

		return array(
			'woo_sl_action'		=> $action,
			'version'			=> FULLCULQI_CC_VERSION,
			'product_unique_id'	=> FULLCULQI_CC_PRODUCT_ID,
			'licence_key'		=> $license_key,
			'domain'			=> FULLCULQI_CC_INSTANCE,
			'wp-version'		=> $wp_version
		);
	}


	public function plugins_api_call($def, $action, $args) {
		
		if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug)
			return $def;

		$request_string = $this->prepare_request($action, $args);
		
		if($request_string === FALSE)
			return new WP_Error('plugins_api_failed', __('An error occour when try to identify the plugin.' , 'letsgo') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'letsgo' ) .'&lt;/a>');

		$request_uri = FULLCULQI_CC_API_URL . '?' . http_build_query( $request_string , '', '&');
		$data = wp_remote_get( $request_uri );

		if(is_wp_error( $data ) || $data['response']['code'] != 200)
			return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'letsgo') . '&lt;/p> &lt;p>&lt;a href=&quot;?&quot; onclick=&quot;document.location.reload(); return false;&quot;>'. __( 'Try again', 'letsgo' ) .'&lt;/a>', $data->get_error_message());

		$response_block = json_decode($data['body']);
		//retrieve the last message within the $response_block
		$response_block = $response_block[count($response_block) - 1];
		$response = $response_block->message;

		if (is_object($response) && !empty($response)) { // Feed the update data into WP updater
			//include slug and plugin data
			$response->slug = $this->slug;
			$response->plugin = $this->plugin;
			
			if( isset( $response->sections ) )
				$response->sections = (array)$response->sections;

			if( isset( $response->banners ) )
				$response->banners = (array)$response->banners;

			if( isset( $response->icons ) )
				$response->icons = (array)$response->icons;

			return $response;
		}
	}

	function get_ajax_status() {

		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'license-wpnonce' ) )
        	die ( 'Busted!');
		
		$license_key = get_option('letsgo_fullculqi_cc_license', '');

		if( !isset($license_key) || empty($license_key) ) {
			$return = array(
							'status'	=> 'error',
							'class'		=> 'error',
							'message'	=> __('The license is missing','letsgo')
						);
		
		} else {

			$args = array(
						'woo_sl_action'		=> 'status-check',
						'licence_key'		=> $license_key,
						'product_unique_id'	=> FULLCULQI_CC_PRODUCT_ID,
						'domain'			=> FULLCULQI_CC_INSTANCE
					);
		
			$request_uri    = FULLCULQI_CC_API_URL . '?' . http_build_query( $args );
			$data           = wp_remote_get( $request_uri );

			if(is_wp_error( $data ) || $data['response']['code'] != 200) {
				
				$result = array(
								'isactive'	=> 0,
								'status'	=> 'error',
								'message'	=> __('Letsgodev is down right','letsgo')
							);
			} else {

				$data_body = json_decode($data['body']);

				if( isset($data_body[0]->status) ) {

					if($data_body[0]->status == 'success') {

						switch( $data_body[0]->status_code ) {
							case 's205' : 
								$is_active = 1;
								$class = 'success';
								$message = __('Licence key is active to this domain','letsgo'); break;
							case 's203' :
								$is_active = 0;
								$class = 'warning';
								$message = __('Licence key is unassigned','letsgo'); break;
							default: $message = __('Licence key is missing','letsgo');
						}

						$return = array(
										//'code'	=> $data_body[0]->status_code,
										//'message'	=> $data_body[0]->message,
										//'status'	=> $data_body[0]->status
										'isactive'	=> $is_active,
										'status'	=> 'success',
										'class'		=> $class,
										'message'	=> $message
									);
					} else {

						$return = array(
										'isactive'	=> 0,
										'status'	=> 'error',
										'class'		=> 'error',
										'message'	=> __('There was a problem checking the license. This license does not correspond to this website.','letsgo')
									);
					}

				} else {

					$return = array(
									'isactive'	=> 0,
									'status'  	=> 'error',
									'class'		=> 'error',
									'message'	=> __('There was a problem establishing a connection to the API server','letsgo')
								);
				}
			}
		}

		echo json_encode($return);
		die();
	}

	function set_ajax_unassign() {
		if ( ! wp_verify_nonce( $_POST['wpnonce'], 'license-wpnonce' ) )
        	die ( 'Busted!');
		
		$license_key = get_option('letsgo_fullculqi_cc_license', '');

		if( !isset($license_key) || empty($license_key) ) {
			$return = array('status' => 'error', 'message' => __('The license is missing','letsgo') );
		
		} else {

			$args = array(
						'woo_sl_action'		=> 'deactivate',
						'licence_key'		=> $license_key,
						'product_unique_id'	=> FULLCULQI_CC_PRODUCT_ID,
						'domain'			=> FULLCULQI_CC_INSTANCE
					);
		
			$request_uri    = FULLCULQI_CC_API_URL . '?' . http_build_query( $args );
			$data           = wp_remote_get( $request_uri );

			if(is_wp_error( $data ) || $data['response']['code'] != 200) {
				
				$result = array(
								'status'	=> 'error',
								'class'		=> 'error',
								'message'	=> __('Letsgodev is down right','letsgo')
							);
			} else {

				$data_body = json_decode($data['body']);

				if( isset($data_body[0]->status) ) {

					if($data_body[0]->status == 'success') {

						switch( $data_body[0]->status_code ) {
							case 's201' : $message = __('Licence key successfully unassigned','letsgo'); break;
							default: $message = __('Licence key is missing','letsgo');
						}

						$return = array(
										//'code'	=> $data_body[0]->status_code,
										//'message'	=> $data_body[0]->message,
										//'status'	=> $data_body[0]->status
										'status'	=> 'success',
										'class'		=> 'success',
										'message'	=> $message
									);

						delete_transient('letsgo_fullculqi_cc_is_active_license');
					} else {

						$return = array(
										'status'	=> 'error',
										'class'		=> 'error',
										'message'	=> __('There was a problem checking the license','letsgo')
									);
					}

				} else {

					$return = array(
									'status'  	=> 'error',
									'class'		=> 'error',
									'message'	=> __('There was a problem establishing a connection to the API server','letsgo')
								);
				}
			}
		}

		echo json_encode($return);
		die();
	}

	function adminscripts() {
		$screen = get_current_screen();

		if ( isset($screen->base) && 'plugins' == $screen->base && is_admin() ) {

			wp_register_script('fullculqi-license-js', FULLCULQI_CC_URL.'admin/assets/js/fullculqi-license.js', array('jquery'), '', true );
			wp_register_style('fullculqi-license-css', FULLCULQI_CC_URL.'admin/assets/css/fullculqi-license.css');

			wp_enqueue_script('fullculqi-license-js');
			wp_enqueue_style('fullculqi-license-css' );

			$array_license = array(
								'loading'	=> admin_url('images/spinner-2x.gif'),
								'ajax'		=> admin_url('admin-ajax.php'),
								'nonce'		=> wp_create_nonce( 'license-wpnonce' ),
								'unbutton'	=> __('Unassign from this website','letsgo')
							);

			wp_localize_script('fullculqi-license-js', 'fullculqi_cc_license', $array_license);
		}
	}

	function print_box() {
		$screen = get_current_screen();

		if ( 'plugins' == $screen->base && is_admin() )
			include_once FULLCULQI_CC_DIR.'admin/layouts/popup-license.php';
	}
}
?>