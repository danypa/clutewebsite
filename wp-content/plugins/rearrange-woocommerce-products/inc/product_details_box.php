<div class="product-details-box">
	<?php echo $product->get_image();?>
	<div class="the-details">
		<div class="product-name"><strong><?php the_title();?></strong> (ID: <?php echo $post->ID;?>)</div>
		<div class="product-sku">SKU: <?php echo $product->get_sku();?></div>
		<div class="product-price"><?php echo $product->get_price_html();?></div>
		<a href="<?php the_permalink();?>" class="button" target="_blank">View Product</a>
		<a href="<?php echo get_edit_post_link();?>" class="button" target="_blank">Edit Product</a>
	</div>
</div>