<?php


/**
 * Fill the countries select
 * @return Array
 */
function departamento_select($selectedCountry = null) {
    global $wpdb;
    $db = $wpdb->get_results("SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM " . $wpdb->prefix . "ubigeo_departamento order by departamento ASC");

    $items = array();

    if (null == $selectedCountry)
        $items[] = 'Seleccione';

    foreach ($db as $data) {
        $items[$data->idDepa] = ucwords(strtolower($data->departamento));
    }
    return $items;
}

//obtener todo los departamento
function getDepartamento() {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM $table_name";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener el departamento por su idDepa
function getDepartamentoByidDepa($idDepa = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_departamento";
    $request = "SELECT idDepa, CONCAT(UCASE(LEFT(departamento, 1)), LOWER(SUBSTRING(departamento, 2))) as departamento FROM $table_name  where idDepa = $idDepa";
    $dto = $wpdb->get_results($request, ARRAY_A);
    return $dto[0]['departamento'];
}

//obtener las provincias por idDepa
function getProvinciaByidDepa($idDepa = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT idProv, CONCAT(UCASE(LEFT(provincia, 1)), LOWER(SUBSTRING(provincia, 2))) as provincia, idDepa FROM $table_name where idDepa = $idDepa";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener provincia por idProv
function getProvinciaByidProv($idProv = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_provincia";
    $request = "SELECT idProv, CONCAT(UCASE(LEFT(provincia, 1)), LOWER(SUBSTRING(provincia, 2))) as provincia, idDepa FROM $table_name where idProv = $idProv";
    $idProv = $wpdb->get_results($request, ARRAY_A);
    return $idProv[0]['provincia'];
}

//obtener distrito por idProv
function getDistritoByidProv($idProv = 0) {
    global $wpdb;
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT idDist, CONCAT(UCASE(LEFT(distrito, 1)), LOWER(SUBSTRING(distrito, 2))) as distrito, idProv FROM $table_name where idProv = $idProv";
    return $wpdb->get_results($request, ARRAY_A);
}

//obtener distrito por idDist
function getDistritoByidDist($idDist = 0) {
    global $wpdb;
   
    $table_name = $wpdb->prefix . "ubigeo_distrito";
    $request = "SELECT idDist, CONCAT(UCASE(LEFT(distrito, 1)), LOWER(SUBSTRING(distrito, 2))) as distrito, idProv FROM $table_name where idDist = $idDist";
    $dist = $wpdb->get_results($request, ARRAY_A);
    return $dist[0]['distrito'];
}


function get_shippingagency($prov0, $index){

	if($prov0=="Arequipa"){
		if($index=="0"){
			return 'COTINSA (Urb. César Vallejo - Calle 1 Mz. 2 Lt. 2)';
		}else if($index=="1"){	
			return 'MARVISUR (CALLE GARCI CARBAJAL NRO. 511 URB. IV CENTENARIO - AREQUIPA)';
		}
	}
	
	if($prov0=="Trujillo"){
		if($index=="0"){
			return 'EMTRAFESA (157, Av Túpac Amaru 185, Trujillo 13001)';
		}else if($index=="1"){	
			return 'GRAU LOGISTICA EXPRESS SAC (Productos Peligrosos) (Av América Sur 2104, Trujillo 13006 / Auxiliar Panamericana Nte. KM 561, Moche 13008)';
		}
	}

	if($prov0=="Lima"){
		return 'Despacho directo (SÓLO PARA LIMA)';
	}

	return "Sin Despacho";

     }
