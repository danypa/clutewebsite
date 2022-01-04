<?php

function mytheme_add_woocommerce_support() {
add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );


// Disable WooCommerce's Default Stylesheets
function disable_woocommerce_default_css( $styles ) {

  // Disable the stylesheets below via unset():
  unset( $styles['woocommerce-general'] );  // Styling of buttons, dropdowns, etc.
  // unset( $styles['woocommerce-layout'] );        // Layout for columns, positioning.
  // unset( $styles['woocommerce-smallscreen'] );   // Responsive design for mobile devices.

  return $styles;
}
add_action('woocommerce_enqueue_styles', 'disable_woocommerce_default_css');


// Add a custom stylesheet to replace woocommerce.css
function use_woocommerce_custom_css() {
  // Custom CSS file located in [Theme]/woocommerce/woocommerce.css
  wp_enqueue_style(
      'woocommerce-custom', 
      get_stylesheet_directory_uri() . '/woocommerce/woocommerce.css'
  );
}
add_action('wp_enqueue_scripts', 'use_woocommerce_custom_css', 15);


function woo_remove_product_tabs( $tabs ) {
  unset( $tabs['additional_information'] );
  return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );


add_action('wp_enqueue_scripts', 'update_jquery_for_cherry_framework', 11);
function update_jquery_for_cherry_framework() {
   wp_deregister_script('jquery');
   wp_register_script('jquery', '/wp-includes/js/jquery/jquery.js', false, false, true);
   wp_enqueue_script('jquery');
}

add_action( 'after_setup_theme', 'ava_remove_custom_gallery', 100 );
function ava_remove_custom_gallery() { 
    remove_theme_support( 'wc-product-gallery-zoom' );
    remove_theme_support( 'wc-product-gallery-lightbox' );
}

// define the woocommerce_get_stock_html callback 
function filter_woocommerce_get_stock_html( $html, $product ) { 
    $html         = '';
	$availability = $product->get_availability();

	if ( ! empty( $availability['availability'] ) ) {
		ob_start();

		/*wc_get_template(
			'single-product/stock.php',
			array(
				'product'      => $product,
				'class'        => $availability['class'],
				'availability' => $availability['availability'],
			)
		);
*/
		$html = ob_get_clean();

		if($availability['availability']<=300){
			$html ='<p class="stock in-stock">Últimos '.$availability['availability'].'</p>';
		}else{
			$html ='<p class="stock in-stock">Hay stock disponible</p>';
		}
	}

	/*if ( has_filter( 'woocommerce_stock_html' ) ) {
		wc_deprecated_function( 'The woocommerce_stock_html filter', '', 'woocommerce_get_stock_html' );
		$html = apply_filters( 'woocommerce_stock_html', $html, $availability['availability'], $product );
	}

	return apply_filters( 'woocommerce_get_stock_html', $html, $product );*/
    
	return $html;
}; 
         


// add the filter 
add_filter( 'woocommerce_get_stock_html', 'filter_woocommerce_get_stock_html', 10, 2 ); 

/*

add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
function woocommerce_category_image() {
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    echo '<img width="120px"  src="' . $image . '" alt="' . $cat->name . '" /><br><br>';
		}
	}
}*/




require_once("category_class_widget.php");
add_action("widgets_init", "my_custom_widgets_init");

function my_custom_widgets_init(){
  register_widget("WP_Categories_Class_2");
}




class Walker_Category_Custom extends Walker {

	/**
	 * What the class handles.
	 *
	 * @since 2.1.0
	 * @var string
	 *
	 * @see Walker::$tree_type
	 */
	public $tree_type = 'category';

