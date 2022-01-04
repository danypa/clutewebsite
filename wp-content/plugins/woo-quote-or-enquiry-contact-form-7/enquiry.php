<?php
add_action( 'woocommerce_product_options_general_product_data', 'wqoecf_quote_or_enquiry_disable_option' );
function wqoecf_quote_or_enquiry_disable_option() {


  global $woocommerce, $post;
 
	$wqoecf_disable_form_value=get_post_meta( $post->ID, '_wqoecf_disable_form', true );
	

	woocommerce_wp_checkbox( 
	array( 
		'id'            => '_wqoecf_disable_form',
		'wrapper_class' => 'wqoecf_enquiry', 		
		'label'         => __('Disable Enquiry Form?', 'woocommerce' ), 
		'value'=>$wqoecf_disable_form_value
		)
	);	
	
}

// Save Data
add_action( 'woocommerce_process_product_meta', 'wqoecf_quote_or_enquiry_save_option' );

function wqoecf_quote_or_enquiry_save_option( $post_id ){
	
	$wqoecf_disable_form = isset( $_POST['_wqoecf_disable_form'] ) ? 'yes' : 'no';
	
	update_post_meta( $post_id, '_wqoecf_disable_form', $wqoecf_disable_form );	
	
}

?>