<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Query Products
$params = array(
			'post_type' 		=> 'product',
			'posts_per_page' 	=> -1,
			'orderby' 			=> 'menu_order',
			'order' 			=> 'ASC',
			'post_status'		=> array('publish')
		);
$wc_query = new WP_Query($params);
?>

<?php if ($wc_query->have_posts()) : ?>

<?php include("notice.php");?>

<form id="frm_rwpp" action="<?php echo get_admin_url();?>admin-ajax.php" method='post'>
	<input type="hidden" name="action" value="save_rwpp" />

	<?php 
	// Get Product Category Tree
	$product_categories = get_taxonomy_hierarchy( 'product_tag' );

	if($product_categories){
		print_tags_tree($product_categories);
	}
	?>

	<div class="submit-btn-wrapper">
		<?php submit_button( __('Update Products', 'rwpp'), 'primary', '', '', array('id' => 'btn_save_rwpp')); ?> <div class="spinner"></div>
		<div class="response"></div>
	</div>

</form> 

<?php else:  ?>

<div class="notice notice-warning">
	<p><?php _e('No products found.', 'rwpp');?></p>
</div>

<?php endif; ?>