	/**
	 * Database fields to use.
	 *
	 * @since 2.1.0
	 * @var array
	 *
	 * @see Walker::$db_fields
	 * @todo Decouple this
	 */
	public $db_fields = array(
		'parent' => 'parent',
		'id'     => 'term_id',
	);

	
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent<ul class='children'>\n";
	}

	
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] ) {
			return;
		}

		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		/** This filter is documented in wp-includes/category-template.php */
		$cat_name = apply_filters( 'list_cats', esc_attr( $category->name ), $category );

		// Don't generate an element if the category name is empty.
		if ( '' === $cat_name ) {
			return;
		}

		$atts         = array();
		$atts['href'] = get_term_link( $category );

		if ( $args['use_desc_for_title'] && ! empty( $category->description ) ) {
			/**
			 * Filters the category description for display.
			 *
			 * @since 1.2.0
			 *
			 * @param string $description Category description.
			 * @param object $category    Category object.
			 */
			$atts['title'] = strip_tags( apply_filters( 'category_description', $category->description, $category ) );
		}

		
		$atts = apply_filters( 'category_list_link_attributes', $atts, $category, $depth, $args, $id );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}




		$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
		$strImage = "sdsd";
	    if ( $image ) {
		    $strImage = '<img width="30px"  src="' . $image . '" style="padding:0px 5px 0px 5px"  />';
		}


		$link = sprintf(
			'<a%s>'.$strImage.'%s</a>',
			$attributes,
			$cat_name
		);

		

		if ( ! empty( $args['show_count'] ) ) {
			$link .= ' (' . number_format_i18n( $category->count ) . ')';
		}
		if ( 'list' == $args['style'] ) {
			$output     .= "\t<li";
			$css_classes = array(
				'cat-item',
				'cat-item-' . $category->term_id,
			);

			if ( ! empty( $args['current_category'] ) ) {
				// 'current_category' can be an array, so we use `get_terms()`.
				$_current_terms = get_terms(
					array(
						'taxonomy'   => $category->taxonomy,
						'include'    => $args['current_category'],
						'hide_empty' => false,
					)
				);

				foreach ( $_current_terms as $_current_term ) {
					if ( $category->term_id == $_current_term->term_id ) {
						$css_classes[] = 'current-cat';
						$link          = str_replace( '<a', '<a aria-current="page"', $link );
					} elseif ( $category->term_id == $_current_term->parent ) {
						$css_classes[] = 'current-cat-parent';
					}
					while ( $_current_term->parent ) {
						if ( $category->term_id == $_current_term->parent ) {
							$css_classes[] = 'current-cat-ancestor';
							break;
						}
						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
					}
				}
			}

			
			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );
			$css_classes = $css_classes ? ' class="' . esc_attr( $css_classes ) . '"' : '';

			$output .= $css_classes;
			$output .= ">$link\n";
		} elseif ( isset( $args['separator'] ) ) {
			$output .= "\t$link" . $args['separator'] . "\n";
		} else {
			$output .= "\t$link<br />\n";
		}
	}

	
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		if ( 'list' != $args['style'] ) {
			return;
		}

		$output .= "</li>\n";
	}

}



