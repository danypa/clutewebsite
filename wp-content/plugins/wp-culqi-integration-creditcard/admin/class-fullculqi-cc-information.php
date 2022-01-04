<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FullCulqi_CcInfo {

	public function __construct() {
		add_filter( 'plugin_row_meta', [ $this, 'setting_external_link' ], 10, 2 );
		add_filter( 'plugin_action_links_'.FULLCULQI_CC_BASE, [ $this, 'setting_internal_link' ] );
	}

	/**
	 *	Internal Links
	 */
	public function setting_internal_link( $links ) {
		$settings = array(
						'license' => sprintf('<a href="%s" id="fullculqi_cc_license">%s</a>', '#open-license', __('Manager Licenses','letsgo'))
					);
	
		return array_merge( $settings, $links );
	}


	/**
	 * External Links
	 */
	public function setting_external_link($links, $file) {
		if ( $file == FULLCULQI_CC_BASE ) {
			
			$row_meta = array(
						'docs' => sprintf('<a href="%s" target="_blank" title="%s">%s</a>',esc_url('https://www.letsgodev.com/documentation/docs-wordpress-culqi-tarjetacredito/'), 'Wordpress Culqi Integration Card Credit' , __('Documentation', 'letsgo')),
						'support' => sprintf('<a href="%s" target="_blank" title="%s">%s</a>',esc_url('https://www.letsgodev.com/contact/'), 'Wordpress Culqi Integration' , __('Support', 'letsgo')),
						'buy' => sprintf('<a href="%s" target="_blank" title="%s">%s</a>',esc_url('https://www.letsgodev.com/'), 'Wordpress Culqi Integration Card Credit' , __('More Premiun Plugins', 'letsgo'))
				);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}