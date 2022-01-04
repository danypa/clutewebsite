<?php


if ( file_exists( dirname( __FILE__ ) . '/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/init.php';
}

add_action( 'cmb2_admin_init', 'migthemes_register_metaboxes' );

function migthemes_register_metaboxes() {
	$wooenhancermeta = 'wooenmeta_';



/*================================== Woocommerce metabox ===============================================*/

	$woocommerce_metabox = new_cmb2_box( array(
		'id'            => $wooenhancermeta . 'woocommerce_options',
		'title'         => __( 'WooEnhancer Options', 'cmb2' ),
		'object_types'  => array( 'product', ), // Post type
		'context'    => 'normal',
		'priority'   => 'low',
		// 'show_on_cb' => 'yourprefix_show_if_front_page', // function should return a bool value
		// 'context'    => 'side',
		// 'priority'   => 'high',
		// 'show_names' => true, // Show field names on the left
		// 'cmb_styles' => false, // false to disable the CMB stylesheet
		// 'closed'     => true, // true to keep the metabox closed by default
		// 'classes'    => 'extra-class', // Extra cmb2-wrap classes
		// 'classes_cb' => 'yourprefix_add_some_classes', // Add classes through a callback.
	) );

	$woocommerce_metabox->add_field( array(
		'name'       => __( 'Video Link', 'cmb2' ),
		'desc'       => __( 'Paste url here', 'cmb2' ),
		'id'         => $wooenhancermeta . 'product_video_link',
		'type'       => 'text',
	) );

	$woocommerce_metabox->add_field( array(
		'name'       => __( 'HTML block', 'cmb2' ),
		'desc'       => __( 'Paste code here', 'cmb2' ),
		'id'         => $wooenhancermeta . 'product_html',
		'type'       => 'textarea_code',

	) );

	$woocommerce_metabox->add_field( array(
		'name'       => __( 'WYSIWYG block', 'cmb2' ),
		'desc'       => __( '', 'cmb2' ),
		'id'         => $wooenhancermeta . 'product_wysiwyg',
		'type'    => 'wysiwyg',
		'options' => array(
			'wpautop' => true, // use wpautop?
			'media_buttons' => true, // show insert/upload button(s)
			'textarea_name' =>  'product_wysiwyg',// set the textarea name to something different, square brackets [] can be used here
			'textarea_rows' => get_option('default_post_edit_rows', 10), // rows="..."
			'tabindex' => '',
			'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the `<style>` tags, can use "scoped".
			'editor_class' => '', // add extra class(es) to the editor textarea
			'teeny' => false, // output the minimal editor config used in Press This
			'dfw' => false, // replace the default fullscreen with DFW (needs specific css)
			'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
			'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
		),

	) );

	/*$woocommerce_metabox->add_field( array(
		'name'         => __( 'Product Images (migu)', 'cmb2' ),
		'desc'         => __( 'Upload or add multiple images/attachments.', 'cmb2' ),
		'id'           => $metaboxprefix . 'file_list',
		'type'         => 'file_list',
		'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
	) );
	*/







}
