<?php

    /* Template Name: System pages (register, cart, etc) */

?>
<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width" />
<?php wp_head(); ?>
<?php $addstyles = '.system_wrap_type .main-side.full_width{padding: 30px 35px 20px 35px; background: #fff; border-radius: 20px}
input[type="text"], textarea, input[type="tel"], input[type="password"], input[type="email"], input[type="url"], input[type="number"]{box-shadow: inset 0 1px 3px #ddd;font-size: 18px;padding: 12px;line-height: 22px;}';
if (rehub_option('rehub_header_color_background') !=''){
    $addstyles .= 'body{background: none '.rehub_option("rehub_header_color_background").' !important}';
}
else{
    $addstyles .= 'body{background: none white !important}.system_wrap_type .main-side.full_width{box-shadow: 0 0 50px #e3e3e3;}';
}
if(class_exists('Woocommerce') && is_checkout()){
    $addstyles .= '.rh-container{max-width:1100px}';
}else{
    $addstyles .= '.rh-container{max-width:900px}';
}
$addstyles .= '@media (max-width: 500px){
    .post{margin: 0}
    .main-side {min-height: 0;}
    .wcfm-membership-wrapper{width: 100%; margin: 0; box-shadow: none !important;}
    .system_wrap_type .main-side.full_width{padding: 0; box-shadow: none !important;}
}';
wp_register_style( 'rhheader-inline-style', false );
wp_enqueue_style( 'rhheader-inline-style' );
wp_add_inline_style('rhheader-inline-style', $addstyles);
?>  
</head>
<body <?php body_class('whitebg'); ?> id="page-<?php the_ID(); ?>">


<style>
	.overlay {
    background: #e9e9e9; 
    display: none; 
    position: fixed;  
    top: 0;              
    right: 0;            
    bottom: 0;
    left: 0;
    opacity: 0.5;
    z-index: 1000;
}

header.header {
    padding-bottom: 20px;
}

#loading-img{
position: fixed;
   top: 50%;
   left: 50%;
   width: 50px;
   height: 50px;
   margin-top: -25px; /* Half the height */
   margin-left: -25px; /* Half the width */
}
	</style>
	<div class="overlay">
    <div id="loading-img"><img src="/wp-content/themes/rehub-theme/images/loading.gif" ></div>
</div>
<div class="system_wrap_type">
    <div class="mt30 mb20 clearfix"></div>
    <?php if(rehub_option('rehub_logo')) : ?>
        <div class="logo text-center mt30 mb35">
            <a href="<?php echo esc_url(home_url()); ?>" class="logo_image"><img src="<?php echo rehub_option('rehub_logo'); ?>" alt="<?php bloginfo( 'name' ); ?>" height="<?php echo rehub_option( 'rehub_logo_retina_height' ); ?>" width="<?php echo rehub_option( 'rehub_logo_retina_width' ); ?>" /></a>      
        </div>
    <?php elseif (rehub_option('rehub_text_logo')) : ?>
        <div class="textlogo text-center fontbold rehub-main-color font175"><?php echo rehub_option('rehub_text_logo'); ?></div> 
    <?php else : ?>
        <div class="textlogo text-center fontbold rehub-main-color font175"><?php bloginfo( 'name' ); ?></div>   
    <?php endif; ?>    
    <div class="rh-container clearfix mt30 mb30" style="display: flex;  justify-content: center;"> 

<?php
		if(is_account_page() && !is_user_logged_in()){

	?>
		<div class="main-side clearfix full_width" style="max-width:500px">
	<?php }else{ ?>
		<div class="main-side clearfix full_width">
		<?php } ?>


            <article class="post">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; endif; ?>                 
            </article>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html> 
