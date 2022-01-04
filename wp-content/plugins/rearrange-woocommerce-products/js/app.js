(function($) {
	
	$( function() {
		// Toggle Product details box
		$("#rwpp-container .menu-item-handle .dashicons-info").click(function (e) { 
			e.preventDefault();
			$(this).parent().parent().find(".product-details-box").slideToggle();
		});
		
		// Make list sortable
		$( ".sortable" ).sortable({
			update: function( event, ui ) {
				update_all_index();
			}
		});
		$( ".sortable" ).disableSelection();

		// Ajax submit form
		$("#frm_rwpp").submit(function(e){
			e.preventDefault();
			
			$("#btn_save_rwpp").attr('disabled', 'true');
			$(".submit-btn-wrapper .spinner").addClass('show');

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: $('#frm_rwpp').serialize(),
				success: function (response) {
					$(".response").html(response);

					$(".submit-btn-wrapper .spinner").removeClass('show');
					$("#btn_save_rwpp").removeAttr('disabled');
				}
			});
		});

		// Expand/Collapse Categories
		$("#rwpp-container ul.product_categories li span.the-product-name").click(function (e) { 
			e.preventDefault();
			$(this).toggleClass('active');
			$(this).next().slideToggle();
		});

		// Move Up Button Action
		$(".moveUp").on('click', function () {
			var current = $(this).parent().parent().parent().parent();
			var prev = current.prev()
			if(prev.length!=0){
				prev.before(current);
				update_all_index();
			}
		});

		// Move Down Button Action
		$(".moveDown").on('click', function () {
			var current = $(this).parent().parent().parent().parent();
			var next = current.next();
			if(next.length!=0){
				next.after(current);
				update_all_index();
			}
		});

		// Move Top Button Action
		$(".moveTop").on('click', function () {
			var current = $(this).parent().parent().parent().parent();
			var first = $(this).parent().parent().parent().parent().parent().find('li').first();
			if(first.length!=0){
				first.before(current);
				update_all_index();
			}
		});

		// Move Bottom Button Action
		$(".moveBottom").on('click', function () {
			var current = $(this).parent().parent().parent().parent();
			var last = $(this).parent().parent().parent().parent().parent().find('li').last();
			if(last.length!=0){
				last.after(current);
				update_all_index();
			}
		});

		// update all product indexes
		function update_all_index(){
			$(".sortable li").each(function (index, element) {
				$(this).find('input[name="new_menu_order[]"]').val(index);
			});
		}
	} );

})(jQuery);