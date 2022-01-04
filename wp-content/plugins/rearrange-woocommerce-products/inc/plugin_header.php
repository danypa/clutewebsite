<div id="rwpp-container">
	
	<h1><?php _e('Rearrange Woocommerce Products', 'rwpp');?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo admin_url('edit.php?post_type=product&page=rwpp-page');?>" 
		class="nav-tab <?php echo empty($_GET['tab'])?'nav-tab-active':'';?>"
		>Show All Products</a>
		
		<a href="<?php echo admin_url('edit.php?post_type=product&page=rwpp-page&tab=groupby-categories');?>" 
		class="nav-tab <?php echo !empty($_GET['tab'] && $_GET['tab']=='groupby-categories')?'nav-tab-active':'';?>"
		>Group by Categories</a>
		
		<a href="<?php echo admin_url('edit.php?post_type=product&page=rwpp-page&tab=groupby-tags');?>" 
		class="nav-tab <?php echo !empty($_GET['tab'] && $_GET['tab']=='groupby-tags')?'nav-tab-active':'';?>"
		>Group by Tags</a>
	</h2>