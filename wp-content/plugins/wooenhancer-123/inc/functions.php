<?php
global $migwoo_enhancer;

/*=========================== DEACTIVATE CHECKOUT BILLING && SHIPPING FIELDS =================================*/

if(!function_exists('migwoo_enhancer_dequeue_checkout_fields')){
	function migwoo_enhancer_dequeue_checkout_fields($fields = NULL){
		global $migwoo_enhancer;

		$checkoutdisabled = array(
			'billing' => $migwoo_enhancer['checkout-billing-fields']['disabled'],
			'shipping' => $migwoo_enhancer['checkout-shipping-fields']['disabled'],
			'order'	=> $migwoo_enhancer['checkout-order-fields']['disabled']
		);


		if(isset($checkoutdisabled)){

			foreach($checkoutdisabled as $unset_key => $unset_value){
				foreach($unset_value as $unset_field_key => $unset_field_value){

					if($unset_field_value != 'placebo'){

						unset($fields[$unset_key][$unset_field_key]);

					}
				}


			}
		}



		return $fields;

	}

}

add_filter( 'woocommerce_checkout_fields' , 'migwoo_enhancer_dequeue_checkout_fields' );


/*=============================== Change Checkout Labels ==============================*/

if(!function_exists('migwoo_enhancer_checkout_labels')){
	function migwoo_enhancer_checkout_labels($fields = NULL){
		global $migwoo_enhancer;

		if(!empty($migwoo_enhancer['billing_first_name_label'])){
			$fields['billing']['billing_first_name']['label'] = $migwoo_enhancer['billing_first_name_label'];
		}
		if(!empty($migwoo_enhancer['billing_last_name_label'])){
			$fields['billing']['billing_last_name']['label'] = $migwoo_enhancer['billing_last_name_label'];
		}
		if(!empty($migwoo_enhancer['billing_company_label'])){
			$fields['billing']['billing_company']['label'] = $migwoo_enhancer['billing_company_label'];
		}
		if(!empty($migwoo_enhancer['billing_address_1_label'])){
			$fields['billing']['billing_address_1']['label'] = $migwoo_enhancer['billing_address_1_label'];
		}
		if(!empty($migwoo_enhancer['billing_city_label'])){
			$fields['billing']['billing_city']['label'] = $migwoo_enhancer['billing_city_label'];
		}
		if(!empty($migwoo_enhancer['billing_postcode_label'])){
			$fields['billing']['billing_postcode']['label'] = $migwoo_enhancer['billing_postcode_label'];
		}
		if(!empty($migwoo_enhancer['billing_country_label'])){
			$fields['billing']['billing_country']['label'] = $migwoo_enhancer['billing_country_label'];
		}
		if(!empty($migwoo_enhancer['billing_state_label'])){
			$fields['billing']['billing_state']['label'] = $migwoo_enhancer['billing_state_label'];
		}
		if(!empty($migwoo_enhancer['billing_email_label'])){
			$fields['billing']['billing_email']['label'] = $migwoo_enhancer['billing_email_label'];
		}
		if(!empty($migwoo_enhancer['billing_phone_label'])){
			$fields['billing']['billing_phone']['label'] = $migwoo_enhancer['billing_phone_label'];
		}

		return $fields;

	}

}

add_filter( 'woocommerce_checkout_fields' , 'migwoo_enhancer_checkout_labels' );



/*============================ Change Billing && Shipping Fields Order ==============================*/



if(!function_exists('migwoo_enhancer_order_checkout_fields')){

	function migwoo_enhancer_order_checkout_fields($fields = NULL){
		global $migwoo_enhancer;


		if(!empty($migwoo_enhancer['checkout-billing-fields']['enabled'])){
			$order = $migwoo_enhancer['checkout-billing-fields']['enabled'];
			if(isset($order)){
				foreach($order as $order_key => $order_value){
					if($order_value != 'placebo'){
						$billing_ordered_fields[$order_key] = $fields["billing"][$order_key];
					}
				}
			}

			$fields["billing"] = $billing_ordered_fields;
		}
		if(!empty($migwoo_enhancer['checkout-shipping-fields']['enabled'])){
			$order = $migwoo_enhancer['checkout-shipping-fields']['enabled'];
			if(isset($order)){
				foreach($order as $order_key => $order_value){
					if($order_value != 'placebo'){
						$shipping_ordered_fields[$order_key] = $fields["shipping"][$order_key];
					}
				}
			}

			$fields["shipping"] = $shipping_ordered_fields;
		}

		return $fields;


	}

}
add_filter( 'woocommerce_checkout_fields' , 'migwoo_enhancer_order_checkout_fields' );


