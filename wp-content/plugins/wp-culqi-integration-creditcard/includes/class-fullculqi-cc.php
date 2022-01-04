<?php

class FullCulqi_CardCredit {

	/**
	 * Plugin Instance
	 */
	protected static $_instance = null;

	/**
	 * Method Instance
	 */
	protected $method;

	/**
	 * Integrator Instance
	 */
	protected $checkout;

	/**
	 * Ajax Instance
	 */
	protected $ajax;

	/**
	 * License Instance
	 */
	protected $license;

	/**
	 * Ensures only one instance is loaded or can be loaded.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'fullculqi' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'fullculqi' ), '2.1' );
	}


	function __construct() {

		$this->load_dependencies();
		$this->set_locale();
		$this->set_objects();

		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 0 );
	}

	private function load_dependencies() {

		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-license.php';
		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-i18n.php';
		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-woocommerce.php';
		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-method.php';
		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-checkout.php';
		require_once FULLCULQI_CC_DIR . 'includes/class-fullculqi-cc-ajax.php';
		require_once FULLCULQI_CC_DIR . 'public/class-fullculqi-cc-public.php';
		
		if( is_admin() ) {
			require_once FULLCULQI_CC_DIR . 'admin/class-fullculqi-cc-information.php';
		}
	}

	function notice_woo() {
		echo '<div class="error"><p>' . sprintf( __( 'Woocommerce Culqi One Click plugin depends on the last version of %s to work!', 'fullculqi' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>' ) . '</p></div>';
	}

	function notice_fullculqi() {
		echo '<div class="error"><p>' . sprintf( __( 'Woocommerce Culqi One Click plugin depends on the last version of %s to work!', 'fullculqi' ), '<a href="https://wordpress.org/plugins/culqi-full-integration/">Wordpress Culqi Integration</a>' ) . '</p></div>';
	}


	function plugins_loaded() {
		if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_woo'] );
			return;
		}

		if ( ! class_exists( 'FullCulqi' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_fullculqi'] );
			return;
		}
	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ShipArea_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new FullCulqi_Cci18n();
		$plugin_i18n->set_domain( 'fullculqi' );

		add_action( 'plugins_loaded', [ $plugin_i18n, 'load_plugin_textdomain' ] );
	}

	/**
	 * Set all global objects
	 */
	private function set_objects() {

		$this->license	= new FullCulqi_CcLicense();

		if( is_admin() )
			$this->info = new FullCulqi_CcInfo();

		if( !FullCulqi_CcLicense::is_active() )
			return;

		$this->method 		= new FullCulqi_CcMethod();
		$this->checkout 	= new FullCulqi_CcCheckout();
		$this->ajax 		= new FullCulqi_CcAjax();
		$this->public 		= new FullCulqi_CcPublic();	
	}
}

?>