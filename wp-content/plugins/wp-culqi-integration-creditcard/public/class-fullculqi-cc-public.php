<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FullCulqi_CcPublic {

	public function __construct() {
		add_filter('woocommerce_update_order_review_fragments', [$this, 'add_fragments'], 10, 1);
		add_filter('woocommerce_checkout_after_order_review' , [ $this, 'add_field' ], 10);
	}

	public function add_fragments($fragments) {

		$fragments['#checkout_fullculqi_total'] = '<input type="hidden" id="checkout_fullculqi_total" name="checkout_fullculqi_total" value="'.WC()->cart->get_total('edit').'" />';
		
		return apply_filters('fullculqi_cc/public/fragments', $fragments);
	}

	public function add_field() {

		echo '<input type="hidden" id="checkout_fullculqi_total" name="checkout_fullculqi_total" value="'.WC()->cart->get_total('edit').'" />';
	}
}