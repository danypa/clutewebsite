jQuery(document).ready(function(){
	
	
	function wooenhancer_delete_ajax_call(){
		jQuery('.wooenhancer_shop_ajax_call_close').click(function(){
			jQuery('#wooenhancer_shop_ajax_call').remove();	
			jQuery('.wooenhancer-quickview .woocommerce-LoopProduct-link').removeClass('wooenhancer_ajax_loaded_data');
		});
	}
	
	
	
	jQuery.fn.wooenhancer_ajax_call = function($attr){
		jQuery(this).css({display: 'block'});
		var $main = jQuery(this);
		var $pagecalled = jQuery(this).attr($attr);
		var $productID = jQuery(this).next('.add_to_cart_button').attr('data-product_id');
		var $position = jQuery(this).offset();
		var $width = jQuery(this).outerWidth();
		var $documentWidth = jQuery(document).outerWidth();
		var $ajaxwrapper = jQuery('#wooenhancer_shop_ajax_call').length;
		var $windowTop = jQuery(window).scrollTop();
		
		
		jQuery(this).addClass('wooenhancer_ajax_loaded_data');
		if($ajaxwrapper == 0){
			jQuery('body').append('<div id="wooenhancer_shop_ajax_call"><span class="wooenhancer_shop_ajax_call_close"><i class="fa fa-times"></i></span><div id="wooenhancer_shop_ajax_call_inner"></div><div id="wooenhancer_ajax_call_all_html"></div></div>');
		}
		
		jQuery('#wooenhancer_shop_ajax_call').addClass('wooenhancer_ajax_loaded_data');
		
		// Check elemnent position relative to document
		if($position.left < $documentWidth / 2){
			jQuery('#wooenhancer_shop_ajax_call').css({left: $position.left + $width - 2, top: $windowTop + 50})
		}	
		else {
			jQuery('#wooenhancer_shop_ajax_call').css({left: $position.left - 558, top: $windowTop + 50})
		}
		
		//load page, select parts, attach to html, delete data loaded
		if(jQuery('#wooenhancer_shop_ajax_call_inner').find('#product-'+$productID).length == 0){
			jQuery('#wooenhancer_shop_ajax_call_inner').load($pagecalled + ' #product-'+$productID+'', function(){
				jQuery(this).find('.woocommerce-tabs.wc-tabs-wrapper').remove();
				jQuery(this).find('.up-sells.upsells.products').remove();
				jQuery(this).find('.related.products').remove();
				jQuery(this).addClass('wooenhancer_ajax_loaded_data');
				wooenhancer_delete_ajax_call();
				jQuery('#wooenhancer_shop_ajax_call').css({backgroundImage: 'none'});
				jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({hook:"data-rel",social_tools:!1,theme:"pp_woocommerce",horizontal_padding:20,opacity:.8,deeplinking:!1});
				jQuery('.wooenhancer_ajax_loaded_data').on('mouseleave', function(){jQuery(this).wooenhancer_ajax_call_remove();});
			})
		}
		
	}
	
	jQuery.fn.wooenhancer_ajax_call_remove = function(){
		 
			if (jQuery('.wooenhancer_ajax_loaded_data:hover').length == 0) {
				jQuery('#wooenhancer_shop_ajax_call').remove();
				jQuery('.wooenhancer-quickview .woocommerce-LoopProduct-link').removeClass('wooenhancer_ajax_loaded_data');
			}
		
	}
	
	
	
	jQuery('.wooenhancer-quickview .woocommerce-LoopProduct-link').on('mouseenter', function(){jQuery(this).wooenhancer_ajax_call('href');});
	
	
	

	/*================ Ask a Question ==============================*/
	
	jQuery('.wooenhancer_enquire_open_form').on('click', function(){
		jQuery('.wooenhancer_enquire_background').css({display: 'block', visibility: 'visible'});
	})
	
	jQuery('.wooenhancer_enquire_close_form').on('click', function(){
		jQuery('.wooenhancer_enquire_background').css({display: 'none', visibility: 'hidden'});
	})
	
});