function custom_list_categories( $args = '' ) {
  $defaults = array(
		'child_of'            => 0,
		'current_category'    => 0,
		'depth'               => 0,
		'echo'                => 1,
		'exclude'             => '',
		'exclude_tree'        => '',
		'feed'                => '',
		'feed_image'          => '',
		'feed_type'           => '',
		'hide_empty'          => 1,
		'hide_title_if_empty' => false,
		'hierarchical'        => true,
		'order'               => 'ASC',
		'orderby'             => 'name',
		'separator'           => '<br />',
		'show_count'          => 0,
		'show_option_all'     => '',
		'show_option_none'    => __( 'No categories' ),
		'style'               => 'list',
		'taxonomy'            => 'category',
		'title_li'            => __( 'Categories' ),
		'use_desc_for_title'  => 1,
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	if ( ! isset( $parsed_args['pad_counts'] ) && $parsed_args['show_count'] && $parsed_args['hierarchical'] ) {
		$parsed_args['pad_counts'] = true;
	}

	// Descendants of exclusions should be excluded too.
	if ( true == $parsed_args['hierarchical'] ) {
		$exclude_tree = array();

		if ( $parsed_args['exclude_tree'] ) {
			$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude_tree'] ) );
		}

		if ( $parsed_args['exclude'] ) {
			$exclude_tree = array_merge( $exclude_tree, wp_parse_id_list( $parsed_args['exclude'] ) );
		}

		$parsed_args['exclude_tree'] = $exclude_tree;
		$parsed_args['exclude']      = '';
	}

	if ( ! isset( $parsed_args['class'] ) ) {
		$parsed_args['class'] = ( 'category' == $parsed_args['taxonomy'] ) ? 'categories' : $parsed_args['taxonomy'];
	}

	if ( ! taxonomy_exists( $parsed_args['taxonomy'] ) ) {
		return false;
	}

	$show_option_all  = $parsed_args['show_option_all'];
	$show_option_none = $parsed_args['show_option_none'];

	$categories = get_categories( $parsed_args );

	$output = '';
	if ( $parsed_args['title_li'] && 'list' == $parsed_args['style'] && ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] ) ) {
		$output = '<li class="' . esc_attr( $parsed_args['class'] ) . '">' . $parsed_args['title_li'] . '<ul>';
	}
	if ( empty( $categories ) ) {
		if ( ! empty( $show_option_none ) ) {
			if ( 'list' == $parsed_args['style'] ) {
				$output .= '<li class="cat-item-none">' . $show_option_none . '</li>';
			} else {
				$output .= $show_option_none;
			}
		}
	} else {
		if ( ! empty( $show_option_all ) ) {

			$posts_page = '';

			// For taxonomies that belong only to custom post types, point to a valid archive.
			$taxonomy_object = get_taxonomy( $parsed_args['taxonomy'] );
			if ( ! in_array( 'post', $taxonomy_object->object_type ) && ! in_array( 'page', $taxonomy_object->object_type ) ) {
				foreach ( $taxonomy_object->object_type as $object_type ) {
					$_object_type = get_post_type_object( $object_type );

					// Grab the first one.
					if ( ! empty( $_object_type->has_archive ) ) {
						$posts_page = get_post_type_archive_link( $object_type );
						break;
					}
				}
			}

			// Fallback for the 'All' link is the posts page.
			if ( ! $posts_page ) {
				if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) {
					$posts_page = get_permalink( get_option( 'page_for_posts' ) );
				} else {
					$posts_page = home_url( '/' );
				}
			}

			$posts_page = esc_url( $posts_page );
			if ( 'list' == $parsed_args['style'] ) {
				$output .= "<li class='cat-item-all'> <a href='$posts_page'>$show_option_all</a></li>";
			} else {
				$output .= "<a href='$posts_page'>$show_option_all</a>";
			}
		}

		if ( empty( $parsed_args['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
			$current_term_object = get_queried_object();
			if ( $current_term_object && $parsed_args['taxonomy'] === $current_term_object->taxonomy ) {
				$parsed_args['current_category'] = get_queried_object_id();
			}
		}

		if ( $parsed_args['hierarchical'] ) {
			$depth = $parsed_args['depth'];
		} else {
			$depth = -1; // Flat.
		}

		$walker = new Walker_Category_Custom;
//echo print_r($categories,true);
		$output .= $walker->walk( $categories, $depth, $parsed_args );
	}

	if ( $parsed_args['title_li'] && 'list' == $parsed_args['style'] && ( ! empty( $categories ) || ! $parsed_args['hide_title_if_empty'] ) ) {
		$output .= '</ul></li>';

	}

$html = apply_filters( 'custom_list_categories', $output, $args );

	if ( $parsed_args['echo'] ) {
		echo $html;
	} else {
		return $html;
	}

}
/* inicio - cambio pagina de registro  */ 
add_filter( 'register_url', 'custom_register_url' );
function custom_register_url( $register_url )
{
    $register_url = get_permalink( $register_page_id = '3042' );
    return $register_url;
}
/* fin - cambio pagina de registro */




//add_filter( 'woocommerce_checkout_fields', 'ubigeo_add_field' );

add_filter( 'woocommerce_billing_fields', 'ubigeo_add_field_billing',9999 );
add_filter( 'woocommerce_after_edit_address_form_billing', 'buscar_ubigeo_billing',9999 );

add_filter( 'woocommerce_shipping_fields', 'ubigeo_add_field_shipping',9999 );
add_filter( 'woocommerce_after_edit_address_form_shipping', 'buscar_ubigeo_shipping',9999 );


add_filter( 'woocommerce_after_checkout_billing_form', 'buscar_ubigeo_billing',9999 );
add_filter( 'woocommerce_before_checkout_shipping_fields', 'add_extra_fields_shipping',9999 );
add_filter( 'woocommerce_after_checkout_shipping_form', 'buscar_ubigeo_shipping2',9999 );

add_action( 'woocommerce_checkout_update_order_meta', 'save_custom_field_usermeta', 90 );
//add_action( 'woocommerce_checkout_update_order_meta', 'syncronize_order_to_erp', 99 );


    
function save_custom_field_usermeta($order_id){
    
$order = new WC_Order($order_id);
//$user_id = $order->customer_user;

if ( ! empty( $_POST['shipping_shipperagency'] ) ) {
    update_post_meta($order_id, 'shipping_shipperagency', $_POST['shipping_shipperagency']);
}

}

function buscar_ubigeo_billing(){
   
    
    ?>
      <script type="text/javascript">
		 
		  // DEPARTAMENTO ....
		  if(document.getElementById("dpto_anterior")!=null){
    		  obj = document.getElementById("billing_department");
    		  if(obj!=null){
    		  var indice=0;
    		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
    				opt = obj.options[i];
    				if ( opt.value == document.getElementById("dpto_anterior").value ) {
    					indice = i;
    					break;
    		   }}
    		  
    		   document.getElementById("billing_department").selectedIndex=indice; 
    		  	
    			jQuery(document).ready(function () {
    			jQuery("#billing_department").trigger('change');
    		  });
    		  }
		  }
	  </script>
     <?php
	 add_checkout_script("billing");
}

