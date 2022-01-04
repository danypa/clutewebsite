<!--
<h3 class="woocommerce-order-details__title" style="font-style: normal; font-variant-ligatures: normal; font-variant-caps: normal; font-variant-numeric: normal; font-variant-east-asian: normal; font-weight: bold; font-stretch: normal; line-height: 34px; font-family: Lato; margin-top: 20px;"><?php echo ($title = get_option('wooccm_order_custom_fields_title', false)) ? esc_html($title) : esc_html__('Custom fields', 'woocommerce-checkout-manager'); ?></h3> 
-->
<h3 class="woocommerce-column__title"><?php echo ($title = get_option('wooccm_order_custom_fields_title', false)) ? esc_html($title) : esc_html__('Datos Adicionales', 'woocommerce-checkout-manager'); ?></h3> 

<!-- vanessa -->
<table width="100%" border="1" bordercolor="#CCC" style="background:#fcfcfc;margin-bottom:10px"><tr><td style="border-color: #19428A;">
<!-- vanessa -->
    <?php
    if (count($checkout = WC()->checkout->get_checkout_fields())):
    
      foreach ($checkout as $field_type => $fields) :
        foreach ($fields as $key => $field) :
        if (strlen(trim($field['name']))>0) : //filtrar los no deseados...
          if (isset(WOOCCM()->$field_type)) :
            ?>
            <?php if (!in_array($field['name'], WOOCCM()->$field_type->get_defaults()) && empty($field['hide_order'])) : ?>
              <?php if ($value = get_post_meta($order_id, sprintf('_%s', $key), true)): ?>
                <?php echo '<div style="padding-left:20px"><strong>'.esc_html($field['label']).':</strong> ' . esc_html($value) . '</div>'; 
                if($value=="Boleta" || $value=="BOLETA")//BY PALK <!--vanessa-->
                    break;
                ?>
                
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>
          <?php endif; ?>  
        <?php endforeach; ?>
      <?php endforeach; ?>


    <?php endif; 

	$calle=get_post_meta( $order_id, '_shipping_calle', true );
	$city='';
	if($calle!='')
	{
	$city=get_post_meta( $order_id, '_shipping_city', true );	

	}
	else
	{
	$city=get_post_meta( $order_id, '_billing_city', true );
	}

	$agencia = get_shippingagency(getProvinciaByidProv(explode("-",$city)[0]), get_post_meta( $order_id, 'shipping_shipperagency', true ));
	echo '<div style="padding-left:20px"><strong>Agencia de Transporte:</strong> ' . esc_html($agencia) . '</div>'; 

?>

	

</td></tr></table>
<script>
function imprimir_comprobante(){
  document.getElementById("boton_imprimir2").style.opacity = "0%";
  document.getElementById("boton_imprimir3").style.opacity = "0%";
  window.print();
  document.getElementById("boton_imprimir2").style.opacity = "100%";
  document.getElementById("boton_imprimir3").style.opacity = "100%";
}
</script>

<script type="text/javascript">
  if (window.location.href.indexOf("view-order") > -1) 
    {}
    else {
      document.write('<a class="button button-primary boton_amarillo" id="boton_imprimir2" style="margin-right: 10px;" target="_blank" href="https://clute.com.pe/tienda/">Volver a la Tienda</a>');
      document.write('<a class="button button-primary boton_amarillo" id="boton_imprimir3" style="" target="_blank" onclick="imprimir_comprobante();">Imprimir Constancia</a>'); 
    }

</script>