/*=================== deactivate password strength ============================*/

if(!function_exists('migwoo_enhancer_remove_password_strength')){
	function migwoo_enhancer_remove_password_strength() {
		global $migwoo_enhancer;
		$passwordstrength = $migwoo_enhancer['password-strength'];

		if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) && $passwordstrength == 'disabled') {
			wp_dequeue_script( 'wc-password-strength-meter' );
		}
	}
}
add_action( 'wp_print_scripts', 'migwoo_enhancer_remove_password_strength', 100 );


/*============================= add to cart button text ==========================================*/

if(!function_exists('migwoo_enhancer_custom_cart_button_text')){
	function migwoo_enhancer_custom_cart_button_text() {

		global $migwoo_enhancer;
		$addtocart = 'Add to cart';
		if(!empty($migwoo_enhancer['add-to-cart-text'])){$addtocart = $migwoo_enhancer['add-to-cart-text'];}

			return __( $addtocart, 'woocommerce' );
	}
}

	add_filter( 'woocommerce_product_single_add_to_cart_text', 'migwoo_enhancer_custom_cart_button_text' );


/*========================== REMOVE HOOKS SINGLE PRODUCT PAGE ========================*/
function migwoo_enhancer_remove_hooks(){
	global $migwoo_enhancer;
	if($migwoo_enhancer['activate-product-page-hooks'] == 1){
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

		remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
		remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
		remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);


		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);


		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
	}

/*========================== REMOVE HOOKS SHOP PRODUCT PAGE ========================*/
	if($migwoo_enhancer['activate-shop-page-hooks'] == 1){
		remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
		remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
		remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
	}

}
add_action('after_setup_theme', 'migwoo_enhancer_remove_hooks', 20);


/*========================== ADD AND SORTER NEW HOOKS ==============================*/
function migwoo_enhancer_add_hooks(){
	global $migwoo_enhancer;
	$before_summary = $thumbnails_area = $summary = $after_summary = $before_shop = $after_shop = array();
	$migwoo_all_hooks = array();

	if($migwoo_enhancer['activate-product-page-hooks'] == 1){
		$migwoo_all_hooks['woocommerce_before_main_content'] = $migwoo_enhancer['single-product-hooks']['before_content'];
		$migwoo_all_hooks['woocommerce_before_single_product_summary'] = $migwoo_enhancer['single-product-hooks']['before_summary'];
		$migwoo_all_hooks['woocommerce_product_thumbnails'] = $migwoo_enhancer['single-product-hooks']['thumbnails_area'];
		$migwoo_all_hooks['woocommerce_single_product_summary'] = $migwoo_enhancer['single-product-hooks']['summary'];
		$migwoo_all_hooks['woocommerce_after_single_product_summary'] = $migwoo_enhancer['single-product-hooks']['after_summary'];
		$migwoo_all_hooks['wooenhancer_full_width'] = $migwoo_enhancer['single-product-hooks']['full_width'];
	}





	if($migwoo_enhancer['activate-shop-page-hooks'] == 1){
		$migwoo_all_hooks['woocommerce_before_shop_loop'] = $migwoo_enhancer['shop-page-hooks']['before_shop'];
		$migwoo_all_hooks['woocommerce_after_shop_loop'] = $migwoo_enhancer['shop-page-hooks']['after_shop'];
	}


	if(!empty($migwoo_all_hooks)){
		foreach($migwoo_all_hooks as $all_key => $all_value){
			if(!empty($all_value)){
				foreach($all_value as $all_value_key => $all_value_value){

					if($all_value_key != 'placebo'){
						add_action($all_key, $all_value_key, 27);
					}
				}
			}
		}

	}



}


add_action('init', 'migwoo_enhancer_add_hooks');


/*========================= custom translations (not active)=====================================*/
function wooenhancer_billing_field_strings( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        case 'Billing Address' :
            $translated_text = __( 'Billing Info', 'woocommerce' );
            break;
    }
    return $translated_text;
}
add_filter( 'gettext', 'wooenhancer_billing_field_strings', 20, 3 );



