<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Syncro extends CI_Controller {


	function startsWith($haystack, $needle)
	{
		  $length = strlen($needle);
		  return (substr($haystack, 0, $length) === $needle);
	}

	
	public function index()
	{
	
		$company = "";
		$clientid = "";
		$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		$urlsyncro = "";
		if($this->startsWith($url, 'dss.')){
			$company = "dss.png";
			$clientid = "AF645935B14444CA8AD4A94FE6B2AF68";
		   $urlsyncro = "http://localhost:8080/openbravo";

		}else{
			$company = "dss.png";
			$clientid = "AF645935B14444CA8AD4A94FE6B2AF68";
		   $urlsyncro = "http://localhost:8080/openbravo";
		}
		


		$this->config->set_item('language', 'spanish');
		$this->form_validation->set_rules('identity', 'Usuario', 'required');
		$this->form_validation->set_rules('password', "Contraseña", 'required');
		$this->form_validation->set_rules('vstore', "Contraseña", 'required');
		
		if ($this->form_validation->run() == true) {

				$identity = $this->input->post('identity');
				$password = $this->input->post('password');
				$vstore = $this->input->post('vstore');

				$ecommerce = $this->syncronizeSekur->syncroGetVirtualStore($urlsyncro, $identity, $password, $vstore);
		

				//ENVIAR DATOS PENDIENTES AL ERP
				if($ecommerce!=null ){
				
					//SINCRONIZANDO NUEVOS PRODUCTOS
					if($this->syncronizeSekur->syncroProducts($urlsyncro, $identity, $password, $ecommerce['warehouseId'],  $ecommerce['pricelistId'], $ecommerce['organizationId'], $ecommerce['id'])){
						//HACER SYNCRONIZACION				
						$this->data['company'] = $company;
						$this->data['message'] = "Sincronización correcta de datos de tienda virtual <b>".$urlsyncro."</b> para el usuario <b>".$identity."</b>. Por favor espere unos minutos y vuelva a revisar sus datos en su aplicación ecommerce";			
						$this->load->view('syncro', $this->data);		
					}else{
						$this->data['company'] = $company;
						$this->data['error'] = "Datos transmitidos correctamente al ERP. Datos incorrectos al actualizar productos en la plataforma ecommerce. Intente nuevamente. ".$this->syncronizeSekur->getErrorMessage();
						$this->load->view('syncro', $this->data);
					}

				}else{
					$this->data['company'] = $company;
					$this->data['error'] = "Error al enviar datos de ventas al ERP. Intente nuevamente. Error: ".$this->syncronizeSekur->getErrorMessage();
					$this->load->view('syncro', $this->data);
				}

 		} else {

			$this->data['company'] = $company;
			$this->data['error'] = (validation_errors()) ? validation_errors() : null;
		   $this->load->view('syncro', $this->data);
		}
	}


}
