jQuery(document).ready(function(){
// shorthand no-conflict safe document-ready function
  jQuery(function($) {
    // Hook into the "notice-my-class" class we added to the notice, so
    // Only listen to YOUR notices being dismissed
    $( document ).on( 'click', '.notice-my-class .notice-dismiss', function () {
        // Read the "data-notice" information to track which notice
        // is being dismissed and send it via AJAX
        var type = $( this ).closest( '.notice-my-class' ).data( 'notice' );
        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.ajax( ajaxurl,
          {
            type: 'POST',
            data: {
              action: 'dismissed_notice_handler',
              type: type,
            }
          } );
      } );
  });

}); /*============End of document ready ================*/