/*======================== checkout custom message ==================================*/

function migwoo_enhancer_checkout_custom_message(){
	global $migwoo_enhancer;
	$output = '';

	if(!empty($migwoo_enhancer['checkout-custom-message'])){
		$output .= '<div class="migwoo_enhancer_custom_message clearfix">';
			$output .= $migwoo_enhancer['checkout-custom-message'];
		$output .= '</div>';
	}
	echo $output;
}
add_action('woocommerce_review_order_before_payment', 'migwoo_enhancer_checkout_custom_message');



//Old pro function released for free



/*=================== product main video ===============================*/
function wooenhancer_product_main_video(){
	global $post;
	global $wooenhancermeta;

	echo wp_oembed_get(get_post_meta($post->ID, 'wooenmeta_product_video_link', true));

}

/*=================== product html ===============================*/
function wooenhancer_product_html_block(){
	global $post;
	global $wooenhancermeta;
	$output = '<div class="wooenhancer_product_html">';
	$output .= get_post_meta($post->ID, 'wooenmeta_product_html', true);
	$output .= '</div>';

	echo $output;
}

/*=================== product wysiwyg ===============================*/
function wooenhancer_product_wysiwyg_block(){
	global $post;
	global $wooenhancermeta;

	$output = '<div class="wooenhancer_product_wysiwyg">';
	$output .= do_shortcode(get_post_meta($post->ID, 'wooenmeta_product_wysiwyg', true));
	$output .= '</div>';

	echo $output;
}


/*======================== catalog quickview ==============================*/

add_filter( 'body_class','wooenhancer_quickview' );
function wooenhancer_quickview( $classes ) {
 	global $migwoo_enhancer;
	$quickview = $migwoo_enhancer['shop-activate-quickview'];

	if(is_shop() && !empty($quickview) && $quickview != 'no'){
    	$classes[] = 'wooenhancer-quickview';
    }

    return $classes;

}




// DISPLAY WOOCOMMERCE TOTAL SALES

function wooenhancer_product_total_sales(){
	global $post;
	$total = get_post_meta($post->ID,'total_sales', true);
	$output = '';

	$output .= '<div class="wooenhancer_product_total_sales">';
		$output .= __($total.' Sales', 'woocommerce');
	$output .= '</div>';

	echo $output;
}


// REGISTER FULL SINGLE PRODUCT AREA
add_action('woocommerce_before_single_product', 'wooenhancer_full_width');
function wooenhancer_full_width(){
	do_action('wooenhancer_full_width');
}


/*=================== ENQUIRE =======================*/

function wooenhancer_enquire(){
	global $post;
	global $migwoo_enhancer;

	$output = '';

	$output .= '<div class="wooenhancer_enquire_open_form">'.$migwoo_enhancer['enquire-button-text'].'</div>';

	$output .= '<div class="wooenhancer_enquire_background">';
		$output .= '<div class="wooenhancer_enquire_wrapper clearfix">';
			$output .= '<p class="migwooenhancer_enquire_custom_message">'.$migwoo_enhancer['enquire-custom-message'].'</p>';
			$output .= '<form action="" method="POST">';
				$output .= '<span>Name</span><input type="text" name="name">';
				$output .= '<span>Email</span><input type="text" name="email">';
				$output .= '<span>Message</span><textarea name="message" rows="4" cols="30">';
				$output .= '</textarea>';
				$output .= '<input type="submit" value="Send">';
			$output .= '</form>';
		$output .= '</div>';
	$output .= '</div>';

	if(!empty($_POST['name'])){
		$name = $_POST['name'];

	}
	if(!empty($_POST['message'])){
		$email = $_POST['email'];


	}
	if(!empty($_POST['message'])){
		$message = $_POST['message'];
		$formcontent = "$message";
	}

	$recipient = $migwoo_enhancer['enquire-email'];
	$subject = "Question About:";


	if(!empty($name) && !empty($email) && !empty($message)){
		$mailheader = 'From: $name \r\n Reply-To: $email \r\n';
		mail($recipient, $subject, $formcontent, $mailheader) or die("Error!");
		echo '<div class="woocommerce-message">'.$migwoo_enhancer['enquire-success-message'].'</div>';
		$_POST = array();
	}

	echo $output;
}

?>
