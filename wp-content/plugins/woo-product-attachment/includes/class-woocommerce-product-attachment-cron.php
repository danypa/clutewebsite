<?php

/**
 * Fired during plugin activation
 *
 * @link       http://www.multidots.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/includes
 */

/**
 * Fired during plugin activation cron.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Attachment
 * @subpackage Woocommerce_Product_Attachment/includes
 * @author     multidots <nirav.soni@multidots.com>
 */
class Woocommerce_Product_Attachment_Activator_Cron {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */

    const WCPOA_MIGRATION_ORDER_CRON = 'wcpoa_migration_new_version_order_cron';
    const WCPOA_MIGRATION_PRODUCT_CRON = 'wcpoa_migration_new_version_product_cron';

    public static function setupCronJob() {

        $wcpoa_upgrade_order_data   = get_option('wcpoa_upgrade_order_data');
        $wcpoa_upgrade_product_data = get_option('wcpoa_upgrade_product_data');
        $wcpoa_upgrade_setting_page = get_option('wcpoa_upgrade_setting_page');

        if (empty($wcpoa_upgrade_setting_page)) {
            $wcpoa_db_upgrade_flag = self::wcpoa_setting_page_migration_script();
            if ($wcpoa_db_upgrade_flag == 1) {
                update_option('wcpoa_upgrade_setting_page', '');
            }
        }
        if (empty($wcpoa_upgrade_order_data)) {
            if ( ! wp_next_scheduled( self::WCPOA_MIGRATION_ORDER_CRON ) ) {
                wp_schedule_event( time(), 'wcpoa_every_one_minute', self::WCPOA_MIGRATION_ORDER_CRON );
            }
        }
        if (empty($wcpoa_upgrade_product_data)) {
            if ( ! wp_next_scheduled( self::WCPOA_MIGRATION_PRODUCT_CRON ) ) {
                wp_schedule_event( time(), 'wcpoa_every_one_minute', self::WCPOA_MIGRATION_PRODUCT_CRON );
            }
        }

    }

