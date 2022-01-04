<?php

/**
 * Plugin Name: MotoPress and WooCommerce Integration
 * Plugin URI: https://motopress.com/
 * Description: Extend MotoPress Content Editor plugin with WooCommerce shortcodes and styles.
 * Version: 1.1
 * Author: MotoPress
 * Author URI: https://motopress.com/
 * License: GPL2 or later
 * Text Domain: motopress-woocommerce
 * Domain Path: /languages
*/

if (!defined('ABSPATH')) die();

class MPCE_WooCommerce {

	private static $instance;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof MPCE_WooCommerce ) ) {
			self::$instance = new MPCE_WooCommerce;
		}
		return self::$instance;
	}

	function __construct() {

		define(	'MPCE_WOOCOMMERCE_CONTENT_URL', content_url() );

		define(	'MPCE_WOOCOMMERCE_PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

		// add 'product' class to product item
		add_filter( 'post_class', array( $this, 'motopress_woocommerce_product_post_class' ), 10, 3 );

		// integrate WooCommerce shortcodes to MotoPress Content Editor
		add_action('mp_library', array( $this, 'motopress_woocommerce_mp_library_action' ), 10, 1);
	}

	public function motopress_woocommerce_product_post_class( $classes, $class = '', $post_id = '' ) {
		if ( ! $post_id || 'product' !== get_post_type( $post_id ) ) {
			return $classes;
		}
		if ( false === ( $key = array_search( 'product', $classes ) ) ) {
			$classes[] = 'product';
		}

		return $classes;
	}
	
	private function get_icon( $icon ) {
		return ltrim( (str_replace( MPCE_WOOCOMMERCE_CONTENT_URL, '' , MPCE_WOOCOMMERCE_PLUGIN_DIR_URL . '/icons/' ) . $icon), '/');
	}

	public function motopress_woocommerce_mp_library_action($motopressCELibrary) {

		// common atts
		$orderby = array(
			'type' => 'select',
			'label' => __('Order by', 'motopress-woocommerce'),
			'default' => 'title',
			'list' => array(
				'date' => __('Date', 'motopress-woocommerce'),
				'id' => __('ID of the product', 'motopress-woocommerce'),
				'menu_order' => __('Menu order', 'motopress-woocommerce'),
				'popularity'	=> __('Number of purchases', 'motopress-woocommerce'),
				'rand'	=> __('Randomly', 'motopress-woocommerce'),
				'rating'	=> __('Average product rating', 'motopress-woocommerce'),
				'title'	=> __('Title', 'motopress-woocommerce'),
			),
		);

		$order = array(
			'type' => 'select',
			'label' => __('Order', 'motopress-woocommerce'),
			'default' => 'ASC',
			'list' => array(
				'ASC' => __('Ascending (1, 2, 3)', 'motopress-woocommerce'),
				'DESC' => __('Descending (3, 2, 1)', 'motopress-woocommerce'),
			),
		);

		$per_page = array(
			'type' => 'text',
			'label' => __('Products', 'motopress-woocommerce'),
			'description' => __('The number of products to display.', 'motopress-woocommerce'),
		);

		$columns = array(
			'type' => 'spinner',
			'label' => __('Columns', 'motopress-woocommerce'),
			'description' => __('The number of columns to display.', 'motopress-woocommerce'),
			'default' => 4,
			'min' => 1,
			'max' => 100,
			'step' => 1
		);

		$mpCart = new MPCEObject('woocommerce_cart', __('Cart', 'motopress-woocommerce'), $this->get_icon('cart.png'), array(), 0);

		$mpCheckout = new MPCEObject('woocommerce_checkout', __('Checkout', 'motopress-woocommerce'), $this->get_icon('checkout.png'), array(), 0);

		$mpOrderTrackingForm = new MPCEObject('woocommerce_order_tracking', __('Order Tracking Form', 'motopress-woocommerce'), $this->get_icon('order_tracking_form.png'), array(), 0);

		$mpWoocommerceMyAccount = new MPCEObject('woocommerce_my_account', __('My Account', 'motopress-woocommerce'), $this->get_icon('account.png'),
			array(
				'order_count' => array(
					'type' => 'text',
					'label' => __('Number of orders to show', 'motopress-woocommerce'),
					'description' => __('Use -1 to display all orders.', 'motopress-woocommerce'),
					'default' => '15',
				),
			),
			0
		);

		$mpRecentProducts = new MPCEObject('recent_products', __('Recent Products', 'motopress-woocommerce'), $this->get_icon('recent_products.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
			),
			0
		);

		$mpFeaturedProducts = new MPCEObject('featured_products', __('Featured Products', 'motopress-woocommerce'), $this->get_icon('featured_products.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
			),
			0
		);
		
		$mpProduct = new MPCEObject('product', __('Product', 'motopress-woocommerce'), $this->get_icon('product.png'),
			array(
				'id' => array(
					'type' => 'text',
					'label' => __('Show a single product by ID', 'motopress-woocommerce'),
				),
				'sku' => array(
					'type' => 'text',
					'label' => __('Show a single product by SKU', 'motopress-woocommerce'),
				),
				'columns' => array(
					'type' => 'spinner',
					'label' => __('Columns', 'motopress-woocommerce'),
					'description' => __('The number of columns to display.', 'motopress-woocommerce'),
					'default' => 1,
					'min' => 1,
					'max' => 100,
					'step' => 1
				),
			),
			0
		);
		
		$mpProducts = new MPCEObject('products', __('Products', 'motopress-woocommerce'), $this->get_icon('products.png'),
			array(
				'ids' => array(
					'type' => 'text',
					'label' => __('Show multiple products by ID', 'motopress-woocommerce'),
					'description' => __('example: 12, 25, 30, 49', 'motopress-woocommerce'),
				),
				'skus' => array(
					'type' => 'text',
					'label' => __('Show multiple products by SKU', 'motopress-woocommerce'),
					'description' => __('example: foo, bar, baz', 'motopress-woocommerce'),
				),
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
			),
			0
		);
		
		$mpAddToCart = new MPCEObject('add_to_cart', __('Add to Cart', 'motopress-woocommerce'), $this->get_icon('add_2_cart.png'),
			array(
				'id' => array(
					'type' => 'text',
					'label' => __('Product ID', 'motopress-woocommerce'),
					'description' => __('Show the price and add to cart button of a single product by ID', 'motopress-woocommerce'),
				),
				'sku' => array(
					'type' => 'text',
					'label' => __('Product SKU', 'motopress-woocommerce'),
					'description' => __('Show the price and add to cart button of a single product by SKU', 'motopress-woocommerce'),
				),
				'style' => array(
					'type' => 'text',
					'label' => __('Style', 'motopress-woocommerce'),
				),
				'show_price' => array(
					'type' => 'checkbox',
					'label' => __('Show Price', 'motopress-woocommerce'),
					'default' => 'true',
				),
			),
			0
		);
		
		$mpProductPage = new MPCEObject('product_page', __('Single Product Page', 'motopress-woocommerce'), $this->get_icon('single_product_page.png'),
			array(
				'id' => array(
					'type' => 'text',
					'label' => __('Show a full single product page by ID', 'motopress-woocommerce'),
				),
				'sku' => array(
					'type' => 'text',
					'label' => __('Show a full single product page by SKU', 'motopress-woocommerce'),
				),
			),
			0
		);

		$mpProductCategory = new MPCEObject('product_category', __('Category', 'motopress-woocommerce'), $this->get_icon('category.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
				'category' => array(
					'type' => 'text',
					'label' => __('Show multiple products in a category by slug', 'motopress-woocommerce'),
					'description' => __('Go to: WooCommerce > Products > Categories to find the slug column', 'motopress-woocommerce'),
					'default' => '',
				),
			),
			0
		);
		
		$mpProductCategories = new MPCEObject('product_categories', __('Categories', 'motopress-woocommerce'), $this->get_icon('category.png'),
			array(
				'number' => array(
					'type' => 'text',
					'label' => __('Number', 'motopress-woocommerce'),
					'description' => __('The number of categories to display.', 'motopress-woocommerce'),
					'default' => '-1',
				),
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
				'hide_empty' => array(
					'type' => 'checkbox',
					'label' => __('Hide empty', 'motopress-woocommerce'),
					'default' => 'true',
				),
				'parent' => array(
					'type' => 'text',
					'label' => __('Parent', 'motopress-woocommerce'),
					'description' => __('Set the parent parameter to 0 to only display top level categories', 'motopress-woocommerce'),
					'default' => '',
				),
				'ids' => array(
					'type' => 'text',
					'label' => __('IDs', 'motopress-woocommerce'),
					'description' => __('Set ids to a comma separated list of category ids to only show those', 'motopress-woocommerce'),
					'default' => '',
				),
			),
			0
		);
		
		$mpSaleProducts = new MPCEObject('sale_products', __('Sale Products', 'motopress-woocommerce'), $this->get_icon('sale_products.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
				'category' => array(
					'type' => 'text',
					'label' => __('Categories', 'motopress-woocommerce'),
					'description' => __('Comma separated list of category slug', 'motopress-woocommerce'),
					'default' => '',
				),
			),
			0
		);
		
		$mpBestSellingProducts = new MPCEObject('best_selling_products', __('Best Selling Products', 'motopress-woocommerce'), $this->get_icon('best_selling_products.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'category' => array(
					'type' => 'text',
					'label' => __('Categories', 'motopress-woocommerce'),
					'description' => __('Comma separated list of category slug', 'motopress-woocommerce'),
					'default' => '',
				),
			),
			0
		);
		
		$mpTopRatedProducts = new MPCEObject('top_rated_products', 'popo', $this->get_icon('top_rated.png'),
		//$mpTopRatedProducts = new MPCEObject('top_rated_products', __('Top Rated Products', 'motopress-woocommerce'), $this->get_icon('top_rated.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
				'category' => array(
					'type' => 'text',
					//'label' => __('Categories', 'motopress-woocommerce'),
					'label' => 'popo',
					'description' => __('Comma separated list of category slug', 'motopress-woocommerce'),
					'default' => '',
				),
			),
			0
		);
		
		$mpProductAttribute = new MPCEObject('product_attribute', __('Products by Attribute', 'motopress-woocommerce'), $this->get_icon('products_by_attribute.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
				'order' => $order,
				'attribute' => array(
					'type' => 'text',
					'label' => __('Attribute', 'motopress-woocommerce'),
					'default' => 'color',
				),
				'filter' => array(
					'type' => 'text',
					'label' => __('Filter (terms)', 'motopress-woocommerce'),
					'default' => 'black',
				),
			),
			0
		);

		$mpRelatedProducts = new MPCEObject('related_products', __('Related Products', 'motopress-woocommerce'), $this->get_icon('related_products.png'),
			array(
				'per_page' => $per_page,
				'columns' => $columns,
				'orderby' => $orderby,
			),
			0
		);

		$woocommerceGroup = new MPCEGroup();
		$woocommerceGroup->setId(MPCEShortcode::PREFIX . 'woocommerce');
		$woocommerceGroup->setName( __('WooCommerce', 'motopress-woocommerce') );
		$woocommerceGroup->setIcon(
			ltrim( (str_replace( MPCE_WOOCOMMERCE_CONTENT_URL, '' , MPCE_WOOCOMMERCE_PLUGIN_DIR_URL . '/icons/' ) . 'woocommerce.png'), '/')
		);
		$woocommerceGroup->setPosition(60);
		$woocommerceGroup->addObject(
			array(
				$mpRecentProducts,
				$mpFeaturedProducts,
				$mpProduct,
				$mpProducts,
				$mpAddToCart,
				$mpProductPage,
				$mpProductCategory,
				$mpProductCategories,
				$mpSaleProducts,
				$mpBestSellingProducts,
				$mpTopRatedProducts,
				$mpProductAttribute,
				$mpRelatedProducts,
				$mpCart, $mpCheckout,
				$mpOrderTrackingForm,
				$mpWoocommerceMyAccount
			)
		);

		$motopressCELibrary->addGroup($woocommerceGroup);
	}
}

function MPCEWooCommerce() {
	return MPCE_WooCommerce::instance();
}

MPCEWooCommerce();
