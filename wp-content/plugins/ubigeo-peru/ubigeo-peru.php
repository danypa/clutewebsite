<?php

/**
 *
 * @link              https://renzotejada.com/
 * @since             2.0.1
 * @package           Ubigeo de Per&uacute;
 *
 * @wordpress-plugin
 * Plugin Name:       Ubigeo de Per&uacute; para Woocommerce
 * Plugin URI:        https://renzotejada.com/blog/ubigeo-de-peru-para-woocommerce/
 * Description:       Ubigeo de Per&uacute; para woocommerce - Plugin contiene los departamentos - provincias y distritos del Per&uacute;
 * Version:           2.0.1
 * Author:            Renzo Tejada
 * Author URI:        https://renzotejada.com/
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       ubigeo-peru
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Currently plugin version.
 * Start at version 2.0.1
 */
define('UBIGEO_PERU_VERSION', '2.0.1');
define('UBIGEO_TITLE', 'Ubigeo Perú');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ubigeo-peru-activator.php
 */
function activate_ubigeo_peru() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru-activator.php';
    Ubigeo_Peru_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ubigeo-peru-deactivator.php
 */
function deactivate_ubigeo_peru() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru-deactivator.php';
    Ubigeo_Peru_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ubigeo_peru');
register_deactivation_hook(__FILE__, 'deactivate_ubigeo_peru');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ubigeo-peru.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.1
 */
function run_ubigeo_peru() {

    $plugin = new Ubigeo_Peru();
    $plugin->run();
}

run_ubigeo_peru();





function add_checkout_script($load_address) {
    
    echo "<script>var load_address = '".$load_address."';</script>";
    ?>

<script type="text/javascript">

(function($) {

	var form = load_address;
	
    var country = $("select[name*='"+form+"_department']");
    var state = $("select[name*='"+form+"_city']");
    
    if (country.length) {
        country.change(function() {

		 $('.overlay').fadeIn('fast');
            var $this = $(this);
            get_states($(this).val(), function(response) {

                
                var obj = JSON.parse(response);

                var len = obj.length;
                var $stateValues = '';

                $("select[name*='"+form+"_city']").empty();
                $("select[name*='"+form+"_district']").empty();
                for (i = 0; i < len; i++) {
                    var mystate = obj[i];
                    $stateValues += '<option value="' + mystate.idProv +'-'+mystate.provincia+'">' + mystate.provincia +
                        '</option>';
                }
                $("select[name*='"+form+"_city']").append($stateValues);

              //si existe la variable prov_anterior colocarla
              if(document.getElementById("prov_anterior")!=null){
				obj = document.getElementsByName(form+"_city")[0];
          		  var indice=0;
          		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
          				opt = obj.options[i];
          				if ( opt.text == document.getElementById("prov_anterior").value
                  				|| opt.value == document.getElementById("prov_anterior").value ) {
          					indice = i;
          					break;
          				}}
      		  
      		    document.getElementsByName(form+"_city")[0].selectedIndex=indice; 
      		  
      			jQuery(document).ready(function () {
      			
      			jQuery("select[name*='"+form+"_city']").trigger('change');
      			});		  
              }

            });
            /* JSON populate Region/State Listbox */
        });
    }

    if (state.length) {
        state.change(function() {
		$('.overlay').fadeIn('fast');
            var $this = $(this);
            get_cities($(this).val(), function(response) {
                var obj = JSON.parse(response);
                var len = obj.length;
                var $cityValues = '';

		if(typeof calcularShippingAgency === "function"){
                calcularShippingAgency();
		}
                $("select[name*='"+form+"_district']").empty();
                for (i = 0; i < len; i++) {
                    var mycity = obj[i];
                    $cityValues += '<option value="' + mycity.id + '">' + mycity.city_name +
                        '</option>';
                }
                $("select[name*='"+form+"_district']").append($cityValues);

                if(document.getElementById("dist_anterior")!=null){
                obj = document.getElementsByName(form+"_district")[0];
      		  var indice=0;
      		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
      				opt = obj.options[i];
      				if ( opt.text == document.getElementById("dist_anterior").value ||
      						 opt.value == document.getElementById("dist_anterior").value ) {
      					indice = i;
      					break;
      				}}
      		  
      			document.getElementsByName(form+"_district")[0].selectedIndex=indice; 
      			
                }
		$('.overlay').fadeOut('slow');
		  		  
            });

        });
        /* JSON populate Cities Listbox */
    }

    function get_states(deptoCode, callback) {
        var data = {
            action: 'get_states_call',
            country_code: deptoCode
        };
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    }

    function get_cities(rowCODE, callback) {
        var data = {
            action: 'get_cities_call',
            row_code: rowCODE
        };
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    }

    
  
})(jQuery);
</script>
<?php       


}




