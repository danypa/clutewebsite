<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/admin
 * @author     Multidots <inquiry@multidots.in>
 */
class Woocommerce_Product_Attachment_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Product_Attachment_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Product_Attachment_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $current_screen = get_current_screen();
        $post_type = $current_screen->post_type;
        $menu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
        
        if ( isset( $menu_page ) && !empty($menu_page) && ($menu_page === "woocommerce_product_attachment" || $menu_page === "wcpoa_bulk_attachment") || !empty($post_type) && $post_type === 'product' ) {
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_style(
                $this->plugin_name . '-wcpoa-main-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'css/woocommerce-product-attachment-admin.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-wcpoa-main-style',
                plugin_dir_url( __FILE__ ) . 'css/style.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-font-awesome',
                plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-jquery-ui',
                plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-main-jquery-ui',
                plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css',
                array(),
                $this->version,
                'all'
            );
            wp_enqueue_style(
                $this->plugin_name . '-select2.min',
                plugin_dir_url( __FILE__ ) . 'css/select2.min.css',
                array(),
                $this->version,
                'all'
            );
        }
    
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Woocommerce_Product_Attachment_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Woocommerce_Product_Attachment_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $current_screen = get_current_screen();
        $post_type = $current_screen->post_type;
        $menu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
        
        if ( isset( $menu_page ) && !empty($menu_page) && ($menu_page === "woocommerce_product_attachment" || $menu_page === "wcpoa_bulk_attachment") || !empty($post_type) && $post_type === 'product' ) {
            wp_enqueue_script( 'postbox' );
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_media();
            wp_enqueue_script(
                $this->plugin_name,
                plugin_dir_url( __FILE__ ) . 'js/woocommerce-product-attachment-admin.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-select2_js',
                plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js?ver=4.0.3',
                array( 'jquery' ),
                '4.0.3',
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-pro',
                plugin_dir_url( __FILE__ ) . 'js/pro-wcpoa-input.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-datepicker',
                plugin_dir_url( __FILE__ ) . 'js/datepicker.min.js',
                array( 'jquery' ),
                $this->version,
                false
            );
            wp_enqueue_script(
                $this->plugin_name . '-validation',
                plugin_dir_url( __FILE__ ) . 'js/jquery.validation.js',
                array( 'jquery' ),
                $this->version,
                false
            );
        }
        
        if ( isset( $menu_page ) && !empty($menu_page) && $menu_page === "wcpoa_bulk_attachment" ) {
            wp_dequeue_script( 'wp-auth-check' );
        }
    }
    
    public function welcome_wcpoa_plugin_screen_do_activation_redirect()
    {
        // if no activation redirect
        if ( !get_transient( '_welcome_screen_activation_redirect_data' ) ) {
            return;
        }
        // Delete the redirect transient
        delete_transient( '_welcome_screen_activation_redirect_data' );
        // if activating from network, or bulk
        $activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_SPECIAL_CHARS );
        if ( is_network_admin() || isset( $activate_multi ) ) {
            return;
        }
        // Redirect to extra cost welcome  page
        wp_safe_redirect( add_query_arg( array(
            'page' => 'woocommerce_product_attachment&tab=wcpoa-plugin-getting-started',
        ), admin_url( 'admin.php' ) ) );
        exit;
    }
    
    /**
     *
     * dotsstore menu add
     */
    public function dot_store_menu()
    {
        global  $GLOBALS ;
        if ( empty($GLOBALS['admin_page_hooks']['dots_store']) ) {
            add_menu_page(
                'DotStore Plugins',
                'DotStore Plugins',
                'null',
                'dots_store',
                array( $this, 'dot_store_menu_page' ),
                plugin_dir_url( __FILE__ ) . 'images/menu-icon.png',
                25
            );
        }
    }
    
    /**
     *
     * WooCommerce Product Attachment menu add
     */
    public function wcpoa_plugin_menu()
    {
        add_submenu_page(
            "dots_store",
            "WooCommerce Product Attachment",
            "WooCommerce Product Attachment",
            "manage_options",
            "woocommerce_product_attachment",
            array( $this, "wcpoa_options_page" )
        );
    }
    
    /*
     * Active Menu
     */
    public function wcpoa_free_active_menu()
    {
        $screen = get_current_screen();
        //DotStore Menu Submenu based conditions
        if ( !empty($screen) && ($screen->id === '' || $screen->id === 'dotstore-plugins_page_wcpoa_bulk_attachment') ) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('a[href="admin.php?page=woocommerce_product_attachment"]').parent().addClass('current');
                    $('a[href="admin.php?page=woocommerce_product_attachment"]').addClass('current');
                });
            </script>
            <?php 
        }
    }
    
    /**
     * WooCommerce Product Attachment Option Page HTML
     *
     */
    public function wcpoa_options_page()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/header/plugin-header.php';
        $menu_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_SPECIAL_CHARS );
        $wcpoa_attachment_tab = ( isset( $menu_tab ) && !empty($menu_tab) ? $menu_tab : '' );
        
        if ( !empty($wcpoa_attachment_tab) ) {
            if ( $wcpoa_attachment_tab === "wcpoa_plugin_setting_page" ) {
                self::wcpoa_setting_page();
            }
            if ( $wcpoa_attachment_tab === "wcpoa-plugin-getting-started" ) {
                self::wcpoa_plugin_get_started();
            }
            if ( $wcpoa_attachment_tab === "wcpoa-plugin-quick-info" ) {
                self::wcpoa_plugin_quick_info();
            }
        } else {
            self::wcpoa_setting_page();
        }
        
        require_once plugin_dir_path( __FILE__ ) . 'partials/header/plugin-sidebar.php';
    }
    
    public function wcpoa_setting_page()
    {
        $plugin_txt_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        $wcpoa_product_tab_name = filter_input( INPUT_POST, 'wcpoa_product_tab_name', FILTER_SANITIZE_STRING );
        $wcpoa_order_tab_name = filter_input( INPUT_POST, 'wcpoa_order_tab_name', FILTER_SANITIZE_STRING );
        $wcpoa_expired_date_label = filter_input( INPUT_POST, 'wcpoa_expired_date_label', FILTER_SANITIZE_STRING );
        $wcpoa_product_tab = ( isset( $wcpoa_product_tab_name ) && !empty($wcpoa_product_tab_name) ? $wcpoa_product_tab_name : 'WooCommerce Product Attachment' );
        $wcpoa_order_tab = ( isset( $wcpoa_order_tab_name ) && !empty($wcpoa_order_tab_name) ? $wcpoa_order_tab_name : 'WooCommerce Product Attachment' );
        $wcpoa_expired_date_label = ( isset( $wcpoa_expired_date_label ) && !empty($wcpoa_expired_date_label) ? $wcpoa_expired_date_label : '' );
        $get_wcpoa_is_viewable = filter_input( INPUT_POST, 'wcpoa_is_viewable', FILTER_SANITIZE_STRING );
        $wcpoa_is_viewable = ( isset( $get_wcpoa_is_viewable ) && !empty($get_wcpoa_is_viewable) ? sanitize_text_field( wp_unslash( $get_wcpoa_is_viewable ) ) : '' );
        //save on database two tab value
        $menu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
        $attachment_submit = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_SPECIAL_CHARS );
        
        if ( isset( $attachment_submit ) && isset( $menu_page ) && $menu_page === 'woocommerce_product_attachment' ) {
            update_option( 'wcpoa_product_tab_name', $wcpoa_product_tab );
            update_option( 'wcpoa_order_tab_name', $wcpoa_order_tab );
            update_option( 'wcpoa_expired_date_label', $wcpoa_expired_date_label );
            update_option( 'wcpoa_is_viewable', $wcpoa_is_viewable );
        }
        
        //store value in variable
        $wcpoa_product_tname = get_option( 'wcpoa_product_tab_name' );
        $wcpoa_order_tname = get_option( 'wcpoa_order_tab_name' );
        $wcpoa_expired_date_tlabel = get_option( 'wcpoa_expired_date_label' );
        $wcpoa_is_viewable = get_option( 'wcpoa_is_viewable' );
        ?>
        <div class="wcpoa-table-main">
            <form method="post" action="#"  enctype="multipart/form-data">
                <table class="wcpoa-tableouter">
                    <tbody>
                    <tr>
                        <th>
                            <span class="wcpoa-name"><?php 
        esc_html_e( 'Product Details Page Tab Title', $plugin_txt_domain );
        ?></span>
                        </th>
                        <td class="">
                            <div class="wcpoa-name-txtbox">
                                <input type="text" name="wcpoa_product_tab_name"
                                       value="<?php 
        echo  esc_attr( $wcpoa_product_tname ) ;
        ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <span class="wcpoa-name"><?php 
        esc_html_e( 'Order Details Page Tab Title', $plugin_txt_domain );
        ?></span>
                        </th>
                        <td class="">
                            <div class="wcpoa-name-txtbox">
                                <input type="text" name="wcpoa_order_tab_name"
                                       value="<?php 
        echo  esc_attr( $wcpoa_order_tname ) ;
        ?>">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <span class="wcpoa-name"><?php 
        esc_html_e( 'Attachments Date Label Show', $plugin_txt_domain );
        ?></span>
                        </th>
                        <td class="">
                            <div class="wcpoa-name-txtbox">
                                <select name="wcpoa_expired_date_label" class="wcpoa_expired_date_label"
                                        data-type="" data-key="">
                                    <option name="yes"
                                            value="yes" <?php 
        echo  ( $wcpoa_expired_date_tlabel === "yes" ? 'selected' : '' ) ;
        ?>><?php 
        esc_html_e( 'Yes', $plugin_txt_domain );
        ?></option>
                                    <option name="no" value="no"
                                            class="" <?php 
        echo  ( $wcpoa_expired_date_tlabel === "no" ? 'selected' : '' ) ;
        ?>><?php 
        esc_html_e( 'No', $plugin_txt_domain );
        ?></option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <?php 
        ?>
                        <tr>
                            <th>
                                <span class="wcpoa-name"><?php 
        esc_html_e( 'Is default behavior of the attached PDF is viewable?', 'woocommerce-product-attachment' );
        ?></span>
                            </th>
                            <td class="">
                                <div class="wcpoa-name-txtbox">
                                    <select name="wcpoa_is_viewable" class="wcpoa_is_viewable" data-type="" data-key="">
                                        <option name="no" value="no" class="" <?php 
        selected( $wcpoa_is_viewable, 'no', true );
        ?>><?php 
        esc_html_e( 'No', 'woocommerce-product-attachment' );
        ?></option>
                                        <option name="yes" value="yes" <?php 
        selected( $wcpoa_is_viewable, 'yes', true );
        ?>><?php 
        esc_html_e( 'Yes', 'woocommerce-product-attachment' );
        ?></option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    <?php 
        ?>
                    
                    <tr>
                        <td colspan="2" class="wcpoa-setting-btn">
                            <?php 
        submit_button();
        ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <?php 
    }
    
    /**
     * Plugin Getting started
     *
     */
    function wcpoa_plugin_get_started()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcpoa-plugin-get-started.php';
    }
    
    /**
     * Plugin Quick Information
     *
     */
    function wcpoa_plugin_quick_info()
    {
        require_once plugin_dir_path( __FILE__ ) . 'partials/wcpoa-plugin-quick-info.php';
    }
    
    public function wcpoa_add_meta_box( $post_type )
    {
        global  $post ;
        $product_id = $post->ID;
        $_product = wc_get_product( $product_id );
        $plugin_txt_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        $post_type = array( 'product' );
        add_meta_box(
            'wcpoa_attachment',
            __( 'WooCommerce Product Attachment', $plugin_txt_domain ),
            array( $this, 'wcpoa_attachment_product_page' ),
            $post_type,
            'advanced',
            'high'
        );
    }
    
    public function wcpoa_get_hidden_input( $atts )
    {
        $atts['type'] = 'hidden';
        return '<input ' . $this->wcpoa_esc_attr( $atts ) . ' />';
    }
    
    public function wcpoa_esc_attr( $atts, $return = true )
    {
        // is string?
        
        if ( is_string( $atts ) ) {
            $atts = trim( $atts );
            return esc_attr( $atts );
        }
        
        // validate
        if ( empty($atts) ) {
            return '';
        }
        foreach ( $atts as $key => $value ) {
            echo  esc_html( $key ) . '="' . esc_attr( $value ) . '"' ;
        }
        return;
    }
    
    /**
     *
     */
    public function wcpoa_attachment_product_page()
    {
        global 
            $product,
            $post,
            $i,
            $field
        ;
        // vars
        $div = array(
            'class'    => 'wcpoa-repeater',
            'data-min' => $field['min'],
            'data-max' => $field['max'],
        );
        // ensure value is an array
        
        if ( empty($field['value']) ) {
            $field['value'] = array();
            $div['class'] .= ' -empty';
        }
        
        // rows
        $field['min'] = ( empty($field['min']) ? 0 : $field['min'] );
        $field['max'] = ( empty($field['max']) ? 0 : $field['max'] );
        // populate the empty row data (used for wcpoacloneindex and min setting)
        $empty_row = array();
        // If there are less values than min, populate the extra values
        if ( $field['min'] ) {
            for ( $i = 0 ;  $i < $field['min'] ;  $i++ ) {
                // continue if already have a value
                if ( array_key_exists( $i, $field['value'] ) ) {
                    continue;
                }
                // populate values
                $field['value'][$i] = $empty_row;
            }
        }
        // If there are more values than man, remove some values
        if ( $field['max'] ) {
            for ( $i = 0 ;  $i < count( $field['value'] ) ;  $i++ ) {
                if ( $i >= $field['max'] ) {
                    unset( $field['value'][$i] );
                }
            }
        }
        // setup values for row clone
        $field['value']['wcpoacloneindex'] = $empty_row;
        // show columns
        $show_order = true;
        $show_add = true;
        $show_remove = true;
        
        if ( $field['max'] ) {
            if ( (int) $field['max'] === 1 ) {
                $show_order = false;
            }
            
            if ( $field['max'] <= $field['min'] ) {
                $show_remove = false;
                $show_add = false;
            }
        
        }
        
        // field wrap
        $before_fields = '';
        $after_fields = '';
        
        if ( 'row' === 'row' ) {
            $before_fields = '<td class="wcpoa-fields -left">';
            $after_fields = '</td>';
        }
        
        // layout
        $div['class'] .= ' -' . 'row';
        $plugin_txt_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        $plugin_url = WCPOA_PLUGIN_URL;
        $product_id = $post->ID;
        $product = wc_get_product( $product_id );
        $wcpoa_attachment_ids = get_post_meta( $product_id, 'wcpoa_attachments_id', true );
        $wcpoa_attachment_name = get_post_meta( $product_id, 'wcpoa_attachment_name', true );
        $wcpoa_attach_type = get_post_meta( $product_id, 'wcpoa_attach_type', true );
        $wcpoa_attachment_ext_url = get_post_meta( $product_id, 'wcpoa_attachment_ext_url', true );
        $wcpoa_attachment_url = get_post_meta( $product_id, 'wcpoa_attachment_url', true );
        $wcpoa_attachment_descriptions = get_post_meta( $product_id, 'wcpoa_attachment_description', true );
        $wcpoa_product_page_enable = get_post_meta( $product_id, 'wcpoa_product_page_enable', true );
        $wcpoa_product_variation = get_post_meta( $product_id, 'wcpoa_variation', true );
        $wcpoa_order_status = array();
        $wcpoa_pd_enable = get_post_meta( $product_id, 'wcpoa_expired_date_enable', true );
        $wcpoa_expired_date = get_post_meta( $product_id, 'wcpoa_expired_date', true );
        wp_nonce_field( plugin_basename( __FILE__ ), 'wcpoa_attachment_nonce' );
        ?>
        <div class="wcpoa-field wcpoa-field-repeater" data-name="attachments" data-type="repeater"
             data-key="attachments">
            <div class="wcpoa-label">
                <label for="wcpoa-pro"><?php 
        esc_html_e( 'Attachments', $plugin_txt_domain );
        ?></label>
                <span><?php 
        esc_html_e( 'Enhance your customer experience of product pages with downloadable files, such as technical descriptions, certificates, and licenses, user guides, and manuals, etc. A plugin will help you to attach/ upload any kind of files (doc, jpg, videos, pdf) for a customer orders.', $plugin_txt_domain );
        ?></span><br>

                <span><?php 
        esc_html_e( 'Attachments can be downloadable/viewable on the Order details and/or Product pages. This will help customers to get more details about products they purchase.', $plugin_txt_domain );
        ?></span>
            </div>

            <div class="wcpoa-input">
                <div <?php 
        $this->wcpoa_esc_attr_e( $div );
        ?>>
                    <table class="wcpoa-table">
                        <tbody id="wcpoa-ui-tbody" class="wcpoa-ui-sortable">

                        <?php 
        if ( !empty($wcpoa_attachment_ids) && is_array( $wcpoa_attachment_ids ) ) {
            foreach ( $wcpoa_attachment_ids as $key => $wcpoa_attachments_id ) {
                
                if ( !empty($wcpoa_attachments_id) ) {
                    $attachment_name = ( isset( $wcpoa_attachment_name[$key] ) && !empty($wcpoa_attachment_name[$key]) ? $wcpoa_attachment_name[$key] : '' );
                    $wcpoa_attachment_file_id = ( isset( $wcpoa_attachment_url[$key] ) && !empty($wcpoa_attachment_url[$key]) ? $wcpoa_attachment_url[$key] : '' );
                    $wcpoa_attach_type_single = ( isset( $wcpoa_attach_type[$key] ) && !empty($wcpoa_attach_type[$key]) ? $wcpoa_attach_type[$key] : '' );
                    $wcpoa_attachment_description = ( isset( $wcpoa_attachment_descriptions[$key] ) && !empty($wcpoa_attachment_descriptions[$key]) ? $wcpoa_attachment_descriptions[$key] : '' );
                    $wcpoa_product_p_enable = ( isset( $wcpoa_product_page_enable[$key] ) && !empty($wcpoa_product_page_enable[$key]) ? $wcpoa_product_page_enable[$key] : '' );
                    $wcpoa_product_date_enable = ( isset( $wcpoa_pd_enable[$key] ) && !empty($wcpoa_pd_enable[$key]) ? $wcpoa_pd_enable[$key] : '' );
                    $wcpoa_expired_dates = ( isset( $wcpoa_expired_date[$key] ) && !empty($wcpoa_expired_date[$key]) ? $wcpoa_expired_date[$key] : '' );
                    $wcpoa_order_status_value = get_post_meta( $product_id, 'wcpoa_order_status', true );
                    
                    if ( $wcpoa_order_status_value === 'wc-all' ) {
                        $wcpoa_order_status = array();
                    } else {
                        $wcpoa_order_status = ( isset( $wcpoa_order_status_value[$wcpoa_attachments_id] ) && !empty($wcpoa_order_status_value[$wcpoa_attachments_id]) ? $wcpoa_order_status_value[$wcpoa_attachments_id] : array() );
                    }
                    
                    //file upload
                    // vars
                    $uploader = 'uploader';
                    // vars
                    $o = array(
                        'icon'     => '',
                        'title'    => '',
                        'url'      => '',
                        'filesize' => '',
                        'filename' => '',
                    );
                    $filediv = array(
                        'class'         => 'wcpoa-file-uploader wcpoa-cf',
                        'data-uploader' => $uploader,
                    );
                    // has value?
                    
                    if ( !empty($wcpoa_attachment_file_id) ) {
                        $file = get_post( $wcpoa_attachment_file_id );
                        
                        if ( $file ) {
                            $o['icon'] = wp_mime_type_icon( $wcpoa_attachment_file_id );
                            $o['title'] = $file->post_title;
                            $o['filesize'] = size_format( filesize( get_attached_file( $wcpoa_attachment_file_id ) ) );
                            $o['url'] = wp_get_attachment_url( $wcpoa_attachment_file_id );
                            $explode = explode( '/', $o['url'] );
                            $o['filename'] = end( $explode );
                        }
                        
                        // url exists
                        if ( $o['url'] ) {
                            $filediv['class'] .= ' has-value';
                        }
                    }
                    
                    ?>

                                    <tr class="wcpoa-row wcpoa-has-value -collapsed" data-id="<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>" id="<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>">

                                        <?php 
                    
                    if ( $show_order ) {
                        ?>
                                            <td class="wcpoa-row-handle order"
                                                title="<?php 
                        esc_html_e( 'Drag to reorder', $plugin_txt_domain );
                        ?>">
                                                <a class="wcpoa-icon -collapse small" href="#" data-event="collapse-row"
                                                   title="<?php 
                        esc_html_e( 'Click to toggle', $plugin_txt_domain );
                        ?>"></a>
                                                <?php 
                        // }
                        ?>
                                                <span><?php 
                        echo  intval( $i++ ) + 1 ;
                        ?></span>
                                            </td>
                                        <?php 
                    }
                    
                    ?>
                                        <?php 
                    echo  wp_kses( $before_fields, $this->allowed_html_tags() ) ;
                    ?>
                                        <div class="wcpoa-field wcpoa-field-text" data-name="id" data-type="text"
                                             data-key="">
                                            <div class="wcpoa-label">
                                                <label for=""><?php 
                    esc_html_e( 'Id', $plugin_txt_domain );
                    ?> </label>
                                                <p class="description"><?php 
                    esc_html_e( 'Attachments Id used to identify each product attachment.This value is automatically generated.', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input">
                                                <div class="wcpoa-input-wrap">
                                                    <input readonly="" class="wcpoa_attachments_id"
                                                           name="wcpoa_attachments_id[]"
                                                           value="<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>"
                                                           placeholder="" type="text">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="wcpoa-field -collapsed-target" data-name="_name" data-type="text"
                                             data-key="">
                                            <div class="wcpoa-label">
                                                <label for="attchment_name"><?php 
                    esc_html_e( 'Attachment Name', $plugin_txt_domain );
                    ?>
                                                    <span class="wcpoa-required"> *</span></label>
                                                <p class="description"><?php 
                    esc_html_e( 'Add a product attachment (downloadable files) name like such as technical descriptions, certificates, and licenses, user guides, and manuals, etc. It will be displayed in the front end', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input wcpoa-att-name-parent">
                                                <input class="wcpoa-attachment-name" type="text"
                                                       name="wcpoa_attachment_name[]" 
                                                       value="<?php 
                    echo  esc_attr( $attachment_name ) ;
                    ?>" >
                                            </div>
                                        </div>
                                        <div class="wcpoa-field wcpoa-field-textarea " data-name="description"
                                             data-type="textarea" data-key="" data-required="1">
                                            <div class="wcpoa-label">
                                                <label for="attchment_desc"><?php 
                    esc_html_e( 'Attachment Description', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'You can type a short description of the attachment file. So User will get details about attachment file.', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input">
                                                <textarea class="" name="wcpoa_attachment_description[]"
                                                          placeholder=""
                                                          rows="8"><?php 
                    echo  esc_html( $wcpoa_attachment_description ) ;
                    ?></textarea>
                                            </div>
                                        </div>
                                        <div class="wcpoa-field wcpoa-field-select -collapsed-target">
                                            <div class="wcpoa-label">
                                                <label for="wcpoa_attach_type"><?php 
                    esc_html_e( 'Attachment Type', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'Attachment Type?', $plugin_txt_domain );
                    ?></p>
                                            </div>

                                            <div class="wcpoa-input wcpoa_attach_type">
                                                <?php 
                    ?>
                                                    <select name="wcpoa_attach_type[]" class="wcpoa_attach_type_list" data-type="" data-key="">
                                                        <option name="file_upload" <?php 
                    echo  ( $wcpoa_attach_type_single === "file_upload" ? 'selected' : '' ) ;
                    ?> value="file_upload"><?php 
                    esc_html_e( 'File Upload', $plugin_txt_domain );
                    ?></option>
                                                        <option name="" value="" class="wcpoa_pro_class" disabled><?php 
                    esc_html_e( 'External URL ( Pro Version )', $plugin_txt_domain );
                    ?></option>
                                                    </select>
                                                <?php 
                    ?>    
                                            </div>
                                        </div>
                                        <?php 
                    $is_show = "";
                    ?>
                                        <div style="display:<?php 
                    echo  esc_attr( $is_show ) ;
                    ?>" class="wcpoa-field file_upload wcpoa-field-file -collapsed-target required" data-name="file" data-type="file" data-key="" data-required="1">
                                            <div class="wcpoa-label">
                                                <div class="wcpoa-label">
                                                    <label for="fee_settings_start_date"><?php 
                    esc_html_e( 'Upload Attachment', $plugin_txt_domain );
                    ?>
                                                        <span class="wcpoa-required">*</span>
                                                    </label>
                                                    <p class="description"><?php 
                    esc_html_e( 'Upload Attachment File.', $plugin_txt_domain );
                    ?></p>
                                                </div>
                                            </div>
                                            <div class="wcpoa-input" data-id="<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>">
                                                <div <?php 
                    $this->wcpoa_esc_attr_e( $filediv );
                    ?>>
                                                    <div class="wcpoa-error-message">
                                                        <p><?php 
                    echo  'File value is required' ;
                    ?></p>
                                                        <input name="wcpoa_attachment_file[]"
                                                               data-validation="[NOTEMPTY]"
                                                               value="<?php 
                    echo  esc_attr( $wcpoa_attachment_file_id ) ;
                    ?>"
                                                               data-name="id" type="hidden" required="required">
                                                    </div>
                                                    <div class="show-if-value file-wrap wcpoa-soh">
                                                        <div class="file-icon">
                                                            <img data-name="icon" src="<?php 
                    echo  esc_url( $o['icon'] ) ;
                    ?>"
                                                                 alt=""/>
                                                        </div>
                                                        <div class="file-info">
                                                            <p>
                                                                <strong data-name="title"><?php 
                    echo  esc_html( $o['title'] ) ;
                    ?></strong>
                                                            </p>
                                                            <p>
                                                                <strong><?php 
                    esc_html_e( 'File name', $plugin_txt_domain );
                    ?>
                                                                    :</strong>
                                                                <a data-name="filename" href="<?php 
                    echo  esc_url( $o['url'] ) ;
                    ?>"
                                                                   target="_blank"><?php 
                    echo  esc_html( $o['filename'] ) ;
                    ?></a>
                                                            </p>
                                                            <p>
                                                                <strong><?php 
                    esc_html_e( 'File size', $plugin_txt_domain );
                    ?>
                                                                    :</strong>
                                                                <span data-name="filesize"><?php 
                    echo  esc_html( $o['filesize'] ) ;
                    ?></span>
                                                            </p>

                                                            <ul class="wcpoa-hl wcpoa-soh-target">
                                                                <?php 
                    
                    if ( $uploader !== 'basic' ) {
                        ?>
                                                                    <li><a data-id="<?php 
                        echo  esc_attr( $wcpoa_attachments_id ) ;
                        ?>" class="wcpoa-icon -pencil dark"
                                                                           data-name="edit" href="#"></a></li>
                                                                <?php 
                    }
                    
                    ?>
                                                                <li><a data-id="<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>" class="wcpoa-icon -cancel dark"
                                                                       data-name="remove" href="#"></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <div class="hide-if-value">
                                                        <?php 
                    
                    if ( $uploader === 'basic' ) {
                        ?>
                                                            <?php 
                        
                        if ( $field['value'] && !is_numeric( $field['value'] ) ) {
                            ?>
                                                                <div class="wcpoa-error-message">
                                                                    <p><?php 
                            echo  esc_html( $field['value'] ) ;
                            ?></p></div>
                                                            <?php 
                        }
                        
                        ?>
                                                            <input type="file" name="<?php 
                        echo  esc_attr( $field['name'] ) ;
                        ?>"
                                                                   id="<?php 
                        echo  esc_attr( $field['id'] ) ;
                        ?>"/>
                                                        <?php 
                    } else {
                        ?>
                                                            <p style="margin:0;"><?php 
                        esc_html_e( 'No file selected', $plugin_txt_domain );
                        ?>
                                                                <?php 
                        echo  wp_kses( $this->misha_image_uploader_field( $wcpoa_attachments_id ), $this->allowed_html_tags() ) ;
                        ?></p>
                                                        <?php 
                    }
                    
                    ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                    ?>
                                        <div class="wcpoa-field">
                                            <div class="wcpoa-label">
                                                <label for="product_page_enable"><?php 
                    esc_html_e( 'Show on Product page', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'On Product Details page show attachment.', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input">
                                                <select id="wcpoa_product_page_enable"
                                                        name="wcpoa_product_page_enable[]">
                                                    <option name="yes" <?php 
                    echo  ( $wcpoa_product_p_enable === "yes" ? 'selected' : '' ) ;
                    ?>
                                                            value="yes"><?php 
                    esc_html_e( 'Yes', $plugin_txt_domain );
                    ?></option>
                                                    <option name="no" <?php 
                    echo  ( $wcpoa_product_p_enable === "no" ? 'selected' : '' ) ;
                    ?>
                                                            value="no"><?php 
                    esc_html_e( 'No', $plugin_txt_domain );
                    ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php 
                    ?>
                                        <div class="wcpoa-field">
                                            <div class="wcpoa-label">
                                                <label for="attchment_order_status"><?php 
                    esc_html_e( 'Order status', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'Select order status for which the attachment(s) will be visible.Leave unselected to apply to all.', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input">
                                                <ul class="wcpoa-checkbox-list">
                                                    <li><label for="wcpoa_wc_order_completed">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-completed" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-completed', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Completed', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_on_hold">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-on-hold" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-on-hold', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'On Hold', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_pending">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-pending" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-pending', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Pending payment', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_processing">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-processing" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-processing', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Processing', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_cancelled">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-cancelled" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-cancelled', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Cancelled', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_failed">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-failed" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-failed', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Failed', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                    <li><label for="wcpoa_wc_order_refunded">
                                                            <input name="wcpoa_order_status[<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>][]"
                                                                   class="" value="wcpoa-wc-refunded" type="checkbox"
                                                                <?php 
                    if ( !is_null( $wcpoa_order_status ) && in_array( 'wcpoa-wc-refunded', $wcpoa_order_status, true ) ) {
                        echo  'checked="checked"' ;
                    }
                    ?>>
                                                            <?php 
                    esc_html_e( 'Refunded', $plugin_txt_domain );
                    ?>
                                                        </label>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="wcpoa-field">
                                            <div class="wcpoa-label">
                                                <label for="wcpoa_expired_date_enable"><?php 
                    esc_html_e( 'Set expire date/time ', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'Expires?', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input enable_expire_date">
                                                <select name="wcpoa_expired_date_enable[]" class="enable_date_time"
                                                        data-type="enable_date_<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?>"
                                                        data-key="">
                                                    <option name="no" <?php 
                    echo  ( $wcpoa_product_date_enable === "no" ? 'selected' : '' ) ;
                    ?>
                                                            value="no" class=""><?php 
                    esc_html_e( 'No', $plugin_txt_domain );
                    ?></option>
                                                    <option name="yes" <?php 
                    echo  ( $wcpoa_product_date_enable === "yes" ? 'selected' : '' ) ;
                    ?>
                                                            value="yes"><?php 
                    esc_html_e( 'Specific Date', $plugin_txt_domain );
                    ?></option>
                                                    <?php 
                    ?>
                                                        <option name="" value="" class="wcpoa_pro_class" disabled><?php 
                    esc_html_e( 'Selected time period after purchase ( Pro Version )', $plugin_txt_domain );
                    ?></option>
                                                    <?php 
                    ?>        
                                                </select>
                                            </div>
                                        </div>
                                         <?php 
                    $is_date = ( $wcpoa_product_date_enable !== 'yes' ? 'none' : '' );
                    ?>   
                                        <div style="display:<?php 
                    echo  esc_attr( $is_date ) ;
                    ?>" class="wcpoa-field enable_date enable_date_<?php 
                    echo  esc_attr( $wcpoa_attachments_id ) ;
                    ?> wcpoa-field-date-picker" data-name="date" data-type="date_picker" data-key="" data-required="1" style=''>
                                            <div class="wcpoa-label">
                                                <label for="wcpoa_expired_date"><?php 
                    esc_html_e( 'Set Date', $plugin_txt_domain );
                    ?></label>
                                                <p class="description"><?php 
                    esc_html_e( 'If an order is placed after the selected date, the attachments will be no longer visible for download', $plugin_txt_domain );
                    ?></p>
                                            </div>
                                            <div class="wcpoa-input">
                                                <div class="wcpoa-date-picker wcpoa-input-wrap"
                                                     data-date_format="yy/mm/dd">
                                                    <input class="input wcpoa-php-date-picker" name="wcpoa_expired_date[]"
                                                           value="<?php 
                    if ( $wcpoa_product_date_enable === "yes" ) {
                        echo  esc_attr( $wcpoa_expired_dates ) ;
                    }
                    ?>"
                                                           type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <?php 
                    ?>        

                                        <?php 
                    echo  wp_kses( $after_fields, $this->allowed_html_tags() ) ;
                    ?>

                                        <?php 
                    
                    if ( $show_remove ) {
                        ?>
                                            <td class="wcpoa-row-handle remove">
                                                <a class="wcpoa-icon -plus small wcpoa-js-tooltip" href="#"
                                                   data-event="add-row"
                                                   title="<?php 
                        esc_html_e( 'Add row', $plugin_txt_domain );
                        ?>"></a>
                                                <a class="wcpoa-icon -minus small wcpoa-js-tooltip" href="#"
                                                   data-event="remove-row"
                                                   title="<?php 
                        esc_html_e( 'Remove row', $plugin_txt_domain );
                        ?>"></a>
                                            </td>
                                        <?php 
                    }
                    
                    ?>

                                    </tr>
                                    <?php 
                }
            
            }
        }
        foreach ( $field['value'] as $i => $row ) {
            $row_att = implode( " ", $row );
            $row_class = 'wcpoa-row trr hidden';
            if ( $i === 'wcpoacloneindex' ) {
                $row_class .= ' wcpoa-clone';
            }
            ?>
                            <tr class="<?php 
            echo  esc_attr( $row_class ) ;
            ?>" rowatt="<?php 
            echo  esc_attr( $row_att ) ;
            ?>" data-id="<?php 
            echo  esc_attr( $i ) ;
            ?>">


                                <?php 
            
            if ( $show_order ) {
                ?>

                                    <td class="wcpoa-row-handle order" title="<?php 
                esc_html_e( 'Drag to reorder', $plugin_txt_domain );
                ?>">
                                        <a class="wcpoa-icon -collapse small" href="#" data-event="collapse-row"
                                           title="<?php 
                esc_html_e( 'Click to toggle', $plugin_txt_domain );
                ?>"></a>
                                        <span><?php 
                echo  intval( $i ) + 1 ;
                ?></span>

                                    </td>
                                <?php 
            }
            
            ?>
                                <td class="wcpoa-fields -left">

                                    <div class="wcpoa-field wcpoa-field-text wcpoa-field-58f4972436131" data-name="id"
                                         data-type="text" data-key="field_58f4972436131">
                                        <div class="wcpoa-label">
                                            <label for=""><?php 
            esc_html_e( 'Id', $plugin_txt_domain );
            ?> </label>
                                            <p class="description"><?php 
            esc_html_e( 'Attachments Id used to identify each product attachment.This value is automatically generated.', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <div class="wcpoa-input-wrap">
                                                <input readonly=""
                                                       class="wcpoa_attachments_id"
                                                       name="wcpoa_attachments_id[]" value="" placeholder=""
                                                       type="text">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="wcpoa-field -collapsed-target">
                                        <div class="wcpoa-label">
                                            <label for="attchment_name"><?php 
            esc_html_e( 'Attachment Name', $plugin_txt_domain );
            ?>
                                                <span class="wcpoa-required"> *</span></label>
                                            <p class="description"><?php 
            esc_html_e( 'Add a product attachment (downloadable files) name like such as technical descriptions, certificates, and licenses, user guides, and manuals, etc. It will be displayed in the front end', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <input class="wcpoa-attachment-name" type="text"
                                                   name="wcpoa_attachment_name[]" value="" >
                                        </div>
                                    </div>
                                    <div class="wcpoa-field wcpoa-field-textarea " data-name="description"
                                         data-type="textarea" data-key="" data-required="1">
                                        <div class="wcpoa-label">
                                            <label for="attchment_desc"><?php 
            esc_html_e( 'Attachment Description', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'You can type a short description of the attachment file. So User will get details about attachment file.', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <textarea class="" name="wcpoa_attachment_description[]" placeholder=""
                                                      rows="8"></textarea>
                                        </div>
                                    </div>
                                    <div class="wcpoa-field">
                                        <div class="wcpoa-label">
                                            <label for="wcpoa_attach_type"><?php 
            esc_html_e( 'Attachment Type', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'Attachment Type?', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input wcpoa_attach_type">
                                            <?php 
            ?>
                                                <select name="wcpoa_attach_type[]" class="wcpoa_attach_type_list" data-type="" data-key="">
                                                    <option name="file_upload" value="file_upload"><?php 
            esc_html_e( 'File Upload', $plugin_txt_domain );
            ?></option>
                                                    <option name="" value="" class="wcpoa_pro_class" disabled><?php 
            esc_html_e( 'External URL ( Pro Version )', $plugin_txt_domain );
            ?></option>
                                                </select>
                                            <?php 
            ?>       
                                        </div>
                                    </div>
                                    <div class="wcpoa-field wcpoa-field-file -collapsed-target file_upload" data-name="file" data-type="file" data-key="field_58f4974e36133" data-required="1">
                                        <div class="wcpoa-label">
                                            <div class="wcpoa-label">
                                                <label for="fee_settings_start_date"><?php 
            esc_html_e( 'Upload Attachment', $plugin_txt_domain );
            ?>
                                                    <span class="wcpoa-required">*</span>
                                                </label>
                                                <p class="description"><?php 
            esc_html_e( 'Upload Attachment File.', $plugin_txt_domain );
            ?></p>
                                            </div>
                                        </div>
                                        <div class="wcpoa-input">
                                            <div class="wcpoa-file-uploader wcpoa-cf" data-uploader="uploader">
                                                <div class="wcpoa-error-message">
                                                    <p><?php 
            echo  'File value is required' ;
            ?></p>
                                                    <input name="wcpoa_attachment_file[]" value="" data-name="id"
                                                           type="hidden">
                                                </div>
                                                <div class="show-if-value file-wrap wcpoa-soh">
                                                    <div class="file-icon">
                                                        <img data-name="icon"
                                                             src="<?php 
            echo  esc_url( $plugin_url ) . 'admin/images/default.png' ;
            ?>"
                                                             alt="" title="">
                                                    </div>
                                                    <div class="file-info">
                                                        <p>
                                                            <strong data-name="title"></strong>
                                                        </p>
                                                        <p>
                                                            <strong>File name:</strong>
                                                            <a data-name="filename" href="" target="_blank"></a>
                                                        </p>
                                                        <p>
                                                            <strong>File size:</strong>
                                                            <span data-name="filesize"></span>
                                                        </p>

                                                        <ul class="wcpoa-hl wcpoa-soh-target">
                                                            <li><a class="wcpoa-icon -pencil dark" data-name="edit"
                                                                   href="#"></a></li>
                                                            <li><a class="wcpoa-icon -cancel dark" data-name="remove"
                                                                   href="#"></a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="hide-if-value">
                                                    <p style="margin:0;">No file selected <?php 
            echo  wp_kses( $this->misha_image_uploader_field( 'test' ), $this->allowed_html_tags() ) ;
            ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
            ?>
                                    <div class="wcpoa-field">
                                        <div class="wcpoa-label">
                                            <label for="product_page_enable"><?php 
            esc_html_e( 'Show on Product page', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'On Product Details page show attachment.', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <select id="wcpoa_product_page_enable"
                                                    name="wcpoa_product_page_enable[]">
                                                <option name="yes" value="yes" selected><?php 
            esc_html_e( 'Yes', $plugin_txt_domain );
            ?></option>
                                                <option name="no" value="no"><?php 
            esc_html_e( 'No', $plugin_txt_domain );
            ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <?php 
            ?>
                                    <div class="wcpoa-field">
                                        <div class="wcpoa-label">
                                            <label for="attchment_order_status"><?php 
            esc_html_e( 'Order status', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'Select order status for which the attachment(s) will be visible.Leave unselected to apply to all.', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <ul class="wcpoa-order-checkbox-list">
                                                <li>
                                                    <label for="wcpoa_wc_order_completed">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-completed" <?php 
            ?>
                                                               type="checkbox"><?php 
            esc_html_e( 'Completed', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_on_hold">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-on-hold"
                                                               type="checkbox"><?php 
            esc_html_e( 'On Hold', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_pending">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-pending"
                                                               type="checkbox"><?php 
            esc_html_e( 'Pending payment', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_processing">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-processing"
                                                               type="checkbox"><?php 
            esc_html_e( 'Processing', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_cancelled">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-cancelled"
                                                               type="checkbox"><?php 
            esc_html_e( 'Cancelled', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_failed">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-failed"
                                                               type="checkbox"><?php 
            esc_html_e( 'Failed', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>
                                                <li>
                                                    <label for="wcpoa_wc_order_refunded">
                                                        <input name="wcpoa_order_status[]" class=""
                                                               value="wcpoa-wc-refunded"
                                                               type="checkbox"><?php 
            esc_html_e( 'Refunded', $plugin_txt_domain );
            ?>
                                                    </label>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="wcpoa-field wcpoa-field-select">
                                        <div class="wcpoa-label">
                                            <label for="wcpoa_expired_date_enable"><?php 
            esc_html_e( 'Set expire date/time', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'Expires?', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input enable_expire_date">
                                            <select name="wcpoa_expired_date_enable[]" class="enable_date_time" data-type="" data-key="">
                                                <option name="no" value="no" class="" selected=""><?php 
            esc_html_e( 'No', $plugin_txt_domain );
            ?></option>
                                                <option name="yes" value="yes"><?php 
            esc_html_e( 'Specific Date', $plugin_txt_domain );
            ?></option>
                                                <?php 
            ?>
                                                    <option name="" value="" class="wcpoa_pro_class" disabled><?php 
            esc_html_e( 'Selected time period after purchase ( Pro Version )', $plugin_txt_domain );
            ?></option>
                                                <?php 
            ?>       
                                            </select>
                                        </div>
                                    </div>
                                    <div class="wcpoa-field enable_date" data-key="" data-required="1" style='display: none'>
                                        <div class="wcpoa-label">
                                            <label for="wcpoa_expired_date"><?php 
            esc_html_e( 'Set Date', $plugin_txt_domain );
            ?></label>
                                            <p class="description"><?php 
            esc_html_e( 'If an order is placed after the selected date, the attachments will be no longer visible for download', $plugin_txt_domain );
            ?></p>
                                        </div>
                                        <div class="wcpoa-input">
                                            <div class="wcpoa-input-wrap" data-date_format="yy/mm/dd">
                                                <!--<input id="" class="input-alt" name="wcpoa_expired_date[]" value=""
                                                       type="hidden">-->
                                                <input class="wcpoa-php-date-picker" value="" name="wcpoa_expired_date[]" type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
            ?>
                                </td>
                                <?php 
            
            if ( $show_remove ) {
                ?>
                                    <td class="wcpoa-row-handle remove">
                                        <a class="wcpoa-icon -plus small wcpoa-js-tooltip" href="#" data-event="add-row"
                                           title="<?php 
                esc_html_e( 'Add row', $plugin_txt_domain );
                ?>"></a>
                                        <a class="wcpoa-icon -minus small wcpoa-js-tooltip" href="#"
                                           data-event="remove-row"
                                           title="<?php 
                esc_html_e( 'Remove row', $plugin_txt_domain );
                ?>"></a>
                                    </td>
                                <?php 
            }
            
            ?>


                            </tr>
                        <?php 
        }
        ?>
                        </tbody>
                    </table>
                    <?php 
        
        if ( $show_add ) {
            ?>

                        <ul class="wcpoa-actions wcpoa-hl">
                            <li>
                                <a class="wcpoa-button button button-primary"
                                   data-event="add-row"><?php 
            esc_html_e( 'Add Attachment', $plugin_txt_domain );
            ?></a>
                            </li>
                        </ul>
                    <?php 
        }
        
        ?>
                </div>
            </div>
        </div>
        <!--File validation-->
        <!--End file validation-->
        <?php 
    }
    
    public function wcpoa_esc_attr_e( $atts )
    {
        echo  wp_kses( $this->wcpoa_esc_attr( $atts ), $this->allowed_html_tags() ) ;
    }
    
    /**
     * Save Meta for post types.
     *
     * @param $product_id
     */
    public function wcpoa_attachment_meta_data( $product_id )
    {
        //  die;
        if ( empty($product_id) ) {
            return;
        }
        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        $post_type = filter_input( INPUT_POST, 'post_type', FILTER_SANITIZE_SPECIAL_CHARS );
        // Check post type is product
        
        if ( isset( $post_type ) && 'product' === $post_type ) {
            $wcpoa_attachments_id = filter_input(
                INPUT_POST,
                'wcpoa_attachments_id',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachments_id = ( !empty($wcpoa_attachments_id) && isset( $wcpoa_attachments_id ) ? $wcpoa_attachments_id : '' );
            update_post_meta( $product_id, 'wcpoa_attachments_id', $wcpoa_attachments_id );
            $wcpoa_attachment_name = filter_input(
                INPUT_POST,
                'wcpoa_attachment_name',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_name = ( !empty($wcpoa_attachment_name) && isset( $wcpoa_attachment_name ) ? $wcpoa_attachment_name : '' );
            update_post_meta( $product_id, 'wcpoa_attachment_name', $wcpoa_attachment_name );
            $wcpoa_attach_type = filter_input(
                INPUT_POST,
                'wcpoa_attach_type',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attach_type = ( !empty($wcpoa_attach_type) && isset( $wcpoa_attach_type ) ? $wcpoa_attach_type : '' );
            update_post_meta( $product_id, 'wcpoa_attach_type', $wcpoa_attach_type );
            $wcpoa_attachment_file = filter_input(
                INPUT_POST,
                'wcpoa_attachment_file',
                FILTER_SANITIZE_STRING,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_file = ( !empty($wcpoa_attachment_file) && isset( $wcpoa_attachment_file ) ? $wcpoa_attachment_file : '' );
            update_post_meta( $product_id, 'wcpoa_attachment_url', $wcpoa_attachment_file );
            $wcpoa_attachment_url = filter_input(
                INPUT_POST,
                'wcpoa_attachment_url',
                FILTER_VALIDATE_URL,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_url = ( !empty($wcpoa_attachment_url) && isset( $wcpoa_attachment_url ) ? $wcpoa_attachment_url : '' );
            update_post_meta( $product_id, 'wcpoa_attachment_ext_url', $wcpoa_attachment_url );
            $wcpoa_attachment_description = filter_input(
                INPUT_POST,
                'wcpoa_attachment_description',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_description = ( !empty($wcpoa_attachment_description) && isset( $wcpoa_attachment_description ) ? $wcpoa_attachment_description : '' );
            update_post_meta( $product_id, 'wcpoa_attachment_description', $wcpoa_attachment_description );
            $wcpoa_order_status = filter_input(
                INPUT_POST,
                'wcpoa_order_status',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_order_status_all = ( !empty($wcpoa_order_status) ? $wcpoa_order_status : 'wc-all' );
            update_post_meta( $product_id, 'wcpoa_order_status', $wcpoa_order_status_all );
            $wcpoa_product_page_enable = filter_input(
                INPUT_POST,
                'wcpoa_product_page_enable',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_product_page_enable = ( !empty($wcpoa_product_page_enable) && isset( $wcpoa_product_page_enable ) ? $wcpoa_product_page_enable : '' );
            update_post_meta( $product_id, 'wcpoa_product_page_enable', $wcpoa_product_page_enable );
            $wcpoa_expired_date_enable = filter_input(
                INPUT_POST,
                'wcpoa_expired_date_enable',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_expired_date_enable = ( !empty($wcpoa_expired_date_enable) && isset( $wcpoa_expired_date_enable ) ? $wcpoa_expired_date_enable : '' );
            update_post_meta( $product_id, 'wcpoa_expired_date_enable', $wcpoa_expired_date_enable );
            $wcpoa_expired_date = filter_input(
                INPUT_POST,
                'wcpoa_expired_date',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_expired_date = ( !empty($wcpoa_expired_date) && isset( $wcpoa_expired_date ) ? $wcpoa_expired_date : '' );
            update_post_meta( $product_id, 'wcpoa_expired_date', $wcpoa_expired_date );
            $wcpoa_variation = filter_input(
                INPUT_POST,
                'wcpoa_variation',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_variation = ( !empty($wcpoa_variation) && isset( $wcpoa_variation ) ? $wcpoa_variation : '' );
            update_post_meta( $product_id, 'wcpoa_variation', $wcpoa_variation );
        }
    
    }
    
    public function wcpoa_attachment_edit_form()
    {
        echo  'enctype="multipart/form-data" novalidate' ;
    }
    
    /**
     * Order wcpoa order meta fields.
     *
     */
    public function wcpoa_order_add_meta_boxes()
    {
        $plugin_txt_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        add_meta_box(
            'wcpoa_order_meta_fields',
            __( 'WooCommerce Product Attachment', $plugin_txt_domain ),
            array( $this, 'wcpoa_order_fields_data' ),
            'shop_order',
            'normal',
            'low'
        );
    }
    
    /**
     * Admin side:Product attachments order data.
     *
     */
    public function wcpoa_order_fields_data()
    {
        global  $post ;
        $wcpoa_order = wc_get_order( $post->ID );
        $order_statuses = wc_get_order_statuses();
        $items = $wcpoa_order->get_items( array( 'line_item' ) );
        $wcpoa_text_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        $wcpoa_att_values_key = array();
        $current_date = date( "Y/m/d" );
        $wcpoa_today_date = strtotime( $current_date );
        $wcpoa_att_values_product_key = array();
        $wcpoa_all_att_values_product_key = array();
        $get_permalink_structure = get_permalink();
        
        if ( strpos( $get_permalink_structure, "?" ) ) {
            $wcpoa_attachment_url_arg = '&';
        } else {
            $wcpoa_attachment_url_arg = '?';
        }
        
        if ( !empty($items) && is_array( $items ) ) {
            foreach ( $items as $item_id => $item ) {
                //single product page attachment
                $wcpoa_order_attachment_items = wc_get_order_item_meta( $item_id, 'wcpoa_order_attachment_order_arr', true );
                
                if ( !empty($wcpoa_order_attachment_items) ) {
                    $wcpoa_attachment_ids = $wcpoa_order_attachment_items['wcpoa_attachment_ids'];
                    $wcpoa_attachment_name = $wcpoa_order_attachment_items['wcpoa_attachment_name'];
                    $wcpoa_attachment_url = $wcpoa_order_attachment_items['wcpoa_attachment_url'];
                    $wcpoa_order_status = $wcpoa_order_attachment_items['wcpoa_order_status'];
                    $wcpoa_order_attachment_expired = $wcpoa_order_attachment_items['wcpoa_order_attachment_expired'];
                    $wcpoa_order_product_variation = "";
                    $selected_variation_id = '';
                    
                    if ( !empty($selected_variation_id) && is_array( $attached_variations ) && in_array( (int) $selected_variation_id, convert_array_to_int( $attached_variations ), true ) ) {
                    } else {
                        foreach ( (array) $wcpoa_attachment_ids as $key => $wcpoa_attachments_id ) {
                            if ( !empty($wcpoa_attachments_id) || $wcpoa_attachments_id !== '' ) {
                                if ( !in_array( $wcpoa_attachments_id, $wcpoa_att_values_key, true ) ) {
                                    
                                    if ( !empty($wcpoa_attachment_ids) ) {
                                        $wcpoa_att_values_key[] = $wcpoa_attachments_id;
                                        $attachment_name = ( isset( $wcpoa_attachment_name[$key] ) && !empty($wcpoa_attachment_name[$key]) ? $wcpoa_attachment_name[$key] : '' );
                                        $wcpoa_attachment_file = ( isset( $wcpoa_attachment_url[$key] ) && !empty($wcpoa_attachment_url[$key]) ? $wcpoa_attachment_url[$key] : '' );
                                        $wcpoa_order_status_val = ( isset( $wcpoa_order_status[$wcpoa_attachments_id] ) && !empty($wcpoa_order_status[$wcpoa_attachments_id]) && $wcpoa_order_status[$wcpoa_attachments_id] ? $wcpoa_order_status[$wcpoa_attachments_id] : array() );
                                        $wcpoa_order_status_new = str_replace( 'wcpoa-', '', $wcpoa_order_status_val );
                                        $wcpoa_expired_dates = ( isset( $wcpoa_order_attachment_expired[$key] ) && !empty($wcpoa_order_attachment_expired[$key]) ? $wcpoa_order_attachment_expired[$key] : '' );
                                        $attachment_id = $wcpoa_attachment_file;
                                        // ID of attachment
                                        echo  '<table class="wcpoa_order">' ;
                                        echo  '<tbody>' ;
                                        $wcpoa_attachment_expired_date = strtotime( $wcpoa_expired_dates );
                                        $attachment_order_name = '<h3 class="wcpoa_attachment_name">' . $attachment_name . '</h3>';
                                        $wcpoa_file_url_btn = '<a class="wcpoa_attachmentbtn" href="' . get_permalink() . $wcpoa_attachment_url_arg . 'attachment_id=' . $attachment_id . '&download_file=' . $wcpoa_attachments_id . '" rel="nofollow">Download</a>';
                                        $wcpoa_file_url_btn = '<a class="wcpoa_attachmentbtn" href="' . get_permalink() . $wcpoa_attachment_url_arg . 'attachment_id=' . $attachment_id . '&download_file=' . $wcpoa_attachments_id . '" rel="nofollow">Download</a>';
                                        $wcpoa_file_expired_url_btn = '<a class="wcpoa_order_attachment_expire" rel="nofollow">Download</a>';
                                        $wcpoa_expire_date_text = '<p class="order_att_expire_date">' . __( 'This Attachment Expire Date Is :: ' ) . $wcpoa_expired_dates . '</p>';
                                        $wcpoa_expired_date_text = '<p class="order_att_expire_date">' . __( 'This Attachment Expired' ) . '</p>';
                                        $wcpoa_never_expired_date_text = '<p class="order_att_expire_date">' . __( 'This Attachment Is Never Expire' ) . '</p>';
                                        
                                        if ( !empty($wcpoa_attachment_expired_date) ) {
                                            
                                            if ( $wcpoa_today_date > $wcpoa_attachment_expired_date ) {
                                                echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                echo  wp_kses( $wcpoa_file_expired_url_btn, $this->allowed_html_tags() ) ;
                                                echo  wp_kses( $wcpoa_expired_date_text, $this->allowed_html_tags() ) ;
                                            } else {
                                                echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                                echo  wp_kses( $wcpoa_expire_date_text, $this->allowed_html_tags() ) ;
                                            }
                                        
                                        } else {
                                            echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                            echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                            echo  wp_kses( $wcpoa_never_expired_date_text, $this->allowed_html_tags() ) ;
                                        }
                                        
                                        echo  '<div class="wcpoa-order-status">' ;
                                        foreach ( $order_statuses as $wcpoa_order_status_key => $wcpoa_order_status_value ) {
                                            
                                            if ( in_array( $wcpoa_order_status_key, $wcpoa_order_status_new, true ) ) {
                                                $order_status_available = '<h4><span class="dashicons dashicons-yes"></span>' . $wcpoa_order_status_value . '</h4>';
                                                echo  wp_kses( $order_status_available, $this->allowed_html_tags() ) ;
                                            } elseif ( empty($wcpoa_order_status_new) ) {
                                                $order_status_available = '<h4><span class="dashicons dashicons-yes"></span>' . $wcpoa_order_status_value . '</h4>';
                                                echo  wp_kses( $order_status_available, $this->allowed_html_tags() ) ;
                                            } else {
                                                $order_status_available = '<h4><span class="dashicons dashicons-no"></span>' . $wcpoa_order_status_value . '</h4>';
                                                echo  wp_kses( $order_status_available, $this->allowed_html_tags() ) ;
                                            }
                                        
                                        }
                                        echo  '</div>' ;
                                        echo  '</tbody>' ;
                                        echo  '</table>' ;
                                    }
                                
                                }
                            }
                        }
                    }
                
                }
            
            }
        }
    }
    
    /* Prints script in footer. This 'initialises' the meta boxes */
    function wcpoa_print_script_in_footer()
    {
        ?>
        <script>jQuery(document).ready(function () {
                postboxes.add_postbox_toggles(pagenow);
            });</script>
        <?php 
    }
    
    function wcpoa_bulk_submitdiv( $post, $args )
    {
        ?>
        <div id="major-publishing-actions">

            <div id="publishing-action">
                <span class="spinner"></span>
                <input type="submit" accesskey="p" value="Publish"
                       class="button button-primary button-large" id="publish" name="submitwcpoabulkatt">
            </div>

            <div class="clear"></div>

        </div>
        <?php 
    }
    
    /**
     * Save option for bulk attachment data save.
     *
     *
     */
    public function wcpoa_bulk_attachment_data_save()
    {
        $wcpoa_attachments_id = filter_input(
            INPUT_POST,
            'wcpoa_attachments_id',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        unset( $wcpoa_attachments_id[count( $wcpoa_attachments_id ) - 1] );
        $wcpoa_attachments_id = ( !empty($wcpoa_attachments_id) ? $wcpoa_attachments_id : '' );
        $wcpoa_attachment_name = filter_input(
            INPUT_POST,
            'wcpoa_attachment_name',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_attachment_name = ( !empty($wcpoa_attachment_name) ? $wcpoa_attachment_name : '' );
        $wcpoa_attach_type = filter_input(
            INPUT_POST,
            'wcpoa_attach_type',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_attach_type = ( !empty($wcpoa_attach_type) ? $wcpoa_attach_type : '' );
        $wcpoa_attachment_file = filter_input(
            INPUT_POST,
            'wcpoa_attachment_file',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_attachment_file = ( !empty($wcpoa_attachment_file) ? $wcpoa_attachment_file : '' );
        $wcpoa_attachment_url = filter_input(
            INPUT_POST,
            'wcpoa_attachment_url',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_attachment_ext_url = ( !empty($wcpoa_attachment_url) ? $wcpoa_attachment_url : '' );
        $wcpoa_attachment_description = filter_input(
            INPUT_POST,
            'wcpoa_attachment_description',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_attachment_description = ( !empty($wcpoa_attachment_description) ? $wcpoa_attachment_description : '' );
        $wcpoa_order_status = filter_input(
            INPUT_POST,
            'wcpoa_order_status',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_order_status_all = ( !empty($wcpoa_order_status) ? $wcpoa_order_status : '' );
        $wcpoa_product_list = filter_input(
            INPUT_POST,
            'wcpoa_product_list',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_product_list = ( !empty($wcpoa_product_list) ? $wcpoa_product_list : '' );
        $wcpoa_category_list = filter_input(
            INPUT_POST,
            'wcpoa_category_list',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_category_list = ( !empty($wcpoa_category_list) ? $wcpoa_category_list : '' );
        $wcpoa_assignment = filter_input(
            INPUT_POST,
            'wcpoa_assignment',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_assignment = ( !empty($wcpoa_assignment) ? $wcpoa_assignment : '' );
        $wcpoa_tag_list = filter_input(
            INPUT_POST,
            'wcpoa_tag_list',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_tag_list = ( !empty($wcpoa_tag_list) ? $wcpoa_tag_list : '' );
        $wcpoa_apply_cat = filter_input(
            INPUT_POST,
            'wcpoa_apply_cat',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_apply_cat = ( !empty($wcpoa_apply_cat) ? $wcpoa_apply_cat : '' );
        $wcpoa_att_visibility = filter_input(
            INPUT_POST,
            'wcpoa_att_visibility',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_att_visibility = ( !empty($wcpoa_att_visibility) ? $wcpoa_att_visibility : '' );
        $wcpoa_is_condition = filter_input(
            INPUT_POST,
            'wcpoa_is_condition',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_is_condition = ( !empty($wcpoa_is_condition) ? $wcpoa_is_condition : '' );
        $wcpoa_expired_date_enable = filter_input(
            INPUT_POST,
            'wcpoa_expired_date_enable',
            FILTER_SANITIZE_SPECIAL_CHARS,
            FILTER_REQUIRE_ARRAY
        );
        $wcpoa_expired_date_enable = ( !empty($wcpoa_expired_date_enable) ? $wcpoa_expired_date_enable : '' );
        
        if ( $wcpoa_expired_date_enable ) {
            $wcpoa_attachment_time_amount = filter_input(
                INPUT_POST,
                'wcpoa_attachment_time_amount',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_time_amount = ( !empty($wcpoa_attachment_time_amount) ? $wcpoa_attachment_time_amount : '' );
            $wcpoa_attachment_time_type = filter_input(
                INPUT_POST,
                'wcpoa_attachment_time_type',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_attachment_time_type = ( !empty($wcpoa_attachment_time_type) ? $wcpoa_attachment_time_type : '' );
            $wcpoa_expired_date = filter_input(
                INPUT_POST,
                'wcpoa_expired_date',
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_REQUIRE_ARRAY
            );
            $wcpoa_expired_date = ( !empty($wcpoa_expired_date) ? $wcpoa_expired_date : '' );
        }
        
        $wcpoa_bulk_attachment_array = [];
        
        if ( !empty($wcpoa_attachments_id) && is_array( $wcpoa_attachments_id ) ) {
            $wcpoa_bulk_attachment_array = [];
            foreach ( $wcpoa_attachments_id as $wcpoa_bulk_key => $wcpoa_bulk_key_value ) {
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachments_id'] = $wcpoa_attachments_id[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_is_condition'] = $wcpoa_is_condition[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_name'] = $wcpoa_attachment_name[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attach_type'] = $wcpoa_attach_type[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_file'] = $wcpoa_attachment_file[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_url'] = $wcpoa_attachment_ext_url[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_description'] = $wcpoa_attachment_description[$wcpoa_bulk_key];
                
                if ( empty($wcpoa_order_status_all[$wcpoa_bulk_key_value]) ) {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_order_status'] = array();
                } else {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_order_status'] = $wcpoa_order_status_all[$wcpoa_bulk_key_value];
                }
                
                $wcpoa_attachment_time_amount = filter_input(
                    INPUT_POST,
                    'wcpoa_attachment_time_amount',
                    FILTER_SANITIZE_SPECIAL_CHARS,
                    FILTER_REQUIRE_ARRAY
                );
                $wcpoa_attachment_time_amount = ( !empty($wcpoa_attachment_time_amount) ? $wcpoa_attachment_time_amount : '' );
                $wcpoa_attachment_time_type = filter_input(
                    INPUT_POST,
                    'wcpoa_attachment_time_type',
                    FILTER_SANITIZE_SPECIAL_CHARS,
                    FILTER_REQUIRE_ARRAY
                );
                $wcpoa_attachment_time_type = ( !empty($wcpoa_attachment_time_type) ? $wcpoa_attachment_time_type : '' );
                $wcpoa_expired_date = filter_input(
                    INPUT_POST,
                    'wcpoa_expired_date',
                    FILTER_SANITIZE_SPECIAL_CHARS,
                    FILTER_REQUIRE_ARRAY
                );
                $wcpoa_expired_date = ( !empty($wcpoa_expired_date) ? $wcpoa_expired_date : '' );
                
                if ( empty($wcpoa_category_list[$wcpoa_bulk_key_value]) ) {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_category_list'] = array();
                } else {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_category_list'] = $wcpoa_category_list[$wcpoa_bulk_key_value];
                }
                
                
                if ( empty($wcpoa_product_list[$wcpoa_bulk_key_value]) ) {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_product_list'] = array();
                } else {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_product_list'] = $wcpoa_product_list[$wcpoa_bulk_key_value];
                }
                
                
                if ( empty($wcpoa_tag_list[$wcpoa_bulk_key_value]) ) {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_tag_list'] = array();
                } else {
                    $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_tag_list'] = $wcpoa_tag_list[$wcpoa_bulk_key_value];
                }
                
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_assignment'] = $wcpoa_assignment[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_apply_cat'] = $wcpoa_apply_cat[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_att_visibility'] = $wcpoa_att_visibility[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_expired_date_enable'] = $wcpoa_expired_date_enable[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_expired_date'] = $wcpoa_expired_date[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_time_amount'] = $wcpoa_attachment_time_amount[$wcpoa_bulk_key];
                $wcpoa_bulk_attachment_array[$wcpoa_bulk_key_value]['wcpoa_attachment_time_type'] = $wcpoa_attachment_time_type[$wcpoa_bulk_key];
            }
        }
        
        update_option( 'wcpoa_bulk_attachment_data', $wcpoa_bulk_attachment_array );
    }
    
    public function misha_image_uploader_field( $attachment_id = '' )
    {
        $image = ' button">Upload image';
        return '
        <div>
            <a href="#" data-id="' . esc_attr( $attachment_id ) . '" class="misha_upload_image_button' . $image . '</a>
           
        </div>';
    }
    
    public function allowed_html_tags( $tags = array() )
    {
        $allowed_tags = array(
            'a'        => array(
            'href'    => array(),
            'title'   => array(),
            'data-id' => array(),
            'class'   => array(),
        ),
            'p'        => array(
            'href'  => array(),
            'title' => array(),
            'class' => array(),
        ),
            'span'     => array(
            'href'  => array(),
            'title' => array(),
            'class' => array(),
        ),
            'ul'       => array(
            'class' => array(),
        ),
            'img'      => array(
            'href'  => array(),
            'title' => array(),
            'class' => array(),
            'src'   => array(),
        ),
            'li'       => array(
            'class' => array(),
        ),
            'h1'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'h2'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'h3'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'h4'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'div'      => array(
            'class'     => array(),
            'id'        => array(),
            "data-max"  => array(),
            "data-min"  => array(),
            "stlye"     => array(),
            "data-name" => array(),
            "data-type" => array(),
            "data-key"  => array(),
        ),
            'select'   => array(
            'id'       => array(),
            'name'     => array(),
            'class'    => array(),
            'multiple' => array(),
            'style'    => array(),
        ),
            'input'    => array(
            'id'    => array(),
            'value' => array(),
            'name'  => array(),
            'class' => array(),
            'type'  => array(),
        ),
            'textarea' => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'td'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'tr'       => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'tbody'    => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'table'    => array(
            'id'    => array(),
            'name'  => array(),
            'class' => array(),
        ),
            'option'   => array(
            'id'       => array(),
            'selected' => array(),
            'name'     => array(),
            'value'    => array(),
        ),
            'br'       => array(),
            'em'       => array(),
            'strong'   => array(),
            'label'    => array(
            'for' => array(),
        ),
        );
        if ( !empty($tags) ) {
            foreach ( $tags as $key => $value ) {
                $allowed_tags[$key] = $value;
            }
        }
        return $allowed_tags;
    }

}