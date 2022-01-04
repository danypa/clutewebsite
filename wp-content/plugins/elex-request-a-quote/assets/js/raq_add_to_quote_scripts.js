// Function that runs when product is added to quote in product page (for simple and external products)
function add_to_quote_product_shop_page_simple_external_products(id, event) {
  document.getElementById(id).disabled = true;
  let quantity; // Product Quantity.

  if (event !== undefined) {
    // i.e if user adds the product from product page, not Related Products at bottom of the page.
    event.preventDefault();
    quantity = jQuery('input[name="quantity"]').val();
  }
  if(undefined === quantity){
    quantity = 1;
  }

  var item = {
    product_id: id,
    Qty: event !== undefined
      ? quantity
      : 1,
    variation_id: null
  };

  jQuery.ajax({
    type: "post",
    url: elex_request_a_quote_scripts_object.ajax_url,
    data: {
      action: "eraq_cart_contents",
      _ajax_nonce: elex_request_a_quote_scripts_object.nonce,
      cart_item: item
    }
  }).done(function( msg ) {
    document.getElementById(id).disabled = false;
    document.getElementById(id).nextElementSibling.style = "display:block";
  });
}

jQuery(window).on('load', function(){
  jQuery("table.variations select").each(function () {
    if (jQuery(this).val() == "" || jQuery(this).val() == undefined) {
      localStorage.removeItem("currently_selected_variation_id");
    }
  });
});

// Function that runs when product is added to quote in product page (for variable products)
function add_to_cart_product_page_variable_products(id, event) {
  // document.getElementById(id).disabled = true;
  var selected_variation_id;
  if (localStorage.getItem("currently_selected_variation_id") === null) {
    event.preventDefault();
    return;
  } else {
    selected_variation_id = localStorage.getItem("currently_selected_variation_id");
  }
  let quantity;
  if (event !== undefined) {
    // i.e if user adds the product from product page, not Related Products at bottom of the page.
    event.preventDefault();
    quantity = jQuery('input[name="quantity"]').val();
  }
  if(undefined === quantity){
    quantity = 1;
  }
  // create JavaScript Object
  var item = {
    product_id: id,
    Qty: event !== undefined ? quantity: 1,
    variation_id: selected_variation_id !== undefined
      ? selected_variation_id
      : null
  };

  jQuery.ajax({
    type: "post",
    url: elex_request_a_quote_scripts_object.ajax_url,
    data: {
      action: "eraq_cart_contents",
      _ajax_nonce: elex_request_a_quote_scripts_object.nonce,
      cart_item: item
    }
  }).done(function( msg ) {

   // document.getElementById(id).disabled = false;
    document.getElementById(id).nextElementSibling.style = "display:block";
  });
}
