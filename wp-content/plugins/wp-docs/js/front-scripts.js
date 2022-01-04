// JavaScript Document
jQuery(document).ready(function($){


	var is_pro = (wpdocs.wpdocs_pro=='1');
	var history_state = wpdocs.is_ajax_url;


	function wpd_load_dir_simple(dir_id){
		
		//console.log(dir_id);

		var separator = '?';
		if(wpdocs.this_url.includes('?')){
			separator = '&';
		}
		if(dir_id === 0){

			window.location.href = wpdocs.this_url;

		}else{

			window.location.href = wpdocs.this_url+separator+'dir='+dir_id;

		}
	}

	$('body').on('click', '.file_wrapper.is_dir, .wpd_bread_item', function(e){

		e.preventDefault();
		var dir_id = $(this).data('id');


		if(is_pro && wpdocs.is_ajax=='1'){

			wpd_load_dir_ajax(dir_id);

		}else{


			wpd_load_dir_simple(dir_id);

		}

	});






	$('body').on('mouseover','figure.file_view', function(){
		$(this).addClass('bg-dark text-white rounded');
		$(this).find('.figure-caption').addClass('text-white');
	});

	$('body').on('mouseout','figure.file_view', function(){
		$(this).removeClass('bg-dark text-white rounded');
		$(this).find('.figure-caption').removeClass('text-white');
	});
	$('body').on('click','figure.file_view', function(){
		if($(this).find('a').length>0){
			window.open($(this).find('a').attr('href'), '_blank');
		}
	});
	$('body').on('click','figure.file_view', function(event){
		event.preventDefault();

	});
	$('body').on('click','tr.file_view', function(){
		if(typeof $(this).data('url')!='undefined' && $(this).data('url')!=''){
			window.open($(this).data('url'), '_blank');
		}
	});
	$('body').on('click','.folder_view_btn', function(){

		$('.folder_view').addClass('d-none');
		var data_class = $(this).data('source');
		$('.'+data_class).removeClass('d-none');
	});

	$('body').on('click','.folder_view_btn', function(){

		$('.folder_view').addClass('d-none');
		var view_source = $(this).data('source');
		$('.'+view_source).removeClass('d-none');

	});


	if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
		$('body').on('click','.wpdocs-front-add-media:visible',  function(e) {
			var id = $('.wpdoc_container').data('dir');
			e.preventDefault();

			//alert(id);alert(attachment.id);return;

			if(id != 0){

				var attachment_ids = [];
				var add_file_status = true;

				wp.media.editor.send.attachment = function(props, attachment) {

					attachment_ids.push(attachment.id);

					if(add_file_status){

						add_file_status = false;

						setTimeout(function(){

							var data = {
								'action': 'wpdocs_add_files',
								'dir_id': id,
								'files': attachment_ids,
							};
							$.post(wpdocs.ajax_url, data, function(response) {

								if(is_pro){

									wpd_load_dir_ajax(id);

								}else{


									wpd_load_dir_simple(id);

								}

							});

						})

					}



				};
				wp.media.editor.open($(this));
			}
			//return false;
		});

	}

	$('body').on('click', '.wpdocs-views a', function(){
		//console.log($(this).data('source'));
		var data = {
			'action': 'wpdocs_update_view',
			'update_view': $(this).data('source'),
		};
		$.post(wpdocs.ajax_url, data, function(response) {	
		
		});
	});

});