function ubigeo_add_field_billing( $fields ) {
    
    unset($fields['billing_country']);
    
  
    $fields['billing_department']   = array(
        'label'        => 'Departamento',
        'required'     => true,
        'class'        => array('rt-field', 'form-row-wide'),
        'priority'     => 1000,
        'options'	=> departamento_select(),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    $fields['billing_city']   = array(
        'label'        => 'Provincia',
        'required'     => true,
        'class'        => array('billing_city', 'form-row-wide'),
        'priority'     => 1100,
        'options'	=>  array(''=> 'Provincia'),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    $fields['billing_district']   = array(
        'label'        => 'Distrito',
        'required'     => true,
        'class'        => array('billing_district', 'form-row-wide'),
        'priority'     => 1200,
        'options'	=> array(''=> 'Distrito'),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    
      
    return $fields;
    
}




function buscar_ubigeo_shipping(){
    
    $address_name = "shipping";
    if ( ! empty( $_GET['address-book'] ) ) {
        $address_name = sanitize_text_field( wp_unslash( $_GET['address-book'] ) );
    }
    
    echo "<script>var address_name = '".$address_name."';</script>";
    ?>
      <script type="text/javascript">
		 
		  // DEPARTAMENTO ....
		  if(document.getElementById("dpto_anterior")!=null){
    		  obj = document.getElementsByName(address_name+"_department")[0];
    		  if(obj!=null){
    		  var indice=0;
    		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
    				opt = obj.options[i];
    				if ( opt.value == document.getElementById("dpto_anterior").value ) {
    					indice = i;
    					break;
    		   }}
    		  
    		   document.getElementsByName(address_name+"_department")[0].selectedIndex=indice; 
    		  	
    			jQuery(document).ready(function () {
    				jQuery("select[name*='"+address_name+"_department']").trigger('change');
    			
    		  });
    		  }
		  }
	  </script>
     <?php
     add_checkout_script($address_name);
}



function buscar_ubigeo_shipping2(){
    
    $address_name = "shipping";
    if ( ! empty( $_GET['address-book'] ) ) {
        $address_name = sanitize_text_field( wp_unslash( $_GET['address-book'] ) );
    }
    
    echo "<script>var address_name2 = '".$address_name."';</script>";
    ?>
      <script type="text/javascript">
		 
		  // DEPARTAMENTO ....
		  if(document.getElementById("dptos_anterior")!=null){
    		  obj = document.getElementsByName(address_name2+"_department")[0];
    		  if(obj!=null){
    		  var indice=0;
    		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
    				opt = obj.options[i];
    				if ( opt.value == document.getElementById("dptos_anterior").value ) {
    					indice = i;
    					break;
    		   }}
    		  
    		   document.getElementsByName(address_name2+"_department")[0].selectedIndex=indice; 
    		  	
    			jQuery(document).ready(function () {
    				jQuery("select[name*='"+address_name2+"_department']").trigger('change');
    			
    		  });
    		  }
		  }
	  </script>
     <?php
     add_checkoutShipCheckout_script($address_name);
}


define('DONOTCACHEPAGE', true);

function ubigeo_add_field_shipping( $fields ) {
    
    $address_name = "shipping";
    if ( ! empty( $_GET['address-book'] ) ) {
        $address_name = sanitize_text_field( wp_unslash( $_GET['address-book'] ) );
    }
    
    
    $fields[$address_name.'_department']   = array(
        'label'        => 'Departamento',
        'required'     => true,
        'class'        => array('rt-field', 'form-row-wide'),
        'priority'     => 100,
        'options'	=> departamento_select(),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    $fields[$address_name.'_city']   = array(
        'label'        => 'Provincia',
        'required'     => true,
        'class'        => array('shipping_city', 'form-row-wide'),
        'priority'     => 110,
        'options'	=>  array(''=> 'Provincia'),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    $fields[$address_name.'_district']   = array(
        'label'        => 'Distrito',
        'required'     => true,
        'class'        => array('shipping_district', 'form-row-wide'),
        'priority'     => 120,
        'options'	=> array(''=> 'Distrito'),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    
    $fields[$address_name.'_calle']   = array(
        'label'        => 'Dirección de la Calle',
        'required'     => true,
        'class'        => array('calle', 'form-row-wide'),
        'priority'     => 10,
        'type'          => 'text',
        'label_class'   => 'rt-label'
    );
      
    return $fields;
    
}

function add_extra_fields_shipping($fields){
    
    $fields['shipping_shipperagency']   = array(
        'label'        => 'Agencia de Transporte (Sólo para despachos a provincia.)',
        'required'     => true,
        'class'        => array('rt-field', 'form-row-wide'),
        'priority'     => 140,
        'options'	=> shipperagency_select(),
        'type'          => 'select',
        'label_class'   => 'rt-label'
    );
    return $fields;
}

function shipperagency_select()
{
	$items = array();
		$items[]='SIN DESPACHO';
	    // DESHABILITADO TEMPORALMENTE... SOLO DESPACHOS PARA TRUJILLO Y AREQUIPA
        /*
        $items[0]='Despacho directo (SÓLO PARA LIMA)';
        $items[1] = 'COTINSA (Urb. César Vallejo - Calle 1 Mz. 2 Lt. 2)';
        $items[2] = 'MARVISUR (CALLE GARCI CARBAJAL NRO. 511 URB. IV CENTENARIO - AREQUIPA)';
        $items[3] = 'GRUPO J&H EIRL (Productos Peligrosos) (Av. Aurelio Garcia y Garcia Nro. 1580)';
        
        $items[4] = 'EMTRAFESA (157, Av Túpac Amaru 185, Trujillo 13001)';
        $items[5] = 'OLTURSA (Av Ejercito 342, Trujillo 13001)';
        $items[6] = 'GRAU LOGISTICA EXPRESS SAC (Productos Peligrosos) (Av América Sur 2104, Trujillo 13006 / Auxiliar Panamericana Nte. KM 561, Moche 13008)';
        
        $items[7] = 'ITTSA (Av. Ignacio Merino. Ex Campamento Graña, Av. F, Talara)';
        */
        return $items;
}


add_action( 'woocommerce_after_checkout_validation', 'custom_validate_fields', 10, 2);

function custom_validate_fields( $fields, $errors ){
    
    if($fields['billing_wooccm9']=='--Seleccione--'){
        $errors->add( 'validation', 'Tipo de Documento es un campo requerido' );
    }
    
    if($fields['billing_wooccm9']=='DNI' && strlen($fields['billing_wooccm10'])!=8){//DNI
        $errors->add( 'validation', count($fields['billing_wooccm10']).'DNI debe tener 8 caracteres numéricos.' );
    }
    
    if(count($fields['billing_wooccm10'])==0){
        $errors->add( 'validation', 'Debe ingresar el numero de documento del cliente.' );
    }
    
    if($fields['additional_wooccm0']=="Factura"){//RUC Obligatorio
        if(strlen($fields['additional_wooccm2'])!=11){
            $errors->add( 'validation', 'RUC debe tener 11 caracteres numéricos.' );
        }
        
    }
    
    if ( preg_match( '/\\d/', $fields[ 'billing_first_name' ] ) || preg_match( '/\\d/', $fields[ 'billing_last_name' ] )  ){
        $errors->add( 'validation', 'Nombre o apellidos contienen números... En serio?' );
    }
    
    
}



function syncronize_order_to_erp( $orderid){
    
    //si la orden aun está pendiente, enviarla a payu
    $order = new WC_Order($orderid);
    
    //si la orden no se ha enviado
    $alreadySync = false;
    
    $syncro = get_post_meta( $orderid, 'erp_syncro', true );
    
    
    
    if(isset($syncro) && $syncro=='OK')
        $alreadySync = true;
         $lines_string="";
        if(!$alreadySync){
            $url = 'http://beta.sekurperu.com.pe/ecommerce/index.php/woocommerce/woocommerce_addorder/5245928cww323dm'.$orderid;

            $handle=fopen($url,"rb");
            
           
            do{
                $data=fread($handle,1024);
                if(strlen($data)==0) {
                    break;
                }
                $lines_string.=$data;
            }while(true);
            fclose($handle);
            
             

            if($lines_string=='1'){
                $alreadySync = true;
                
                $url = 'http://beta.sekurperu.com.pe/ecommerce/index.php/woocommerce/woocommerce_updatestocks/5245928cww323dm';
                $handle=fopen($url,"rb");
                fclose($handle);
                
            }
        }
//$alreadySync=true;
        if($alreadySync){
         
        }else{
            
            if (!empty($order)) {
            //    $order->update_status( 'cancelled' );
            }
            
            //throw new Exception( 'El pedido no pudo ser procesado debido a inconvenientes con el sistema o falta de stock. Por favor intente nuevamente o contacte con uno de nuestros asesores.' );
            

        }
}
