jQuery("#elex_raq_cart_table_body").on("click", ".eraq_remove_icon", function () {
  if (jQuery(this).closest("tr").attr("data-vid")) {
    // Its a variation.
    let product_id = jQuery(this).closest("tr").attr("data-pid");
    let variation_id = jQuery(this).closest("tr").attr("data-vid");
    let data = {
      product_id: product_id,
      variation_id: variation_id,
      operation: "delete"
    };
    jQuery(this).closest("tr").remove();
    jQuery.ajax({
      type: "post",
      url: eraq_shortcode_handler_scripts_object.ajax_url,
      data: {
        action: "eraq_update_cart_contents",
        _ajax_nonce: eraq_shortcode_handler_scripts_object.nonce,
        cart_item: data
      }
    }).done(function( msg ) {
      location.reload();
    });
  } else {
    // Its a simple/external product.
    let product_id = jQuery(this).closest("tr").attr("data-pid");
    let data = {
      product_id: product_id,
      variation_id: null,
      operation: "delete"
    };
    jQuery(this).closest("tr").remove();
    jQuery.ajax({
      type: "post",
      url: eraq_shortcode_handler_scripts_object.ajax_url,
      data: {
        action: "eraq_update_cart_contents",
        _ajax_nonce: eraq_shortcode_handler_scripts_object.nonce,
        cart_item: data
      }
    }).done(function( msg ) {
      location.reload();
    });
  }
});

jQuery(document).ready(function () {
  var total = 0;
  jQuery("#eraq_quote_list_table tbody tr:not(:last)").each(function () {
    // Get price values.
    var value = parseFloat(jQuery(this).find(" td.eraq_product_price span").text());

    // Get quantity values.
    var quantity = jQuery(this).find(" td.eraq_product_quantity input").val();

    var subtotal_full = value * quantity;

    var subtotal = parseFloat(subtotal_full.toFixed(eraq_shortcode_handler_scripts_object.round_price_value));
    jQuery(this).find(" td.eraq_product_subtotal span").text(subtotal);
    total += parseFloat(subtotal.toFixed(eraq_shortcode_handler_scripts_object.round_price_value));
  });
  jQuery("#eraq_quote_list_table tbody tr:last td.eraq_product_total span").text(total);
});

jQuery(".eraq_product_quantity_input_value").on("change", function () {
  // Get current value of quantity
  let currentQty = jQuery(this).val();

  if (jQuery(this).closest("tr").attr("data-vid")) {
    // Its a variation.
    let product_id = jQuery(this).closest("tr").attr("data-pid");
    let variation_id = jQuery(this).closest("tr").attr("data-vid");
    let data = {
      product_id: product_id,
      variation_id: variation_id,
      current_quantity: currentQty,
      operation: "update"
    };
    jQuery.ajax({
      type: "post",
      url: eraq_shortcode_handler_scripts_object.ajax_url,
      data: {
        action: "eraq_update_cart_contents",
        _ajax_nonce: eraq_shortcode_handler_scripts_object.nonce,
        cart_item: data
      }
    }).done(function( msg ) {
      location.reload();
    });
  } else {
    // Its a simple/external product.
    let product_id = jQuery(this).closest("tr").attr("data-pid");
    let data = {
      product_id: product_id,
      variation_id: null,
      current_quantity: currentQty,
      operation: "update"
    };
    jQuery.ajax({
      type: "post",
      url: eraq_shortcode_handler_scripts_object.ajax_url,
      data: {
        action: "eraq_update_cart_contents",
        _ajax_nonce: eraq_shortcode_handler_scripts_object.nonce,
        cart_item: data
      }
    }).done(function( msg ) {
      location.reload();
    });
  }
});

jQuery("#quote_details_form").submit(function (e) {
  e.preventDefault();
  jQuery("input.button.eraq-send-request").attr("disabled", true);
  jQuery.ajax({
    type: "post",
    url: eraq_shortcode_handler_scripts_object.ajax_url,
    data: {
      action: "eraq_place_order",
      _ajax_nonce: eraq_shortcode_handler_scripts_object.nonce,
      cart_item: jQuery(this).serialize()
    }
  }).done(function( msg ) {
    window.location.href = '/quote-received-page';
  });
});
