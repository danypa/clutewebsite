<?php  	function migwoo_enhancer_dynamic_styles(){?>
<!-- Load dynamic styles from backend -->
<?php 
global $migwoo_enhancer;

?>

<style type="text/css">
/*======================= General ================================*/
.woocommerce-MyAccount-content- {
font-family:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['font-family']?>; 
font-size:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['font-size']?>; 
color:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['color']?>; 
font-weight:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['font-weight']?>; 
font-style:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['font-style']?>;
line-height:<?php echo $migwoo_enhancer['migwoo_enhancer_main_typography']['line-height']?>;
}

/***Shop design***/
<?php if($migwoo_enhancer['activate-shop-page-design'] == 1 && is_shop()):?>


/*============= breadcrumb =================*/
.woocommerce-breadcrumb {text-align: <?php echo $migwoo_enhancer['shop-breadcrumb-alignment']?> !important; color: <?php echo $migwoo_enhancer['shop-breadcrumb-color']?> !important; font-size: <?php echo $migwoo_enhancer['shop-breadcrumb-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['shop-breadcrumb-transform']?> !important;}

/*=================== Shop Title =======================*/
.post-type-archive-product h1.page-title {text-align: <?php echo $migwoo_enhancer['shop-title-alignment']?> !important; color: <?php echo $migwoo_enhancer['shop-title-color']?> !important; font-size: <?php echo $migwoo_enhancer['shop-title-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['shop-title-transform']?> !important;}


/*=================== Shop results count =======================*/
.woocommerce-result-count {text-align: <?php echo $migwoo_enhancer['shop-result-alignment']?> !important; color: <?php echo $migwoo_enhancer['shop-result-color']?> !important; font-size: <?php echo $migwoo_enhancer['shop-result-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['shop-result-transform']?> !important;}

/*=================== Shop order =======================*/
.woocommerce-ordering select {color: <?php echo $migwoo_enhancer['shop-order-color']?> !important; background-color: <?php echo $migwoo_enhancer['shop-order-background-color']?> !important; font-size: <?php echo $migwoo_enhancer['shop-order-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['shop-order-transform']?> !important; padding: <?php echo $migwoo_enhancer['shop-order-dimensions']['height']?> <?php echo $migwoo_enhancer['shop-order-dimensions']['width']?> !important;}




/*================ Shop Products ================*/
<?php if($migwoo_enhancer['shop-products-image'] == 'square'):?>.products li img {border-radius: 0px !important;}<?php endif;?>
<?php if($migwoo_enhancer['shop-products-image'] == 'rounded'):?>.products li img {border-radius: 20px !important;}<?php endif;?>
<?php if($migwoo_enhancer['shop-products-image'] == 'circle'):?>.products li img {border-radius: 300px !important;}<?php endif;?>
.products li h3 {text-align: <?php echo $migwoo_enhancer['shop-products-title-alignment']?> !important; color: <?php echo $migwoo_enhancer['shop-products-title-color']?> !important; font-size: <?php echo $migwoo_enhancer['shop-products-title-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['shop-products-title-transform']?> !important;}
.products li .star-rating {color:<?php echo $migwoo_enhancer['shop-products-stars-color']?> !important;}
.products li .woocommerce-Price-amount {color:<?php echo $migwoo_enhancer['shop-products-price-color']?> !important; font-size:<?php echo $migwoo_enhancer['shop-products-price-size']?> !important;}
.products li .add_to_cart_button {color:<?php echo $migwoo_enhancer['shop-products-addtocart-color']['regular']?> !important; background-color:<?php echo $migwoo_enhancer['shop-products-addtocart-background-color']['regular']?> !important;}
.products li .add_to_cart_button:hover {color:<?php echo $migwoo_enhancer['shop-products-addtocart-color']['hover']?> !important; background-color:<?php echo $migwoo_enhancer['shop-products-addtocart-background-color']['hover']?> !important;}
/*============= Pagination ===========================*/
.woocommerce nav.woocommerce-pagination ul li a, .woocommerce nav.woocommerce-pagination ul li span {
padding: <?php echo $migwoo_enhancer['shop-pagination-dimensions']['height']?> <?php echo $migwoo_enhancer['shop-pagination-dimensions']['width']?> !important; color: <?php echo $migwoo_enhancer['shop-pagination-text-color']['regular']?> !important; background-color: <?php echo $migwoo_enhancer['shop-pagination-background-color']['regular']?> !important; font-size: <?php echo $migwoo_enhancer['shop-pagination-size']?>px !important; 
}
.woocommerce nav.woocommerce-pagination ul li a:focus, .woocommerce nav.woocommerce-pagination ul li a:hover, .woocommerce nav.woocommerce-pagination ul li span.current {
color: <?php echo $migwoo_enhancer['shop-pagination-text-color']['hover']?> !important; background-color: <?php echo $migwoo_enhancer['shop-pagination-background-color']['hover']?> !important; 
}


<?php endif;?> /*is_shop*/



<?php if($migwoo_enhancer['activate-product-design'] == 1 && is_product()):?>

/*================== Product Title ===============================*/

h1.product_title.entry-title {color: <?php echo $migwoo_enhancer['product-title-color']?> !important; font-size: <?php echo $migwoo_enhancer['product-title-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['product-title-transform']?> !important;}

/*================== Product Price ===============================*/

.woocommerce div.product p.price, .woocommerce div.product span.price {
color: <?php echo $migwoo_enhancer['product-price-color']?> !important; font-size: <?php echo $migwoo_enhancer['product-price-size']?>px !important; 
}

/*================== Product Quantity ===============================*/

form.cart input.input-text.qty.text {
color: <?php echo $migwoo_enhancer['product-quantity-color']?> !important; background-color: <?php echo $migwoo_enhancer['product-quantity-backcolor']?> !important; 
}

/*================== Product Stock ===============================*/

.woocommerce div.product p.stock {
color: <?php echo $migwoo_enhancer['product-stock-color']?> !important; font-size: <?php echo $migwoo_enhancer['product-stock-size']?>px !important; 
}

/*================== Product Review =================================*/

.woocommerce-product-rating {font-size: <?php echo $migwoo_enhancer['product-review-size']?>px !important; }
.star-rating{color: <?php echo $migwoo_enhancer['product-review-stars-color']?> !important; }
.woocommerce-review-link {color: <?php echo $migwoo_enhancer['product-review-link-color']?> !important; }



/*============= Product Excerpt*/
.description {color: <?php echo $migwoo_enhancer['product-excerpt-color']?> !important; font-size: <?php echo $migwoo_enhancer['product-excerpt-size']?>px !important; }


/*=========== add to cart button*/

form.cart input.input-text.qty.text {height:<?php echo $migwoo_enhancer['add-to-cart-quantity-size']['height']?> ; width:<?php echo $migwoo_enhancer['add-to-cart-quantity-size']['width']?> ;}
.single_add_to_cart_button.button {color: <?php echo $migwoo_enhancer['add-to-cart-link-text-color']['regular']?> !important;}
.single_add_to_cart_button.button:hover {color: <?php echo $migwoo_enhancer['add-to-cart-link-text-color']['hover']?> !important;}
.single_add_to_cart_button.button {background-color: <?php echo $migwoo_enhancer['add-to-cart-link-background-color']['regular']?> !important;}
.single_add_to_cart_button.button:hover {background-color: <?php echo $migwoo_enhancer['add-to-cart-link-background-color']['hover']?> !important;}

/*================= tabs button*/

.woocommerce div.product .woocommerce-tabs ul.tabs li a {color:<?php echo $migwoo_enhancer['tab-link-color']['regular']?> ;}
.woocommerce div.product .woocommerce-tabs ul.tabs li {background-color:<?php echo $migwoo_enhancer['tab-link-background-color']['regular']?> ;}

.tabs.wc-tabs li:hover a {color:<?php echo $migwoo_enhancer['tab-link-color']['hover']?> !important; background-color:<?php echo $migwoo_enhancer['tab-link-background-color']['hover']?> !important;}
.tabs.wc-tabs li:hover {background-color:<?php echo $migwoo_enhancer['tab-link-background-color']['hover']?> !important;}

.tabs.wc-tabs li.active a, .tabs.wc-tabs li.active a:hover {color:<?php echo $migwoo_enhancer['tab-link-color']['active']?> !important;}
.tabs.wc-tabs li.active, .tabs.wc-tabs li.active:hover, .tabs.wc-tabs li.active:hover a  {background-color:<?php echo $migwoo_enhancer['tab-link-background-color']['active']?> !important;}



/*=================== Inside Tabs*/
.woocommerce-Tabs-panel.woocommerce-Tabs-panel--description.panel.entry-content h2 {font-size: <?php echo $migwoo_enhancer['product-description-title-size']?>px !important; color:<?php echo $migwoo_enhancer['product-description-title-color']?> !important;}
.woocommerce-Tabs-panel.woocommerce-Tabs-panel--description.panel.entry-content p {font-size: <?php echo $migwoo_enhancer['product-description-content-size']?>px !important; color:<?php echo $migwoo_enhancer['product-description-content-color']?> !important;}

<?php endif;?> /*is_product()*/

/*================= Account Menu =====================*/
<?php if($migwoo_enhancer['activate-account-design'] == 1):?>
	.woocommerce-MyAccount-navigation-link a {color:<?php echo $migwoo_enhancer['account-link-color']['regular']?> !important ; background-color:<?php echo $migwoo_enhancer['account-link-background-color']['regular']?> !important ;}
	.woocommerce-MyAccount-navigation-link:hover a {color:<?php echo $migwoo_enhancer['account-link-color']['hover']?> !important ; background-color:<?php echo $migwoo_enhancer['account-link-background-color']['hover']?> !important ;}
	.woocommerce-MyAccount-navigation-link.is_active a {color:<?php echo $migwoo_enhancer['account-link-color']['active']?> !important ; background-color:<?php echo $migwoo_enhancer['account-link-background-color']['active']?> !important ;}
	.woocommerce-MyAccount-navigation-link a {padding:<?php echo $migwoo_enhancer['account-links-dimensions']['height']?> <?php echo $migwoo_enhancer['account-links-dimensions']['width']?> !important; }
	
	<?php if($migwoo_enhancer['account-links-width'] == 'yes'):?>
	.woocommerce-MyAccount-navigation-link a {display: block; width:<?php echo $migwoo_enhancer['account-links-width-size']['width']?> !important; }
	<?php endif;?>
<?php endif;?>


/*==================== Checkout Styles ===========================*/
/*titles*/
<?php if($migwoo_enhancer['activate-checkout-titles-design'] == 1 && is_checkout()):?>
h3 {color: <?php echo $migwoo_enhancer['checkout-titles-color']?> !important; font-size: <?php echo $migwoo_enhancer['checkout-titles-size']?>px !important; text-transform: <?php echo $migwoo_enhancer['checkout-titles-transform']?> !important;}
<?php endif;?>


/*Fields*/
<?php if($migwoo_enhancer['activate-checkout-fields-design'] == 1 && is_checkout()):?>
.col2-set input, .select2-container a, .select2-drop ul, .select2-drop {color: <?php echo $migwoo_enhancer['checkout-fields-text-color']?> !important; background-color: <?php echo $migwoo_enhancer['checkout-fields-background-color']?> !important; padding: <?php echo $migwoo_enhancer['checkout-fields-dimensions']['height']?> <?php echo $migwoo_enhancer['checkout-fields-dimensions']['width']?> !important; }
label {color: <?php echo $migwoo_enhancer['checkout-fields-label-color']?> !important;}
	
	<?php if($migwoo_enhancer['checkout-fields-allmax'] == 'yes'):?>
	woocommerce form .form-row-first, .woocommerce form .form-row-last, .woocommerce-page form .form-row-first, .woocommerce-page form .form-row-last {
		float: none;
		width: 100%;
		overflow: visible;
	}
	<?php endif;?>
<?php endif;?>


</style>


<?php } ?>