    /**
     * Data Migration of setting page on plugin update
     *
     * @return int
     * @since    1.0.0
     *
     * */
    public static function wcpoa_setting_page_migration_script(){
        global $wpdb;
        $setting_pro_name = get_option('wcpoa_pro_product_tab_name');
        $setting_ord_name = get_option('wcpoa_pro_order_tab_name');
        $setting_date_lable = get_option('wcpoa_pro_expired_date_label'); 
        $setting_down_btn = get_option('wcpoa_pro_att_download_btn');
        $setting_down_lable = get_option('wcpoa_pro_att_download_label');
        $setting_url = get_option('wcpoa_pro_att_icon_upload_url');
        $setting_my_acc = get_option( 'wcpoa_pro_att_in_my_acc');
        $setting_down_restric = get_option('wcpoa_pro_att_download_restrict');
        $setting_email = get_option('wcpoa_pro_attachments_show_in_email');
        $setting_action = get_option('wcpoa_pro_attachments_action_on_click');
        $setting_bulk_attachment_data = get_option('wcpoa_pro_bulk_attachment_data');
         
        if ( $setting_pro_name ) {
            update_option('wcpoa_product_tab_name', $setting_pro_name );
            delete_option('wcpoa_pro_product_tab_name' );
        }
        if ( $setting_ord_name ) {
            update_option('wcpoa_order_tab_name', $setting_ord_name );
            delete_option('wcpoa_pro_order_tab_name' );
        }
        if ( $setting_date_lable ) {
            update_option('wcpoa_expired_date_label', $setting_date_lable );
            delete_option('wcpoa_pro_expired_date_label' );
        }
        if ( $setting_down_btn ) {
            if('wcpoa_pro_att_icon' === $setting_down_btn){
                $new_setting_down_btn = 'wcpoa_att_icon';
            }elseif('wcpoa_pro_att_btn' === $setting_down_btn){
                $new_setting_down_btn = 'wcpoa_att_btn';
            }else{
                $new_setting_down_btn = 'wcpoa_att_btn';
            }
            update_option('wcpoa_att_download_btn', $new_setting_down_btn );
            delete_option('wcpoa_pro_att_download_btn' );
        }
        if ( $setting_down_lable ) {
            update_option('wcpoa_att_download_label', $setting_down_lable );
            delete_option('wcpoa_pro_att_download_label' );
        }
        if ( $setting_url ) {
            update_option('wcpoa_att_icon_upload_url', $setting_url );
            delete_option('wcpoa_pro_att_icon_upload_url' );
        }
        if ( $setting_my_acc ) {
            if('wcpoa_pro_att_in_my_acc_enable' === $setting_my_acc){
                $new_setting_my_acc = 'wcpoa_att_in_my_acc_enable';
            }elseif('wcpoa_pro_att_in_my_acc_disable' === $setting_my_acc){
                $new_setting_my_acc = 'wcpoa_att_in_my_acc_disable';
            }else{
                $new_setting_my_acc = 'wcpoa_att_in_my_acc_enable';
            }
            update_option('wcpoa_att_in_my_acc', $new_setting_my_acc );
            delete_option('wcpoa_pro_att_in_my_acc' );
        }
        if ( $setting_email ) {
            update_option('wcpoa_attachments_show_in_email', $setting_email );
            delete_option('wcpoa_pro_attachments_show_in_email' );
        }
        if ( $setting_action ) {
            update_option('wcpoa_attachments_action_on_click', $setting_action );
            delete_option('wcpoa_pro_attachments_action_on_click' );
        }
        if ( $setting_down_restric ) {
            $new_setting_down_restric = str_replace("_pro_", "_", $setting_down_restric);
            update_option('wcpoa_att_download_restrict', $new_setting_down_restric );
            delete_option('wcpoa_pro_att_download_restrict' );
        }

        if ( $setting_bulk_attachment_data ) {
            $new_setting_bulk_attachment_data = [];
            foreach ($setting_bulk_attachment_data as $key=>$value) {

                $change_bulk_attachment_key =array(
                    'wcpoa_pro_attachments_id'          => 'wcpoa_attachments_id',
                    'wcpoa_pro_is_condition'            => 'wcpoa_is_condition',
                    'wcpoa_pro_attachment_name'         => 'wcpoa_attachment_name',
                    'wcpoa_pro_attach_type'             => 'wcpoa_attach_type',
                    'wcpoa_pro_attachment_file'         => 'wcpoa_attachment_file',
                    'wcpoa_pro_attachment_url'          => 'wcpoa_attachment_url',
                    'wcpoa_pro_attachment_description'  => 'wcpoa_attachment_description',
                    'wcpoa_pro_order_status'            => 'wcpoa_order_status',
                    'wcpoa_pro_category_list'           => 'wcpoa_category_list',
                    'wcpoa_pro_product_list'            => 'wcpoa_product_list',
                    'wcpoa_pro_tag_list'                => 'wcpoa_tag_list',
                    'wcpoa_pro_assignment'              => 'wcpoa_assignment',
                    'wcpoa_pro_att_visibility'          => 'wcpoa_att_visibility',
                    'wcpoa_pro_expired_date_enable'     => 'wcpoa_expired_date_enable',
                    'wcpoa_pro_expired_date'            => 'wcpoa_expired_date',
                    'wcpoa_pro_attachment_time_amount'  => 'wcpoa_attachment_time_amount',
                    'wcpoa_pro_attachment_time_type'    => 'wcpoa_attachment_time_type'
                );
                foreach ($change_bulk_attachment_key as $oldkey=>$newkey) {
                    if (array_key_exists($oldkey, $value)) {
                        $value[$newkey] = $value[$oldkey];
                        unset($value[$oldkey]);
                    }
                }
                $new_setting_bulk_attachment_data[$key] = $value;
            }
            update_option('wcpoa_bulk_attachment_data', $new_setting_bulk_attachment_data );
            delete_option('wcpoa_pro_bulk_attachment_data' );
        }
        $wcpoa_db_upgrade_flag = 1;
        return $wcpoa_db_upgrade_flag;
    }
 
    
}








