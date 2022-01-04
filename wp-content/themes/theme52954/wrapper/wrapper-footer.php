<?php /* Wrapper Name: Footer */ ?>
<div class="row footer-widgets">
	<div class="span3" data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar-1">
		<strong style="font-size: 15px;color: #c71e1f;">TIENDA</strong></br></br><?php get_template_part("static/static-footer-nav"); ?>
	</div>
	<div class="span3" data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar-2">
		<!--<?php dynamic_sidebar("footer-sidebar-1"); ?>-->
		<strong style="font-size: 15px;color: #c71e1f;">INFORMACIÓN</strong></br></br>
		<nav class="nav footer-nav">
			<ul id="menu-menu-shop_nav_footer" class="menu"><li id="menu-item-2661" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2661"><a href="<?php echo get_site_url(); ?>/">Home</a></li>
				<li id="menu-item-2662" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2662 texto"><a href="<?php echo get_site_url(); ?>/nosotros/">Nosotros</a></li>
				
				<li id="menu-item-2664" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2664 texto"><a href="<?php echo get_site_url(); ?>/soporte/">Soporte</a></li>
				<li id="menu-item-2664" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2664 "><a class ="texto" href="<?php echo get_site_url(); ?>/sucursales/">Sucursales</a></li>
			</ul>		
		</nav>
	</div>
	<div class="span3 box" data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar-3">
		<strong style="font-size: 15px;color: #c71e1f;">SERVICIO AL CLIENTE</strong></br></br>
		<nav class="nav footer-nav">
			<ul id="menu-menu-shop_nav_footer" class="menu">
			
				<li id="menu-item-2662" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2662 texto">
				<a class = "texto" href="tel:+5112036530"><span class="social_ico" style="padding-right:5px"><img src="<?php echo get_home_url(); ?>/wp-content/themes/theme52954/images/icons/phone.png" alt=""></span>
				+51-1-203-6530</a></li>
				
				<li id="menu-item-2664" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2664 texto">
				<a class = "texto" href="mailto:ventas@sekurperu.com.pe" style="text-transform: lowercase;"><span class="social_ico" style="padding-right:5px"><img src="<?php echo get_home_url(); ?>/wp-content/themes/theme52954/images/icons/email-2.png" alt=""></span>ventas@sekurperu.com.pe</a></li>
				<li id="menu-item-2664" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-2664 texto">
				<a class = "texto" href="https://goo.gl/maps/cyW36G8FheuDkcVv6"><span class="social_ico" style="padding-right:5px"><img src="<?php echo get_home_url(); ?>/wp-content/themes/theme52954/images/icons/street.png" alt=""></span>
				Ricardo Angulo 782, Urb. Corpac - San Isidro</a></li>
			</ul>		
		</nav>
	</div>
	<div class="span3" data-motopress-type="dynamic-sidebar" data-motopress-sidebar-id="footer-sidebar-4">
		<?php dynamic_sidebar("footer-sidebar-4"); ?>
	</div>
</div>
<div class="row copyright">
	<?php get_template_part("static/static-footer-text"); ?>
</div>