function add_checkoutShipCheckout_script($load_address) {
    
    echo "<script>var load_address2 = '".$load_address."';</script>";
    ?>

<script type="text/javascript">


function calcularShippingAgency(){

  //inicio cambio cambiar transportista
var	form = 'shipping';
               
                if (!document.getElementById("ship-to-different-address-checkbox").checked) {
		
			form ='billing';
		}

                    // POBLAR TRANSPORTISTAS SEGUN PROVINCIA
                    var prov0 = document.getElementsByName(form+"_city")[0].value;

                    var select = document.getElementsByName("shipping_shipperagency")[0];    
                    select.options.length = 0;

                    if (prov0.includes("Lima")){
                        var array1 = {
                            '0' : 'Despacho directo (SÓLO PARA LIMA)'
                        };
                        
                        for(index in array1) {
                            select.options[index] = new Option(array1[index], index);
                        }
                    }

                    if (prov0.includes("Trujillo")){
                        var array1 = {
                            '0' : 'EMTRAFESA (157, Av Túpac Amaru 185, Trujillo 13001)',
                            '1' : 'GRAU LOGISTICA EXPRESS SAC (Productos Peligrosos) (Av América Sur 2104, Trujillo 13006 / Auxiliar Panamericana Nte. KM 561, Moche 13008)'
                        };
                        
                        for(index in array1) {
                            select.options[index] = new Option(array1[index], index);
                        }
                    }

                    if (prov0.includes("Arequipa")){
                        var array1 = {
                            '0' : 'COTINSA (Urb. César Vallejo - Calle 1 Mz. 2 Lt. 2)',
                            '1' : 'MARVISUR (CALLE GARCI CARBAJAL NRO. 511 URB. IV CENTENARIO - AREQUIPA)'
                        };

                        for(index in array1) {
                            select.options[index] = new Option(array1[index], index);
                        }                                        
                    }

                    if (!prov0.includes("Arequipa") && !prov0.includes("Trujillo") && !prov0.includes("Lima")){
                        var array1 = {
                            '0' : 'SIN DESPACHO' 
                        };

                        for(index in array1) {
                            select.options[index] = new Option(array1[index], index);
                        }                                        
                    }
                
                //fin cambio cambiar transportista

}

(function($) {

	var form = load_address2;
	
    var country = $("select[name*='"+form+"_department']");
    var state = $("select[name*='"+form+"_city']");
    
    if (country.length) {
        country.change(function() {
		$('.overlay').fadeIn('fast');
            var $this = $(this);
            get_states($(this).val(), function(response) {

                
                var obj = JSON.parse(response);

                var len = obj.length;
                var $stateValues = '';

                $("select[name*='"+form+"_city']").empty();
                $("select[name*='"+form+"_district']").empty();
                for (i = 0; i < len; i++) {
                    var mystate = obj[i];
                    $stateValues += '<option value="' + mystate.idProv +'-'+mystate.provincia+'">' + mystate.provincia +
                        '</option>';
                }
                $("select[name*='"+form+"_city']").append($stateValues);

              //si existe la variable prov_anterior colocarla
              if(document.getElementById("provs_anterior")!=null){
				obj = document.getElementsByName(form+"_city")[0];
          		  var indice=0;
          		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
          				opt = obj.options[i];
          				if ( opt.text == document.getElementById("provs_anterior").value
                  				|| opt.value == document.getElementById("provs_anterior").value ) {
          					indice = i;
          					break;
          				}}
      		  
      		    document.getElementsByName(form+"_city")[0].selectedIndex=indice; 
      		  
      			jQuery(document).ready(function () {
      			
      			jQuery("select[name*='"+form+"_city']").trigger('change');
      			});		  
              }

            });
            /* JSON populate Region/State Listbox */
        });
    }

    if (state.length) {
        state.change(function() {
		$('.overlay').fadeIn('fast');
            var $this = $(this);
            get_cities($(this).val(), function(response) {
                var obj = JSON.parse(response);
                var len = obj.length;
                var $cityValues = '';

                calcularShippingAgency();


                $("select[name*='"+form+"_district']").empty();
                for (i = 0; i < len; i++) {
                    var mycity = obj[i];
                    $cityValues += '<option value="' + mycity.id + '">' + mycity.city_name +
                        '</option>';
                }
                $("select[name*='"+form+"_district']").append($cityValues);

                if(document.getElementById("dists_anterior")!=null){
                obj = document.getElementsByName(form+"_district")[0];
      		  var indice=0;
      		  for ( var i = 0, len = obj.options.length; i < len; i++ ) {
      				opt = obj.options[i];
      				if ( opt.text == document.getElementById("dists_anterior").value ||
      						 opt.value == document.getElementById("dists_anterior").value ) {
      					indice = i;
      					break;
      				}}
      		  
      			document.getElementsByName(form+"_district")[0].selectedIndex=indice; 
      			
                }
		  	$('.overlay').fadeOut('slow');	  
            });

        });
        /* JSON populate Cities Listbox */
    }

    
//que llame a w5media para que carge mas rapido
    function get_states(deptoCode, callback) {
        var data = {
            action: 'get_states_call',
            country_code: deptoCode
        };
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    }

    function get_cities(rowCODE, callback) {
        var data = {
            action: 'get_cities_call',
            row_code: rowCODE
        };
        $.post(ajaxurl, data, function(response) {
            callback(response);
        });
    }

    
  
})(jQuery);
</script>
<?php       
}
