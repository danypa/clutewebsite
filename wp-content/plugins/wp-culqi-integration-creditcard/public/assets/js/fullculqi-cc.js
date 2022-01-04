var fullculqi_cc_isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
var checkout_form = jQuery('form.checkout');
var is_open = false;

checkout_form.on( 'checkout_place_order_fullculqi', function() {

	if ( checkout_form.is( '.processing' ) ) {
		return true;
	}

	if( !is_open && !fullculqi_cc_isSafari && fullculqi_have_validated_fields() ) {

		Culqi.publicKey = fullculqi_cc.public_key;

		var cart_total = jQuery('#checkout_fullculqi_total').val().toString().replace('.', '');

		var args_settings = {
			title: fullculqi_cc.commerce,
			currency: fullculqi_cc.currency,
			description: fullculqi_cc.description,
			amount: cart_total
		};

		Culqi.settings(args_settings);

		args_options = {};

		// If is enable the installments option
		if( fullculqi_cc.installments == 'yes' ) {
			args_options.installments = true;
		}

		// if is set the logo url
		if( fullculqi_cc.url_logo.length > 0 ) {
			args_options.style = { logo : fullculqi_cc.url_logo };	
		}

		if( Object.keys(args_options).length > 0 ) {
			Culqi.options(args_options);
		}

		Culqi.open();

		return false;
	}

	return true;
});


function culqi() {

	checkout_form.addClass( 'processing' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});


	if(Culqi.error) {
		console.log(Culqi.error.user_message);
		checkout_form.unblock();

	} else {

		is_open = true;
		checkout_form.removeClass( 'processing' );
		checkout_form.append('<input type="hidden" name="fullculqi_token_id" value="' + Culqi.token.id + '" />');
		checkout_form.append('<input type="hidden" name="fullculqi_installments" value="' + Culqi.token.metadata.installments + '" />');
		checkout_form.submit();
	}

	Culqi.close();
}


function fullculqi_have_validated_fields() {
	var is_validated = false;

	jQuery.ajax({
		url : fullculqi_cc.url_ajax_validate,
		dataType: 'json',
		type: 'POST',
		async: false,
		data: checkout_form.serialize(),
		success: function (response) {

			if( response.result == 'failure' ) {
				is_validated = false;
				fullculqi_submit_error( response.messages );
			} else
				is_validated = true;
		}
	});

	return is_validated;
}


function fullculqi_submit_error( error_message ) {
	jQuery( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
	checkout_form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error_message + '</div>' );
	checkout_form.removeClass( 'processing' ).unblock();
	checkout_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
	fullculqi_scroll_to_notices();
	jQuery( document.body ).trigger( 'checkout_error' );
}

function fullculqi_scroll_to_notices() {
	var scrollElement = jQuery( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );

	if ( ! scrollElement.length ) {
		scrollElement = jQuery( '.form.checkout' );
	}
	jQuery.scroll_to_notices( scrollElement );
}