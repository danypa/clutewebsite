<?php

if(!defined('ABSPATH')) exit;
	if(isset($_POST['wqoecf_add_form'])){
		$wqoecf_allow_user="";
		$wqoecf_product_single_page="";
		$wqoecf_product_list_page="";
		$wqoecf_status="";
	
		if(isset($_POST['wqoecf_status'])) { $wqoecf_status=sanitize_text_field($_POST['wqoecf_status']); } 
		$wqoecf_select_form=sanitize_text_field($_POST['wqoecf_forms']);
		$wqoecf_color=sanitize_text_field($_POST['wqoecf_btncolor']);
		$wqoecf_text=sanitize_text_field($_POST['wqoecf_btntext']);
		if(isset($_POST['wqoecf-allow-user'])) { $wqoecf_allow_user=sanitize_text_field($_POST['wqoecf-allow-user']); } 
		if(isset($_POST['wqoecf-product-single-page'])) { $wqoecf_product_single_page=sanitize_text_field($_POST['wqoecf-product-single-page']); } 
		if(isset($_POST['wqoecf-product-list-page'])) { $wqoecf_product_list_page=sanitize_text_field($_POST['wqoecf-product-list-page']); } 
		$options['status']=$wqoecf_status;
		$options['allow_user']=$wqoecf_allow_user;
		$options['product_single_page']=$wqoecf_product_single_page;
		$options['product_list_page']=$wqoecf_product_list_page;
		$options['contact_form7']=$wqoecf_select_form;
		$options['button_color']=$wqoecf_color;
		$options['button_text']=$wqoecf_text;
		$nonce=$_POST['wqoecf_wpnonce'];
		if($wqoecf_status=="on" && empty($wqoecf_select_form))
		{
			$validation=failure_option_msg_wqoecf('Please select contact form 7.');
			
		}
		else
		{
			if(wp_verify_nonce( $nonce, 'wqoecf_nonce' ))
			{
				update_option('wqoecf_quote_or_enquiry_settings', $options);
				$successmsg= success_option_msg_wqoecf('Settings Saved!');
				
			}
			else
			{
				$errormsg= failure_option_msg_wqoecf('An error has occurred.');
				
			}
			
		}

	}
$options_db =  wqoecf_quote_enquiry_options();
$status="";
$contactform="";
$color="";
$text="";
$allow_user='';
$product_single_page='';
$product_list_page='';
if(isset($options_db['status'])){
	$status = $options_db['status'];
}
if(isset($options_db['allow_user'])){
	$allow_user = $options_db['allow_user'];
}
if(isset($options_db['product_single_page'])){
	$product_single_page = $options_db['product_single_page'];
}
if(isset($options_db['product_list_page'])){
	$product_list_page = $options_db['product_list_page'];
}
if(isset($options_db['contact_form7'])){
	$contactform = $options_db['contact_form7'];
}
if(isset($options_db['button_color'])){
	$color = $options_db['button_color'];
}
if(isset($options_db['button_text'])){
	$text = $options_db['button_text'];
}
?>

<div class="wrap">

		<h2>WooCommerce Quote or Enquiry Contact Form 7</h2>
		
    <?php
    if ( isset( $successmsg ) ) 
	{
		echo $successmsg; 
    }
	
    if ( isset( $errormsg ) ) 
	{
        echo $errormsg;
    } 
	if ( isset( $validation ) ) 
	{
        echo $validation;
    }
    
    ?>
	

  <div class='wqoecf_inner'>
    	
    <form method="post" name="enquiry_pro" id="enquiry_pro" enctype="multipart/form-data">
        
		<table class="form-table">
		
		<tbody>

			<tr valign="top">

				<th scope="row">Status</th>
				
				<td>			
					<label class="wqoecf-switch wqoecf-switch-quote_status">							  
						<input type="checkbox" id="wqoecf-status" name="wqoecf_status" value="on" <?php if($status=='on'){echo "checked";}?> >							  
						<span class="wqoecf-slider wqoecf-round"></span>					
					</label>
				</td>
			</tr>

			<tr valign="top">

				<th scope="row">Allow Guest User</th>
				<td>
								
					<label class="wqoecf-switch wqoecf-switch-quote_status">							  
						<input type="checkbox" id="wqoecf-allow-user" name="wqoecf-allow-user" value="on" <?php if($allow_user=='on'){echo "checked";}?> >							  
						<span class="wqoecf-slider wqoecf-round"></span>					
					</label>
					
					<span class="tooltip-msg">if Enable it will be guest user to get Enquiry without registration.</span>
				</td>
			</tr>
			<tr valign="top">

				<th scope="row">Product Single Page</th>
				
				<td>				
					<label class="wqoecf-switch wqoecf-switch-quote_status">							  
						<input type="checkbox" id="wqoecf-product-single-page" name="wqoecf-product-single-page" value="on" <?php if($product_single_page=='on'){echo "checked";}?> >							  
						<span class="wqoecf-slider wqoecf-round"></span>					
					</label>
					<span class="tooltip-msg">if Enable Enquiry Button will be Display on Product Single Page.</span>
				</td>
			</tr>
			<tr valign="top">

				<th scope="row">Product List Page</th>
				
				<td>				
					<label class="wqoecf-switch wqoecf-switch-quote_status">							  
						<input type="checkbox" id="wqoecf-product-list-page" name="wqoecf-product-list-page" value="on" <?php if($product_list_page=='on'){echo "checked";}?> >							  
						<span class="wqoecf-slider wqoecf-round"></span>					
					</label>
					<span class="tooltip-msg">if Enable Enquiry Button will be Display on Product List Page.</span>
				</td>
			</tr>
			<tr valign="top">

				<th scope="row">Select Contact Form 7</th>

				<td>							
					<select name="wqoecf_forms" id="wqoecf-forms"> 
						<option value="">--Select--</option>
						<?php
							$wqoecf_get_form = get_posts(array(
								'post_type'     => 'wpcf7_contact_form'
							));
						foreach ( $wqoecf_get_form as $wqoecf_form ) {
							echo '<option value="'.$wqoecf_form->ID.'"'.selected($wqoecf_form->ID,$contactform,false).'>'.$wqoecf_form->post_title.' ('.$wqoecf_form->ID.')'.'</option>';
						} ?>
					</select>	
                </td>
			</tr>
			<tr valign="top">
				<th scope="row">Button Color</th>
				<td>
					<input type="text" name="wqoecf_btncolor" class="wqoecf_colorpicker" value="<?php echo $color; ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Button Text</th>
				<td>
					<input type="text" name="wqoecf_btntext" class="wqoecf_btntext" value="<?php echo $text; ?>">
				</td>
			</tr>
            </tbody>
		</table>
		
				<input type="hidden" name="wqoecf_wpnonce" value="<?php echo $nonce= wp_create_nonce('wqoecf_nonce'); ?>">
					
            	<input class="button button-primary button-large wqoecf_submit" type="submit" name="wqoecf_add_form" id="wqoecf_submit" value="Save"/>
            	
          
    	</form>
    </div>
</div>
<script>
	(function($) {
		// Add Color Picker to all inputs that have 'color-field' class
		$(function() {
			$('.wqoecf_colorpicker').wpColorPicker();
		});

	})(jQuery);
	   
</script>
