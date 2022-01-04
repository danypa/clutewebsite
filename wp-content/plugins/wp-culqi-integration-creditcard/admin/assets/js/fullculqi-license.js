jQuery( function( $ ) {

	$('#fullculqi_cc_license').click(function (event) {
		event.preventDefault();

		var loading = '<div style="text-align:center;"><img src="'+fullculqi_cc_license.loading+'" alt="loading" /></div>';

		$('#fullculqi_cc_box_message').removeClass('error').removeClass('success').removeClass('warning');
		$('#fullculqi_cc_box_license').slideDown('fast');
		$('#fullculqi_cc_box_message').html(loading);
		$('#fullculqi_cc_box_button').empty();

		jQuery.post(
			fullculqi_cc_license.ajax,
			{
				action: 'fullculqi_cc_get_status',
				wpnonce: fullculqi_cc_license.nonce
			},
			function(response) {
				var data = jQuery.parseJSON(response);

				$('#fullculqi_cc_box_message').addClass(data.class);
				$('#fullculqi_cc_box_message').html(data.message);

				if( data.isactive ) {
					var iunassined = '<button id="fullculqi_cc_button_unassign">'+fullculqi_cc_license.unbutton+'</button>';
					$('#fullculqi_cc_box_button').html(iunassined);
				}
		});
	});

	$(document).on('click','#fullculqi_cc_button_unassign', function() {

		var loading = '<div style="text-align:center;"><img src="'+fullculqi_cc_license.loading+'" alt="loading" /></div>';

		$('#fullculqi_cc_box_message').removeClass('error').removeClass('success');
		$('#fullculqi_cc_box_message').html(loading);
		$('#fullculqi_cc_box_button').empty();

		jQuery.post(
			fullculqi_cc_license.ajax,
			{
				action: 'fullculqi_cc_set_unassign',
				wpnonce: fullculqi_cc_license.nonce
			},
			function(response) {
				var data = jQuery.parseJSON(response);

				$('#fullculqi_cc_box_message').addClass(data.class);
				$('#fullculqi_cc_box_message').html(data.message);

				setTimeout(window.location.reload.bind(window.location), 300);
		});
	});

	$('#fullculqi_cc_box_close').click(function (event) {
		event.preventDefault();
		$('#fullculqi_cc_box_license').slideUp('fast');
	});

	//close with esc
	$(document).keyup(function(e) {
		if (e.keyCode == 27) {
			$('#fullculqi_cc_box_license').slideUp('fast');
		}
	});
});