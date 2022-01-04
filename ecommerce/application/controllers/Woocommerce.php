<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Woocommerce extends CI_Controller {
    
    
 // función que guarda en el fichero de log una variable
    function log_message_text(String $userName,String $str_Message){
          $logmessage =  '[' . date('Y-m-d h:i:s') .' '.$userName.'] '. $str_Message . "\n";
          error_log($logmessage, 3,  'syncro.log');      
    }

    function base64url_encode($data)
    {
        // First of all you should encode $data to Base64 string
        $b64 = base64_encode($data);
        
        // Make sure you get a valid result, otherwise, return FALSE, as the base64_encode() function do
        if ($b64 === false) {
            return false;
        }
        
        // Convert Base64 to Base64URL by replacing ?+? with ?-? and ?/? with ?_?
        $url = strtr($b64, '+/', '-_');
        
        // Remove padding character from the end of line and return the Base64URL result
        return rtrim($url, '=');
    }
    
    
    function base64url_decode($data, $strict = false)
    {
        // Convert Base64URL to Base64 by replacing ?-? with ?+? and ?_? with ?/?
        $b64 = strtr($data, '-_', '+/');
        
        // Decode Base64 string and return the original data
        return base64_decode($b64, $strict);
    }
    
    public function index()
    {
    }
    
    
    
    public function woocommerce_addproducts($tokens64){
        $base = $this->base64url_decode($tokens64);
        list($token, $postid, $products) = explode('|', $base);
        $lsProducts = explode(',', $products);
        
        if($token!='5245928cww323dm'){
            echo "0"; return;
        }
        
        //traer info por webservices
        $urlsyncro='https://sekurperu.pe:10443/openbravo';
        $identity='eCommerce@clutesa';
        $password='commP$$9w';
        $vstore='CL001';
        
        $ecommerce = $this->syncronizeSekur->syncroGetVirtualStore($urlsyncro, $identity, $password, $vstore);
        
        if($ecommerce!=null ){
            //verificar que productos ya existen, estos no insertarlos
            $lsProds = array();
            $lsData = array();
            foreach($lsProducts as $product){
                $product = trim($product);
                $exists = $this->syncronizeSekur->existsProduct($product);
                if(!$exists){
                    $data = $this->syncronizeSekur->syncroGetProduct($urlsyncro, $identity, $password, $ecommerce['warehouseId'],  $ecommerce['pricelistId'], $ecommerce['organizationId'], $ecommerce['id'], $product, $postid);
                    if($data){
                        $lsProds[]=$product;
                        $lsData[] = $data;
                    }
                }
            }
            
            $this->syncronizeSekur->updateCats();
            
            echo "1";
        }
        
        else
            echo "0";
    }
    
    //http://localhost/ecommerce/index.php/woocommerce/woocommerce_updatestocks/539#245928cww@23dm
    public function woocommerce_updatestocks($token)
    {
       $this->log_message_text('ecommerce','Llamada a proceso de actualizacion de stocks');
        if($token!='5245928cww323dm'){
            echo "0"; return;
        }
        
        
        $urlsyncro='https://sekurperu.pe:10443/openbravo';
	//$urlsyncro='https://sekursa.prime-erp.com:8184/openbravo2';
        $identity='eCommerce@clutesa';
        $password='commP$$9w';
        $vstore='CL001';
        
        $ecommerce = $this->syncronizeSekur->syncroGetVirtualStore($urlsyncro, $identity, $password, $vstore);
        
        //ENVIAR DATOS PENDIENTES AL ERP
        if($ecommerce!=null ){
            
            //SINCRONIZANDO NUEVOS PRODUCTOS
            if($this->syncronizeSekur->syncroProducts($urlsyncro, $identity, $password, $ecommerce['warehouseId'],  $ecommerce['pricelistId'], $ecommerce['organizationId'], $ecommerce['id'])){
                echo "1";
            }else{
                echo "0";
            }
            
        }else{
            echo "0";
        }
    }
    
    
    public function woocommerce_addorder($token){
        if(substr($token, 0, 15 )!='5245928cww323dm'){
            echo "0"; return;
        }
        $orderid = substr($token, 15);
        
        
       // $urlsyncro='https://sekurperu.pe:10443/openbravo';
	$urlsyncro='https://sekursa.prime-erp.com:8184/openbravo2';
        $identity='eCommerce@clutesa';
        $password='commP$$9w';
        $vstore='CL001';
        
        $ecommerce = $this->syncronizeSekur->syncroGetVirtualStore($urlsyncro, $identity, $password, $vstore);
        
        //ENVIAR DATOS PENDIENTES AL ERP
        if($ecommerce!=null ){
            
            //SINCRONIZANDO NUEVOS PRODUCTOS
            if($this->syncronizeSekur->desyncroSales($urlsyncro, $identity, $password, $ecommerce['warehouseId'],  $ecommerce['pricelistId'], $ecommerce['organizationId'], $ecommerce['id'], $orderid)){
                echo "1";
            }else{
                echo "0";
            }
            
        }else{
            echo "0";
        }
        
    }
    
    
    public function woocommerce_invoicing($token)
    {
        
        if($token!='5245928cww323dm'){
            echo "0"; return;
        }
        
        
        //$urlsyncro='https://sekurperu.pe:10443/openbravo';
	$urlsyncro='https://sekursa.prime-erp.com:8184/openbravo2';

        $identity='eCommerce@clutesa';
        $password='commP$$9w';
        $vstore='CL001';
        
        $ecommerce = $this->syncronizeSekur->syncroGetVirtualStore($urlsyncro, $identity, $password, $vstore);
        
        //ENVIAR DATOS PENDIENTES AL ERP
        if($ecommerce!=null ){
            
            //SINCRONIZANDO NUEVOS PAGOS
            if($this->syncronizeSekur->desyncroPayments($urlsyncro, $identity, $password, $ecommerce['warehouseId'],  $ecommerce['pricelistId'], $ecommerce['organizationId'], $ecommerce['id'])){
                echo "1";
            }else{
                echo "0";
            }
            
        }else{
            echo "0";
        }
    }
}
