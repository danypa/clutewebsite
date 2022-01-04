<div id="receipt_page_fullculqi">
	<?php $src_image = apply_filters('fullculqi/receipt_page/image', $src_image); ?>
	
	<img src="<?php echo $src_image; ?>" alt="fullculqi" /><br />
	<span>Realiza la compra presionando <strong>Pagar</strong><br>Si deseas cambiar de medio de pago presiona <strong>Cancelar</strong></span><br><br>

	<?php $class_buttonpay = apply_filters('fullculqi/receipt_page/button_class', array('button','alt') ); ?>
	<!-- css-palk -->
	<button style="padding:5px; margin:5px" id="fullculqi_button" class="<?php echo implode(' ', $class_buttonpay); ?>"><?php _e('Pay with Culqi','letsgo'); ?></button>
	<a style="padding:5px; margin:5px" href="<?php echo $url_cancel; ?>" class="button cancel"><?php _e('Cancel','letsgo'); ?></a>

	<div id="fullculqi_notify" style="padding:10px 0px;"><?php echo apply_filters('fullculqi/receipt_page/notify', '', $order_id); ?></div>
</div>
