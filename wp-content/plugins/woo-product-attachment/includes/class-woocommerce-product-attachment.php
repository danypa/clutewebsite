<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/includes
 */
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/includes
 * @author     multidots <nirav.soni@multidots.com>
 */
class Woocommerce_Product_Attachment
{
    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Woocommerce_Product_Attachment_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected  $loader ;
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected  $plugin_name ;
    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected  $version ;
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'woocommerce-product-attachment';
        $this->version = '1.0.0';
        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $prefix = ( is_network_admin() ? 'network_admin_' : '' );
        add_filter(
            "{$prefix}plugin_action_links_" . WCPOA_PLUGIN_BASENAME,
            array( $this, 'plugin_action_links' ),
            10,
            4
        );
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Woocommerce_Product_Attachment_Loader. Orchestrates the hooks of the plugin.
     * - Woocommerce_Product_Attachment_i18n. Defines internationalization functionality.
     * - Woocommerce_Product_Attachment_Admin. Defines all hooks for the admin area.
     * - Woocommerce_Product_Attachment_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-product-attachment-loader.php';
        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-woocommerce-product-attachment-i18n.php';
        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-woocommerce-product-attachment-admin.php';
        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-woocommerce-product-attachment-public.php';
        $this->loader = new Woocommerce_Product_Attachment_Loader();
    }
    
    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Woocommerce_Product_Attachment_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {
        $plugin_i18n = new Woocommerce_Product_Attachment_i18n();
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Woocommerce_Product_Attachment_Admin( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        if ( empty($GLOBALS['admin_page_hooks']['dots_store']) ) {
            $this->loader->add_action( 'admin_menu', $plugin_admin, 'dot_store_menu' );
        }
        $this->loader->add_action( 'admin_init', $plugin_admin, 'welcome_wcpoa_plugin_screen_do_activation_redirect' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'wcpoa_plugin_menu' );
        $this->loader->add_action( 'admin_wcpoa_setting_page', $plugin_admin, 'wcpoa_setting_page' );
        $this->loader->add_action(
            'add_meta_boxes',
            $plugin_admin,
            'wcpoa_add_meta_box',
            10,
            9
        );
        $this->loader->add_action( 'save_post', $plugin_admin, 'wcpoa_attachment_meta_data' );
        $this->loader->add_action( 'post_edit_form_tag', $plugin_admin, 'wcpoa_attachment_edit_form' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'wcpoa_order_add_meta_boxes' );
        $this->loader->add_action( 'admin_head', $plugin_admin, 'wcpoa_free_active_menu' );
    }
    
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }
    
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Woocommerce_Product_Attachment_Public( $this->get_plugin_name(), $this->get_version() );
        $wcpoa_attachments_show_in_email = get_option( 'wcpoa_attachments_show_in_email' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_filter( 'woocommerce_product_tabs', $plugin_public, 'wcpoa_new_product_tab' );
        $this->loader->add_action( 'woocommerce_account_wcpoa_attachment_tab_endpoint', $plugin_public, 'wcpoa_attachment_content' );
        $this->loader->add_action(
            'woocommerce_new_order_item',
            $plugin_public,
            'wcpoa_add_values_to_order_item_meta',
            1,
            3
        );
        $this->loader->add_action(
            'woocommerce_order_details_after_order_table',
            $plugin_public,
            'wcpoa_order_data_show',
            1,
            2
        );
        $this->loader->add_action( 'init', $plugin_public, 'wcpoa_download_file' );
        
        if ( $wcpoa_attachments_show_in_email === 'yes' ) {
            $this->loader->add_filter(
                'woocommerce_email_order_meta_fields',
                $plugin_public,
                'wcpoa_woocommerce_email_order_attachment',
                10,
                3
            );
            $this->loader->add_action( 'woocommerce_email_header', $plugin_public, 'wcpoa_woocommerce_email_add_css_to_email_attachment' );
        }
        
        $this->loader->add_filter(
            'woocommerce_paypal_args',
            $plugin_public,
            'paypal_bn_code_filter',
            99,
            1
        );
    }
    
    /**
     * Return the plugin action links.  This will only be called if the plugin
     * is active.
     *
     * @since 1.0.0
     * @param array $actions associative array of action names to anchor tags
     * @return array associative array of plugin action links
     */
    public function plugin_action_links(
        $actions,
        $plugin_file,
        $plugin_data,
        $context
    )
    {
        $custom_actions = array(
            'configure' => sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=woocommerce_product_attachment' ), __( 'Settings', $this->plugin_name ) ),
            'docs'      => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'http://www.thedotstore.com/docs/plugin/woocommerce-product-attachment/' ), __( 'Docs', $this->plugin_name ) ),
            'support'   => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( 'www.thedotstore.com/support' ), __( 'Support', $this->plugin_name ) ),
        );
        // add the links to the front of the actions list
        return array_merge( $custom_actions, $actions );
    }
    
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }
    
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Woocommerce_Product_Attachment_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

}