<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/public
 * @author     multidots <nirav.soni@multidots.com>
 */
class Woocommerce_Product_Attachment_Public
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
     * @param      string $plugin_name The name of the plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
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
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/woocommerce-product-attachment-public.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
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
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/woocommerce-product-attachment-public.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    // Start the download if there is a request for that
    function wcpoa_download_file()
    {
        $attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_SPECIAL_CHARS );
        $download_file = filter_input( INPUT_GET, 'download_file', FILTER_SANITIZE_SPECIAL_CHARS );
        $wcpoa_attachment_order_id = filter_input( INPUT_GET, 'wcpoa_attachment_order_id', FILTER_SANITIZE_SPECIAL_CHARS );
        
        if ( !empty($attachment_id) && !empty($download_file) && !empty($wcpoa_attachment_order_id) ) {
            $wcpoa_attachment_order_id = $wcpoa_attachment_order_id;
            $order = new WC_Order( $wcpoa_attachment_order_id );
            $items = $order->get_items( array( 'line_item' ) );
            //Bulk Attachement
            if ( isset( $items ) && is_array( $items ) ) {
                foreach ( array_keys( $items ) as $items_key ) {
                    $wcpoa_order_attachment_items = wc_get_order_item_meta( $items_key, 'wcpoa_order_attachment_order_arr', true );
                    $current_date = date( "Y/m/d" );
                    $wcpoa_today_date = strtotime( $current_date );
                    $download_flag = 0;
                    
                    if ( !empty($wcpoa_order_attachment_items) ) {
                        $wcpoa_attachment_ids = $wcpoa_order_attachment_items['wcpoa_attachment_ids'];
                        $wcpoa_order_attachment_expired = $wcpoa_order_attachment_items['wcpoa_order_attachment_expired'];
                        $wcpoa_order_attachment_expired_new = array();
                        $wcpoa_order_attachment_expired_new = array_combine( $wcpoa_attachment_ids, $wcpoa_order_attachment_expired );
                        $download_file = filter_input( INPUT_GET, 'download_file', FILTER_SANITIZE_SPECIAL_CHARS );
                        if ( !empty($wcpoa_order_attachment_expired_new) ) {
                            foreach ( $wcpoa_order_attachment_expired_new as $attach_key => $attach_value ) {
                                if ( $attach_key === $download_file && (strtotime( $attach_value ) >= $wcpoa_today_date || empty($attach_value)) ) {
                                    $download_flag = 1;
                                }
                            }
                        }
                    }
                    
                    if ( $download_flag === 1 ) {
                        $this->wcpoa_send_file();
                    }
                }
            }
            wp_die( sprintf( __( '<strong>This Attachement is Expired.</strong> You are no longer to download this attachement.', 'woocommerce-product-attachment' ) ) );
        } else {
            $this->wcpoa_send_file();
        }
    
    }
    
    public function wcpoa_send_file()
    {
        $attachment_id = filter_input( INPUT_GET, 'attachment_id', FILTER_SANITIZE_SPECIAL_CHARS );
        
        if ( isset( $attachment_id ) ) {
            $attID = $attachment_id;
            $theFile = wp_get_attachment_url( $attID );
            if ( !$theFile ) {
                return;
            }
            $upload_dir = wp_upload_dir();
            //clean the fileurl
            $file_url = stripslashes( trim( $theFile ) );
            //get filename
            $files_arr = explode( "/uploads", $file_url );
            set_time_limit( 0 );
            // disable the time limit for this script
            $path = $upload_dir['basedir'] . $files_arr[1];
            // change the path to fit your websites document structure
            $fullPath = $path;
            
            if ( get_option( 'wcpoa_is_viewable' ) ) {
                $wcpoa_is_viewable = get_option( 'wcpoa_is_viewable' );
                $pdf_download_mode = "attachment";
                if ( 'yes' === $wcpoa_is_viewable ) {
                    $pdf_download_mode = "inline";
                }
            } else {
                $pdf_download_mode = "attachment";
            }
            
            global  $wp_filesystem ;
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
            
            if ( $wp_filesystem->exists( $fullPath ) ) {
                $fsize = filesize( $fullPath );
                $path_parts = pathinfo( $fullPath );
                $ext = strtolower( $path_parts["extension"] );
                switch ( $ext ) {
                    case "pdf":
                        header( "Content-type: application/pdf" );
                        header( "Content-Disposition: {$pdf_download_mode}; filename=\"" . $path_parts["basename"] . "\"" );
                        // use 'attachment' to force a file download
                        break;
                    case "jpg":
                        header( "Content-type: image/jpg" );
                        header( "Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\"" );
                        // use 'attachment' to force a file download
                        header( "Content-Length: {$fsize}" );
                        break;
                    case "png":
                        header( "Content-type: image/png" );
                        header( "Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\"" );
                        // use 'attachment' to force a file download
                        break;
                        // add more headers for other content types here
                    // add more headers for other content types here
                    default:
                        header( "Content-type: application/octet-stream" );
                        header( "Content-Disposition: filename=\"" . $path_parts["basename"] . "\"" );
                        header( 'Accept-Ranges: bytes' );
                        break;
                }
                header( "Cache-control: private" );
                //use this to open files directly
                echo  $wp_filesystem->get_contents( $fullPath ) ;
                //phpcs: ignore
            }
            
            exit;
        }
    
    }
    
    // Adds the new tab
    public function wcpoa_new_product_tab( $tabs )
    {

        global  $post ;
        $product_id = $post->ID;
        $product_tab_name = get_option( 'wcpoa_product_tab_name' );
        $_product = wc_get_product( $product_id );
        $wcpoa_bulk_att_data = "";
        $wcpoa_product_page_enable = get_post_meta( $product_id, 'wcpoa_product_page_enable', true );
        if ( !$_product->is_type( 'grouped' ) ) {
            
            if ( !empty($wcpoa_product_page_enable) || !empty($wcpoa_bulk_att_data) ) {
                $tabs['wcpoa_product_tab'] = array(
                    'title'    => $product_tab_name,
                    'priority' => 50,
                    'callback' => array( $this, 'wcpoa_product_tab_content' ),
                );
                if ( !empty($wcpoa_product_page_enable) ) {
                    foreach ( $wcpoa_product_page_enable as $wcpoa_p_page_enable ) {
                        if ( $wcpoa_p_page_enable === 'yes' ) {
                            return $tabs;
                        }
                    }
                }
                if ( !empty($wcpoa_bulk_att_data) ) {
                    return $tabs;
                }
            }
        
        }
        return $tabs;
    }
    
    /*
     * The wcpoa_new_product_tab tab content
     */
    public function wcpoa_product_tab_content( $attachment_id )
    {
        global  $post ;
        $wcpoa_text_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        do_action( 'before_wcpoa_attachment_detail' );
        // Product edit attachment.
        $product_id = $post->ID;
        $wcpoa_attachment_ids = get_post_meta( $product_id, 'wcpoa_attachments_id', true );
        $wcpoa_attachment_name = get_post_meta( $product_id, 'wcpoa_attachment_name', true );
        $wcpoa_attachment_description = get_post_meta( $product_id, 'wcpoa_attachment_description', true );
        $wcpoa_product_page_enable = get_post_meta( $product_id, 'wcpoa_product_page_enable', true );
        $wcpoa_attach_type = get_post_meta( $product_id, 'wcpoa_attach_type', true );
        $wcpoa_attachment_ext_url = get_post_meta( $product_id, 'wcpoa_attachment_ext_url', true );
        $wcpoa_attachment_url = get_post_meta( $product_id, 'wcpoa_attachment_url', true );
        $wcpoa_expired_date_enable = get_post_meta( $product_id, 'wcpoa_expired_date_enable', true );
        $wcpoa_expired_date = get_post_meta( $product_id, 'wcpoa_expired_date', true );
        $get_permalink_structure = get_permalink();
        $wcpoa_expired_date_tlabel = get_option( 'wcpoa_expired_date_label' );
        $user = wp_get_current_user();
        $wcpoa_att_download_restrict_flag = 1;
        
        if ( strpos( $get_permalink_structure, "?" ) ) {
            $wcpoa_attachment_url_arg = '&';
        } else {
            $wcpoa_attachment_url_arg = '?';
        }
        
        $current_date = date( "Y/m/d" );
        $wcpoa_today_date = strtotime( $current_date );
        $wcpoa_bulk_att_match = 'no';
        
        if ( (int) $wcpoa_att_download_restrict_flag === 1 ) {
            if ( !empty($wcpoa_attachment_ids) ) {
                foreach ( (array) $wcpoa_attachment_ids as $key => $wcpoa_attachments_id ) {
                    
                    if ( !empty($wcpoa_attachments_id) ) {
                        $wcpoa_attachments_name = ( isset( $wcpoa_attachment_name[$key] ) && !empty($wcpoa_attachment_name[$key]) ? $wcpoa_attachment_name[$key] : '' );
                        $wcpoa_attach_type_single = ( isset( $wcpoa_attach_type[$key] ) && !empty($wcpoa_attach_type[$key]) ? $wcpoa_attach_type[$key] : '' );
                        $wcpoa_attachment_file = ( isset( $wcpoa_attachment_url[$key] ) && !empty($wcpoa_attachment_url[$key]) ? $wcpoa_attachment_url[$key] : '' );
                        $wcpoa_attachment_ext_url_single = ( isset( $wcpoa_attachment_ext_url[$key] ) && !empty($wcpoa_attachment_ext_url[$key]) ? $wcpoa_attachment_ext_url[$key] : '' );
                        $wcpoa_product_pages_enable = ( isset( $wcpoa_product_page_enable[$key] ) && !empty($wcpoa_product_page_enable[$key]) ? $wcpoa_product_page_enable[$key] : '' );
                        $wcpoa_expired_dates_enable = ( isset( $wcpoa_expired_date_enable[$key] ) && !empty($wcpoa_expired_date_enable[$key]) ? $wcpoa_expired_date_enable[$key] : '' );
                        $wcpoa_expired_dates = ( isset( $wcpoa_expired_date[$key] ) && !empty($wcpoa_expired_date[$key]) ? $wcpoa_expired_date[$key] : '' );
                        $wcpoa_attachment_descriptions = ( isset( $wcpoa_attachment_description[$key] ) && !empty($wcpoa_attachment_description[$key]) ? $wcpoa_attachment_description[$key] : '' );
                        $attachment_id = $wcpoa_attachment_file;
                        // ID of attachment
                        $wcpoa_attachments_type = get_post_mime_type( $attachment_id );
                        $wcpoa_mime_type = explode( '/', $wcpoa_attachments_type );
                        $wcpoa_att_type = $wcpoa_mime_type['0'];
                        $wcpoa_attachments_icons = WCPOA_PLUGIN_URL . 'public/images/default.png';
                        $wcpoa_attachments_expired_icons = WCPOA_PLUGIN_URL . 'public/images/expired.png';
                        
                        if ( $wcpoa_att_type === 'image' ) {
                            $wcpoa_attachments_icon = WCPOA_PLUGIN_URL . 'public/images/image.png';
                        } elseif ( $wcpoa_attachments_type === 'text/csv' ) {
                            $wcpoa_attachments_icon = WCPOA_PLUGIN_URL . 'public/images/csv.png';
                        } elseif ( $wcpoa_mime_type === 'video' ) {
                            $wcpoa_attachments_icon = WCPOA_PLUGIN_URL . 'public/images/video.png';
                        } elseif ( $wcpoa_mime_type === 'text/xml' ) {
                            $wcpoa_attachments_icon = WCPOA_PLUGIN_URL . 'public/images/xml.png';
                        } elseif ( $wcpoa_mime_type === 'text/doc' ) {
                            $wcpoa_attachments_icon = WCPOA_PLUGIN_URL . 'public/images/doc.png';
                        } else {
                            $wcpoa_attachments_icon = $wcpoa_attachments_icons;
                        }
                        
                        $wcpoa_att_btn = "Ficha Técnica";//__( 'Download', $wcpoa_text_domain );
                        $wcpoa_att_ex_btn = "Ficha Técnica";//__( 'Download', $wcpoa_text_domain );
                        
                        if ( $wcpoa_product_pages_enable === "yes" ) {
                            $wcpoa_attachment_expired_date = strtotime( $wcpoa_expired_dates );
                            $wcpoa_attachments_name = '<h4 class="wcpoa_attachment_name">' . __( $wcpoa_attachments_name, $wcpoa_text_domain ) . '</h4>';
                            $wcpoa_file_url_btn = '<a class="wcpoa_attachmentbtn" href="' . get_permalink() . $wcpoa_attachment_url_arg . 'attachment_id=' . $attachment_id . '&download_file=' . $wcpoa_attachments_id . '" rel="nofollow"> ' . $wcpoa_att_btn . '</a>';
                            $wcpoa_file_expired_url_btn = '<a class="wcpoa_order_attachment_expire" rel="nofollow"> ' . $wcpoa_att_ex_btn . ' </a>';
                            $wcpoa_attachment_descriptions = '<p class="wcpoa_attachment_desc">' . __( $wcpoa_attachment_descriptions, $wcpoa_text_domain ) . '</p>';
                            
                            if ( $wcpoa_expired_date_tlabel === 'no' ) {
                                $wcpoa_expire_date_text = '';
                                $wcpoa_expired_date_text = '';
                            } else {
                                $wcpoa_expire_date_text = '<p class="order_att_expire_date"><span>*</span>' . __( 'This Attachment Expiry Date : ', $wcpoa_text_domain ) . $wcpoa_expired_dates . '</p>';
                                $wcpoa_expired_date_text = '<p class="order_att_expire_date"><span>*</span>' . __( 'This Attachment Expired.', $wcpoa_text_domain ) . '</p>';
                            }
                            
                            echo  '<div class="wcpoa_attachment">' ;
                            
                            if ( 'no' === $wcpoa_expired_dates_enable ) {
                                echo  wp_kses( $wcpoa_attachments_name, $this->allowed_html_tags() ) ;
                                echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                echo  wp_kses( $wcpoa_attachment_descriptions, $this->allowed_html_tags() ) ;
                                $wcpoa_bulk_att_match = 'yes';
                            } else {
                                
                                if ( !empty($wcpoa_attachment_expired_date) ) {
                                    
                                    if ( $wcpoa_today_date > $wcpoa_attachment_expired_date ) {
                                        echo  wp_kses( $wcpoa_attachments_name, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_file_expired_url_btn, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_attachment_descriptions, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_expired_date_text, $this->allowed_html_tags() ) ;
                                        $wcpoa_bulk_att_match = 'yes';
                                    } else {
                                        echo  wp_kses( $wcpoa_attachments_name, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_attachment_descriptions, $this->allowed_html_tags() ) ;
                                        echo  wp_kses( $wcpoa_expire_date_text, $this->allowed_html_tags() ) ;
                                        $wcpoa_bulk_att_match = 'yes';
                                    }
                                
                                } else {
                                    echo  wp_kses( $wcpoa_attachments_name, $this->allowed_html_tags() ) ;
                                    echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                    echo  wp_kses( $wcpoa_attachment_descriptions, $this->allowed_html_tags() ) ;
                                    $wcpoa_bulk_att_match = 'yes';
                                }
                            
                            }
                            
                            echo  '</div>' ;
                        }
                    
                    }
                
                }
            }
            if ( $wcpoa_bulk_att_match !== 'yes' ) {
                ?>
                <style type="text/css">
                    #tab-title-wcpoa_product_tab{
                        display: none !important;
                    }
                </style>
                <?php 
            }
        }
        
        do_action( 'after_wcpoa_attachment_detail' );
    }
    
    /**
     * Product attachments data save in order table.
     *
     * @param $item_id
     * @param $item
     * @param $order_id
     */
    public function wcpoa_add_values_to_order_item_meta( $item_id, $item, $order_id )
    {
        $item_product = new WC_Order_Item_Product( $item );
        $product_id = $item_product->get_product_id();
        $wcpoa_attachment_ids = get_post_meta( $product_id, 'wcpoa_attachments_id', true );
        $wcpoa_attachment_name = get_post_meta( $product_id, 'wcpoa_attachment_name', true );
        $wcpoa_attachment_description = get_post_meta( $product_id, 'wcpoa_attachment_description', true );
        $wcpoa_attachment_url = get_post_meta( $product_id, 'wcpoa_attachment_url', true );
        $wcpoa_attach_type = get_post_meta( $product_id, 'wcpoa_attach_type', true );
        $wcpoa_order_status = get_post_meta( $product_id, 'wcpoa_order_status', true );
        $wcpoa_expired_date_enable = get_post_meta( $product_id, 'wcpoa_expired_date_enable', true );
        $wcpoa_expired_date = get_post_meta( $product_id, 'wcpoa_expired_date', true );
        
        if ( !empty($wcpoa_attachment_ids) ) {
            $wcpoa_order_attachment_order_arr = array(
                'wcpoa_attachment_ids'           => $wcpoa_attachment_ids,
                'wcpoa_attachment_name'          => $wcpoa_attachment_name,
                'wcpoa_att_order_description'    => $wcpoa_attachment_description,
                'wcpoa_attachment_url'           => $wcpoa_attachment_url,
                'wcpoa_attach_type'              => $wcpoa_attach_type,
                'wcpoa_order_status'             => $wcpoa_order_status,
                'wcpoa_expired_date_enable'      => $wcpoa_expired_date_enable,
                'wcpoa_order_attachment_expired' => $wcpoa_expired_date,
            );
            wc_add_order_item_meta( $item_id, 'wcpoa_order_attachment_order_arr', $wcpoa_order_attachment_order_arr );
        }
    
    }
    
    /**
     * Product attachments data show on each order page.
     *
     * @since    1.0.0
     * @access   public
     */
    public function wcpoa_order_data_show( $order_id )
    {
	 return null;
        $wcpoa_text_domain = WCPOA_PLUGIN_TEXT_DOMAIN;
        $order = new WC_Order( $order_id );
        $order_data = $order->get_data();
        $order_time = $order_data['date_created']->date( 'Y/m/d H:i:s' );
        $items = $order->get_items( array( 'line_item' ) );
        $items_order_status = $order->get_status();
        $items_order_id = $order->get_order_number();
        $wcpoa_order_tab_name = get_option( 'wcpoa_order_tab_name' );
        //wcpoa order tab option name
        $wcpoa_expired_date_tlabel = get_option( 'wcpoa_expired_date_label' );
        $wcpoa_attachments_action_on_click = get_option( 'wcpoa_attachments_action_on_click' );
        $get_permalink_structure = get_permalink();
        
        if ( strpos( $get_permalink_structure, "?" ) ) {
            $wcpoa_attachment_url_arg = '&';
        } else {
            $wcpoa_attachment_url_arg = '?';
        }
        
        $current_date = date( "Y/m/d" );
        $wcpoa_today_date = strtotime( $current_date );
        $wcpoa_today_date_time = current_time( 'Y/m/d H:i:s' );
        $wcpoa_end_div = '';
        $wcpoa_att_values_key = array();
        $tab_title_match = 'no';
        $wcpoa_att_in_my_acc = "wcpoa_att_in_my_acc_enable";
        // User role accessibility
        $user = wp_get_current_user();
        $wcpoa_att_download_restrict_flag = 1;
        
        if ( (int) $wcpoa_att_download_restrict_flag === 1 && $wcpoa_att_in_my_acc === "wcpoa_att_in_my_acc_enable" ) {
            echo  '<section class="woocommerce-attachment-details">' ;
            
            do_action( 'before_wcpoa_attachment_detail' );
            if ( $tab_title_match === 'no' ) {
                echo  '<h2 class="woocommerce-order-details__title">' . esc_html( $wcpoa_order_tab_name ) . '</h2>' ;
            }
            $wcpoa_attachments_id_bulk = array();
            if ( !empty($items) && is_array( $items ) ) {
                foreach ( $items as $item_id => $item ) {
                    $wcpoa_order_attachment_items = wc_get_order_item_meta( $item_id, 'wcpoa_order_attachment_order_arr', true );
                    
                    if ( !empty($wcpoa_order_attachment_items) ) {
                        $wcpoa_attachment_ids = $wcpoa_order_attachment_items['wcpoa_attachment_ids'];
                        $wcpoa_attachment_name = $wcpoa_order_attachment_items['wcpoa_attachment_name'];
                        $wcpoa_attachment_description = $wcpoa_order_attachment_items['wcpoa_att_order_description'];
                        $wcpoa_attachment_url = $wcpoa_order_attachment_items['wcpoa_attachment_url'];
                        $wcpoa_attach_type = $wcpoa_order_attachment_items['wcpoa_attach_type'];
                        $wcpoa_order_status = $wcpoa_order_attachment_items['wcpoa_order_status'];
                        $wcpoa_expired_date_enable = $wcpoa_order_attachment_items['wcpoa_expired_date_enable'];
                        $wcpoa_order_attachment_expired = $wcpoa_order_attachment_items['wcpoa_order_attachment_expired'];
                        $selected_variation_id = "";
                        $attached_variations = array();
                        
                        if ( !empty($selected_variation_id) && is_array( $attached_variations ) && in_array( (int) $selected_variation_id, convert_array_to_int( $attached_variations ), true ) ) {
                        } else {
                            if ( !empty($wcpoa_attachment_ids) && is_array( $wcpoa_attachment_ids ) ) {
                                //End Woo Product Attachment Order Tab
                                foreach ( $wcpoa_attachment_ids as $key => $wcpoa_attachments_id ) {
                                    if ( !empty($wcpoa_attachments_id) || $wcpoa_attachments_id !== '' ) {
                                        
                                        if ( !in_array( $wcpoa_attachments_id, $wcpoa_att_values_key, true ) ) {
                                            $wcpoa_att_values_key[] = $wcpoa_attachments_id;
                                            $attachment_name = ( isset( $wcpoa_attachment_name[$key] ) && !empty($wcpoa_attachment_name[$key]) ? $wcpoa_attachment_name[$key] : '' );
                                            $wcpoa_attachment_type = ( isset( $wcpoa_attach_type[$key] ) && !empty($wcpoa_attach_type[$key]) ? $wcpoa_attach_type[$key] : '' );
                                            $wcpoa_attachment_file = ( isset( $wcpoa_attachment_url[$key] ) && !empty($wcpoa_attachment_url[$key]) ? $wcpoa_attachment_url[$key] : '' );
                                            $wcpoa_attachment_descriptions = ( isset( $wcpoa_attachment_description[$key] ) && !empty($wcpoa_attachment_description[$key]) ? $wcpoa_attachment_description[$key] : '' );
                                            $wcpoa_order_status_val = ( isset( $wcpoa_order_status[$wcpoa_attachments_id] ) && !empty($wcpoa_order_status[$wcpoa_attachments_id]) && $wcpoa_order_status[$wcpoa_attachments_id] ? $wcpoa_order_status[$wcpoa_attachments_id] : '' );
                                            $wcpoa_order_status_new = str_replace( 'wcpoa-wc-', '', $wcpoa_order_status_val );
                                            $wcpoa_expired_date_enable = ( isset( $wcpoa_expired_date_enable[$key] ) && !empty($wcpoa_expired_date_enable[$key]) ? $wcpoa_expired_date_enable[$key] : '' );
                                            $wcpoa_order_attachment_expired_date = ( isset( $wcpoa_order_attachment_expired[$key] ) && !empty($wcpoa_order_attachment_expired[$key]) ? $wcpoa_order_attachment_expired[$key] : '' );
                                            $wcpoa_attachment_time_amount_concate_single = "";
                                            $attachment_id = $wcpoa_attachment_file;
                                            // ID of attachment
                                            $wcpoa_attachment_expired_date = strtotime( $wcpoa_order_attachment_expired_date );
                                            $wcpoa_att_btn = __( 'Download', $wcpoa_text_domain );
                                            $wcpoa_att_ex_btn = __( 'Download', $wcpoa_text_domain );
                                            $attachment_order_name = '<h4 class="wcpoa_attachment_name">' . __( $attachment_name, $wcpoa_text_domain ) . '</h4>';
                                            $is_download = ( $wcpoa_attachments_action_on_click === 'download' ? 'download' : 'target="_blank"' );
                                            $wcpoa_file_url_btn = '<a class="wcpoa_attachmentbtn" href="' . get_permalink() . $wcpoa_attachment_url_arg . 'attachment_id=' . $attachment_id . '&download_file=' . $wcpoa_attachments_id . '&wcpoa_attachment_order_id=' . $items_order_id . '" rel="nofollow">' . $wcpoa_att_btn . '</a>';
                                            $wcpoa_file_expired_url_btn = '<a class="wcpoa_order_attachment_expire" rel="nofollow">' . $wcpoa_att_ex_btn . '</a>';
                                            $wcpoa_order_attachment_descriptions = '<p class="wcpoa_attachment_desc">' . __( $wcpoa_attachment_descriptions, $wcpoa_text_domain ) . '</p>';
                                            
                                            if ( $wcpoa_expired_date_tlabel === 'no' ) {
                                                $wcpoa_expire_date_text = '';
                                                $wcpoa_expired_date_text = '';
                                            } else {
                                                $wcpoa_expire_date_text = '<p class="order_att_expire_date"><span>*</span>' . __( 'This Attachment Expiry Date : ', $wcpoa_text_domain ) . $wcpoa_order_attachment_expired_date . '</p>';
                                                $wcpoa_expired_date_text = '<p class="order_att_expire_date"><span>*</span>' . __( 'This Attachment Expired', $wcpoa_text_domain ) . '</p>';
                                            }
                                            
                                            
                                            if ( !empty($wcpoa_order_status_new) ) {
                                                
                                                if ( !empty($wcpoa_attachment_expired_date) && $wcpoa_expired_date_enable === "yes" ) {
                                                    
                                                    if ( $wcpoa_today_date > $wcpoa_attachment_expired_date ) {
                                                        
                                                        if ( in_array( $items_order_status, $wcpoa_order_status_new, true ) ) {
                                                            echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_file_expired_url_btn, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_expired_date_text, $this->allowed_html_tags() ) ;
                                                            $tab_title_match = 'yes';
                                                        }
                                                    
                                                    } else {
                                                        
                                                        if ( in_array( $items_order_status, $wcpoa_order_status_new, true ) ) {
                                                            echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                            echo  wp_kses( $wcpoa_expire_date_text, $this->allowed_html_tags() ) ;
                                                            $tab_title_match = 'yes';
                                                        }
                                                    
                                                    }
                                                
                                                } elseif ( !empty($wcpoa_attachment_time_amount_concate_single) && $wcpoa_expired_date_enable === "time_amount" ) {
                                                } else {
                                                    
                                                    if ( in_array( $items_order_status, $wcpoa_order_status_new, true ) ) {
                                                        echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                        $tab_title_match = 'yes';
                                                    }
                                                
                                                }
                                            
                                            } else {
                                                
                                                if ( !empty($wcpoa_attachment_expired_date) && $wcpoa_expired_date_enable === "yes" ) {
                                                    
                                                    if ( $wcpoa_today_date > $wcpoa_attachment_expired_date ) {
                                                        echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_file_expired_url_btn, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_expired_date_text, $this->allowed_html_tags() ) ;
                                                        $tab_title_match = 'yes';
                                                    } else {
                                                        echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                                        echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                        $tab_title_match = 'yes';
                                                    }
                                                
                                                } elseif ( !empty($wcpoa_attachment_time_amount_concate_single) && $wcpoa_expired_date_enable === "time_amount" ) {
                                                } else {
                                                    echo  wp_kses( $attachment_order_name, $this->allowed_html_tags() ) ;
                                                    echo  wp_kses( $wcpoa_file_url_btn, $this->allowed_html_tags() ) ;
                                                    echo  wp_kses( $wcpoa_order_attachment_descriptions, $this->allowed_html_tags() ) ;
                                                    $tab_title_match = 'yes';
                                                }
                                            
                                            }
                                            
                                            echo  wp_kses( $wcpoa_end_div, $this->allowed_html_tags() ) ;
                                        }
                                    
                                    }
                                }
                            }
                        }
                    
                    }
                
                }
            }
            
            if ( $tab_title_match !== 'yes' ) {
                esc_html_e( 'No attachments...yet! ', $wcpoa_text_domain );
                $tab_title_match = 'yes';
            }
            
            echo  '</section>' ;
            do_action( 'after_wcpoa_attachment_detail' );
        }

        return null;
    }
    
    /*
     * Emails Attachment
     */
    public function wcpoa_woocommerce_email_order_attachment( $fields, $sent_to_admin, $order_id )
    {
        $this->wcpoa_order_data_show( $order_id );
        return $fields;
    }
    
    /*
     * Emails Attachment custome style
     */
    public function wcpoa_woocommerce_email_add_css_to_email_attachment()
    {
        echo  '<st' . 'yle type="text/css">
                        a.wcpoa_attachmentbtn {padding: 10px;background: #35a87b;color: #fff;}
                        a.wcpoa_order_attachment_expire {padding: 10px;background: #ccc;color: #ffffff;cursor: no-drop;box-shadow: none;}
                        .wcpoa_attachment_desc{padding: 8px 0px;}
                        .order_att_expire_date { padding: 8px 0px; }

                </st' . 'yle>' ;
    }
    
    /**
     * BN code added
     *
     * @param $paypal_args
     *
     * @return mixed
     */
    function paypal_bn_code_filter( $paypal_args )
    {
        $paypal_args['bn'] = 'Multidots_SP';
        return $paypal_args;
    }
    
    public function allowed_html_tags( $tags = array() )
    {
        $allowed_tags = array(
            'a'        => array(
            'href'     => array(),
            'title'    => array(),
            'target'   => array(),
            'class'    => array(),
            'download' => array(),
        ),
            'ul'       => array(
            'class' => array(),
        ),
            'li'       => array(
            'class' => array(),
        ),
            'p'        => array(
            'class' => array(),
        ),
            'img'      => array(
            'href'  => array(),
            'title' => array(),
            'class' => array(),
            'src'   => array(),
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

