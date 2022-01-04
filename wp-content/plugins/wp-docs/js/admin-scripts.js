// JavaScript Document
jQuery(document).ready(function($){
	$('.new-folder').on('click', function(){
		var data = {
			'action': 'wpdocs_create_folder',
			'parent_dir': $(this).data('id'),
		};
		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		$('.ab-new').removeClass('ab-new');
		$.post(wpdocs_ajax_object.ajax_url, data, function(response) {
			
			$('div.wpdocs_list > ul').prepend(response);
		});		
	});
	$('body').on('click', '.wpdocs_list ul li.ab-dir > a.dtitle', function(){
		var obj = $(this);
		var id = obj.parent().data('id');
		var rename_to = prompt(wpdocs_ajax_object.rename_confirm, obj.html());
		
		if($.trim(rename_to)!=''){
			var data = {
				'action': 'wpdocs_update_folder',
				'dir_id': id,
				'new_name': rename_to,
			};
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			$.post(wpdocs_ajax_object.ajax_url, data, function(response) {
				//window.location.reload();
				obj.html(rename_to);
			});			
		}
	});

	$('body').on('click', '.wpdocs_list ul li.ab-dir a.wpd-edit', function(){
		$(this).closest('li.ab-dir').find('a.dtitle').click();
	});

	$('body').on('click', '.wpdocs_list ul li.ab-dir a.wpd-trash', function(){
		var delete_confirm = confirm(wpdocs_ajax_object.wpdocs_delete_msg);
		var obj = $(this);
		if(delete_confirm){
			var data = {
				'action': 'wpdocs_delete_folder',
				'dir_id': $(this).closest('li.ab-dir').data('id'),
			};
			
			$.post(wpdocs_ajax_object.ajax_url, data, function(response) {
				obj.closest('li.ab-dir').fadeOut();	
			});
			
			
		}
	});	
	$('body').on('click', '.wpdocs_list ul li:not(.ab-dir) a.wpd-trash', function(event){
		event.preventDefault();
		var delete_confirm = confirm(wpdocs_ajax_object.del_confirm);
		var obj = $(this);
		if(delete_confirm){
			var id = obj.parents().eq(1).data('dir');
			var attachment_id = obj.parents().eq(1).data('id');
			obj.parents().eq(1).fadeOut();
			var data = {
				'action': 'wpdocs_delete_files',
				'dir_id': id,
				'files': attachment_id,
			};
			//alert(attachment.id);//return;
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			$.post(wpdocs_ajax_object.ajax_url, data, function(response) {
				//window.location.reload();				
				obj.closest('li:not(.ab-dir)').fadeOut();
			});	
		}
	});	

	$('body').on('click', '.wpdocs_list ul li > a.folder', function(){
		window.location.href = 'options-general.php?page=wpdocs&dir='+$(this).parent().data('id');		
	});
	$('body').on('click', '.back-folder', function(){
		window.location.href = 'options-general.php?page=wpdocs&dir='+$(this).data('parent');		
	});
	
	if ($('.new-file:visible').length > 0) {
		if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
			$('.new-file:visible').on('click', function(e) {
				var id = $(this).data('id');
				e.preventDefault();
				//alert(id);alert(attachment.id);return;

				var attachment_ids = [];
				var add_file_status = true;

				wp.media.editor.send.attachment = function(props, attachment) {


					attachment_ids.push(attachment.id);
					//alert(attachment.id);//return;
					// We can also pass the url value separately from ajaxurl for front end AJAX implementations

					if(add_file_status){

						add_file_status = false;

						setTimeout(function(){

							var data = {
								'action': 'wpdocs_add_files',
								'dir_id': id,
								'files': attachment_ids,
							};
							$.post(wpdocs_ajax_object.ajax_url, data, function(response) {
								//window.location.reload();
								//console.log(response);
								if(response!=''){
									$('div.wpdocs_list > ul > li:not(.ab-dir)').remove();
									$('div.wpdocs_list > ul').append(response);
								}
							});

						})

					}


				};
				wp.media.editor.open($(this));
				//return false;
			});
			
		}		
	}

	//save selected file or directory globally every time on selection these will be replaced

	var selected_move_dir = null;
	var selected_move_is_file = false;
	var selected_move_file_dir = null;
	
	$('input[name^="wpdocs_options"]').on('change', function(){
		//console.log($(this));
		//console.log($(this).parents().eq(1));
		$(this).parents().eq(1).find('ul').toggleClass('d-none');
		
		var wpdocs_option_ajax = $('input[name^="wpdocs_options"][value="ajax"]');
		var wpdocs_option_ajax_url = $('input[name^="wpdocs_options"][value="ajax_url"]');


		if(wpdocs_option_ajax.prop('checked') == false){

			wpdocs_option_ajax_url.prop('checked', false);

		}

		var wpdocs_option_checked = $('input[name^="wpdocs_options"][type="checkbox"]:checked');

		wpdocs_options_post = {};


		if(wpdocs_option_checked.length > 0 ){
			$.each(wpdocs_option_checked, function () {

				wpdocs_options_post[$(this).val()] = true;

			});
		}
		
		var wpdocs_option_colors = $('input[name^="wpdocs_options"][type="color"]');

		if(wpdocs_option_colors.length > 0 ){
			$.each(wpdocs_option_colors, function () {

				wpdocs_options_post[$(this).attr('id')] = $(this).val();

			});
		}


		var data = {

			action : 'wpdocs_update_option',
			wpdocs_update_option_nonce : wpdocs_ajax_object.nonce,
			wpdocs_options : wpdocs_options_post,

		}

		$.post(ajaxurl, data, function(code, response){

			//console.log(response);

			if(response == 'success'){

				$('.wpdocs-options .alert').removeClass('d-none').addClass('show');
				setTimeout(function(){
					$('.wpdocs-options .alert').addClass('d-none');
				}, 10000);

			}



		});
		

	});

	function wpdocs_disable_child(dir_id){

		var dir_list_select = $('li.wpdocs_move_folder_to select');
		var dir_list_options = dir_list_select.find('option');
		var current = dir_list_select.find('option[value="'+dir_id+'"]');
		var current_parent = current.data('parent');

		var childs = dir_list_select.find('option[data-parent="'+dir_id+'"]');

		//check if the selected is not a file than disable all child directories
		if (!selected_move_is_file) {

			if (dir_list_options.length > 0) {

				$.each(dir_list_options, function () {

					var this_val = $(this).val();
					var this_parent = $(this).data('parent');

					if (this_val == current_parent) {

						$(this).prop('disabled', true);
						$(this).hide();
					}


					if ($(this).val() == dir_id || $(this).data('parent') == dir_id) {

						$(this).prop('disabled', true);
						$(this).hide();
						// $(this).prop('title', 'Can not move to this directory');

						if (childs.length > 0 && this_val != dir_id) {

							wpdocs_disable_child(this_val);

						}
					}

				});
			}

		} else {

			//if selected is file than disable only its parent directory and root directory

			current = dir_list_select.find('option[value="' + selected_move_file_dir + '"]');
			var current_root = dir_list_select.find('option[value="0"]');
			current.prop('disabled', true);
			current.hide();
			current_root.prop('disabled', true);
			current_root.hide();

		}

	}

	$('body').on('click', '.wpdocs_list ul li a.wpd-move', function(){

		var dir_id = $(this).parents('li').data('id');
		var file_dir = $(this).parents('li').data('dir');
		var is_file = file_dir !== undefined;
		selected_move_file_dir = is_file ? file_dir: null;
		selected_move_is_file = is_file;

		selected_move_dir = dir_id;
		var dir_list_select = $('li.wpdocs_move_folder_to select');
		var dir_list_options = dir_list_select.find('option');
		dir_list_options.prop('disabled', false);
		dir_list_options.show();


		$('li.wpdocs_move_folder_to').show();

		wpdocs_disable_child(selected_move_dir);

	});

	$('body').on('click', 'li.wpdocs_move_folder_to button', function(){

		var selected_dir = $('li.wpdocs_move_folder_to select').val();



		if(selected_dir != -1){

			var wpdocs_move_selected_dir_obj = {

					'dir_selected' : selected_move_dir,
					'dir_id' : selected_dir,
					'files' : selected_move_dir,
					'is_file' : selected_move_is_file,
					'file_dir': selected_move_file_dir,
			};

			var data = {

				action: 'wpdocs_update_option',
				wpdocs_update_option_nonce : wpdocs_ajax_object.nonce,
				wpdocs_move_selected_dir: wpdocs_move_selected_dir_obj

			}


			$.post(ajaxurl, data, function(response) {

				response = JSON.parse(response);

				if(response){

					window.location.href= wpdocs_ajax_object.url+'&dir='+selected_dir ;

				}else{

					alert(wpdocs_ajax_object.move_error);
					$(this).parents('li.wpdocs_move_folder_to').hide();

				}

			});


		}else{

			alert(wpdocs_ajax_object.target_dir_msg);
		}
	});


	
});