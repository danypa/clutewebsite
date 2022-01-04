<?php
class FullCulqi_CcWoocommerce extends WC_Checkout {
	
	public function WooUpdateSession( $data ) {
		$this->update_session( $data );
	}

	public function WooValidateCheckout( &$data, &$errors ) {
		$this->validate_checkout( $data, $errors );
	}
}