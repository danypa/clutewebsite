<?php

class SyncronizeSekur extends CI_Model
{
    
    private $conn = null;
    private $prefix = "wp";
    private $msgError = "Error en sincronización de datos";
    
    function __construct() {
        parent::__construct();
        
        $this->load->model('Ubigeo');
    }
    
    function getErrorMessage(){
        return $this->msgError;
    }
    
    // función que guarda en el fichero de log una variable
    function log_message_text(String $userName,String $str_Message){
          $logmessage =  '[' . date('Y-m-d h:i:s') .' '.$userName.'] '. $str_Message . "\n";
          error_log($logmessage, 3,  'syncro.log');      
    }
    
    function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    
    //SINCRONIZACION ERP-WOOCOMMERCE
    
    function createFeatureArray($arrProduct, $features){
        $arrFeatures = explode(";", $features);
        $arrResult = array();
        for($i=0; $i<count($arrFeatures); $i++)
        {
            $property = "prime_cf".($i+1);
            $arrResult[$arrFeatures[$i]] = (string)($arrProduct->$property);
            
        }
        
        return $arrResult;
    }
    
    
    
    
    function insertPostMeta($postId, $attribute, $value, $verifyAlreadyExists){
        
        $exists =0;
        if($verifyAlreadyExists){
            $exists = $this->db->get_where('postmeta', array('meta_key'=> $attribute, 'post_id' => $postId))->num_rows();
        }
        
        if($exists==0){//inserta
            $detailsMeta = array('post_id' => $postId, 'meta_key' => $attribute, 'meta_value' => $value);
            if(!$this->db->insert('postmeta', $detailsMeta)){ return false;}
            
        }else{//actualiza
            $detailsMeta = array('meta_value' => $value);
            if(!$this->db->update('postmeta', $detailsMeta, array('post_id' => $postId, 'meta_key' => $attribute))) return false;
        }
        
        return true;
    }


   function getPostMeta($postId, $attribute){
        
       
            $qf = $this->db->get_where('postmeta', array('meta_key'=> $attribute, 'post_id' => $postId));
            $exists = $qf->num_rows();
        
        if($exists==0){
	     return null;
            
        }else{
            foreach (($qf->result()) as $rowMeta) {
               return $rowMeta->meta_value;
	   }
        }
	return null;
    }
    
 function insertPostMetaOnlyIfNotExists($postId, $attribute, $value){
        
           $exists = $this->db->get_where('postmeta', array('meta_key'=> $attribute, 'post_id' => $postId))->num_rows();
        
        if($exists==0){//inserta
            $detailsMeta = array('post_id' => $postId, 'meta_key' => $attribute, 'meta_value' => $value);
            if(!$this->db->insert('postmeta', $detailsMeta)){ return false;}
            
        }
        
        return true;
    }
    
    function insertPostMetaAttributes($postId, $arrFeatures, $verifyAlreadyExists){
        
        //feature "dimensiones" tratarla diferente
        
        $attr_keys = '_product_attributes';
        
        $ddArray = array();
        $exists =0;
        if($verifyAlreadyExists){
            $query =	$this->db->get_where('postmeta', array('meta_key'=> $attr_keys, 'post_id' => $postId));
            $exists = $query->num_rows();
            if($exists>0) $ddArray =unserialize($query->row()->meta_value);
        }
        
        $position = 0;
        foreach(array_keys($arrFeatures) as $key){
            if($key!='dimensiones'){
                if($arrFeatures[$key]!=null && $arrFeatures[$key]!='')
                    $ddArray[$key] = array('name' => $key , 'value' => $arrFeatures[$key], 'position' => $position, 'is_visible' => 1, 'is_variation' => 0, 'is_taxonomy' => 0) ;
                    $position++;
            }
        }
        
        if($exists==0){//inserta
            $detailsMeta = array('post_id' => $postId, 'meta_key' => $attr_keys, 'meta_value' => serialize($ddArray));
            if(!$this->db->insert('postmeta', $detailsMeta)){ return false;}
            
        }else{//actualiza
            $detailsMeta = array('meta_value' => serialize($ddArray));
            if(!$this->db->update('postmeta', $detailsMeta, array('post_id' => $postId, 'meta_key' => $attr_keys))) return false;
        }
        
        //ahora tratar feature "dimensiones"
        if(isset($arrFeatures['dimensiones']) && $arrFeatures['dimensiones']!=null && $arrFeatures['dimensiones']!=''){
            $_length = 0;
            $_width = 0;
            $_height = 0;
            
            $arrDim = explode("x", $arrFeatures['dimensiones']);
            if(count($arrDim)>0)
                $_length = $arrDim[0];
                if(count($arrDim)>1)
                    $_width = $arrDim[1];
                    if(count($arrDim)>2)
                        $_height = $arrDim[2];
                        
                        $this->insertPostMeta($postId, '_length', $_length, $verifyAlreadyExists);
                        $this->insertPostMeta($postId, '_width', $_width, $verifyAlreadyExists);
                        $this->insertPostMeta($postId, '_height', $_height, $verifyAlreadyExists);
        }
        
        return true;
    }
    
    
    function existsProduct($productId){
        $query =	$this->db->get_where('postmeta', array('meta_key'=> '_sku', 'meta_value' => $productId));
        $exists = $query->num_rows();
        if($exists>0) return true;
        return false;
    }
    
    function setToVariableProduct($productId){
        
        $sqlDel = "delete rel from ".$this->prefix."_term_relationships rel where rel.object_id='".$productId."' and (SELECT trim(tax.taxonomy) from ".$this->prefix."_term_taxonomy tax WHERE tax.term_taxonomy_id=rel.term_taxonomy_id  ) IN ('product_type')";
        $result = $this->db->query($sqlDel);
        
        $taxonomy = array( 'object_id' => $productId ,  //modelo debe estar creado como atributo manualmente
            'term_taxonomy_id' => '4',//producto variable
            'term_order' => '0');
        return $this->db->insert('term_relationships', $taxonomy);
        
        
    }
    
    function existsTaxonomy($slug, $taxonomy){
        
        //term with the same slug and taxonomy
        $query =	$this->db->get_where('terms', array('slug'=> $slug));
        $exists = $query->num_rows();
        if($exists>0){
            foreach ($query->result() as $row){
                $queryTaxonomy =	$this->db->get_where('term_taxonomy', array('term_id'=> $row->term_id, 'taxonomy' => $taxonomy), 1);
                $exists = $queryTaxonomy->num_rows();
                if($exists>0){ return $queryTaxonomy->row()->term_taxonomy_id; }
            }
        }
        return false;
    }
    
    
    //SINCRONIZACION ERP-WOOCOMMERCE
    function syncroGetVirtualStore($url, $username, $password, $virtualStore){
        
        $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=getVirtualStore';
        
        $curl = curl_init($url_ws);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            "searchkey=$virtualStore");
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $curl_response = curl_exec($curl); 

        curl_close($curl);
        
        $vstore = null;
        if($this->startsWith($curl_response,'<?xml')){
            $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));
          
            foreach($xml->vstore as $pos){
                
                
                $vstore = array(
                    'id' => (string)$pos['id'],
                    'name' => $pos->value.' - '.$pos->name,
                    'defaultpartnerId' => $pos->defaultpartnerId,
                    'defaultpartnerDocno' => $pos->defaultpartnerDocno,
                    'defaultpartnerName' => $pos->defaultpartnerName,
                    'pricelistId' => $pos->pricelistId,
                    'pricelistCurrency' => $pos->pricelistCurrency,
                    'pricelistName' => $pos->pricelistName,
                    'organizationName' => $pos->organizationName,
                    'organizationId' => $pos->organizationId,
                    'orderTypeId' => $pos->orderTypeId,
                    'warehouseId' => $pos->warehouseId,
                    'locatorId' => $pos->locatorId,
                    'cashId' => $pos->cashId,
                    'salesrepId' => $pos->salesrepId,
                    'url' => $pos->url,
                    'features' => $pos->features
                );
                
            }
        }
        return $vstore;
    }
    
    
    function convertCategory($erpcode){
        if($erpcode=='2001') return "67";//term_taxonomy
        if($erpcode=='2003') return "68";
        if($erpcode=='2004') return "69";
        if($erpcode=='2005') return "70";
        if($erpcode=='2006') return "71";
        
        if($erpcode=='2009') return "72";
        if($erpcode=='2010') return "73";
        if($erpcode=='2011') return "74";
        if($erpcode=='2017') return "75";
        if($erpcode=='2018') return "76";
        
        if($erpcode=='2023') return "77";
        if($erpcode=='2024') return "78";
        if($erpcode=='2025') return "79";
        if($erpcode=='2026') return "80";
        if($erpcode=='2027') return "81";
        
        if($erpcode=='2028') return "82";
        if($erpcode=='2040') return "83";
        if($erpcode=='2050') return "84";
        
        return "67";
        
    }
    
    function convertCategoryClute($erpcode){
        if($erpcode=='301') return "67";//anteojos
        if($erpcode=='302') return "384";
        if($erpcode=='303') return "68";//guantes
        if($erpcode=='304') return "385";
        if($erpcode=='305') return "386";
        
        if($erpcode=='307') return "387";
        if($erpcode=='309') return "388";
        if($erpcode=='310') return "389";
        if($erpcode=='311') return "74";//botas
        if($erpcode=='312') return "78";//absorventes
        
        if($erpcode=='313') return "84";//equipo de rescate
        if($erpcode=='314') return "80";//telas
        if($erpcode=='315') return "392";
        if($erpcode=='316') return "393";
        if($erpcode=='317') return "394";
        
        if($erpcode=='318') return "395";
        if($erpcode=='319') return "396";
        if($erpcode=='320') return "397";
        
        if($erpcode=='321') return "398";
        if($erpcode=='322') return "399";
        if($erpcode=='323') return "400";
        
        return "67";
        
    }
    
    
    function convertBrand($erpcode){
        if($erpcode=='3M') return "88";
        if($erpcode=='CLUTE') return "86";
        if($erpcode=='MSA') return "87";
        return "86";
    }
    
    function updateCats(){
        $sqlUpdate1 = "update ".$this->prefix."_term_taxonomy tt
inner join ".$this->prefix."_terms t on t.term_id=tt.term_id
set tt.count = (select count(distinct tr.object_id) from ".$this->prefix."_posts p inner join ".$this->prefix."_term_relationships tr on p.id=tr.object_id
inner join ".$this->prefix."_postmeta pm on pm.post_id=id and pm.meta_key='_stock_status' and pm.meta_value<>'outofstock'
 where tr.term_taxonomy_id=tt.term_taxonomy_id and p.post_status<>'trash')
where tt.taxonomy='product_cat'";
        
        $sqlUpdate2 = "update ".$this->prefix."_termmeta tm inner join ".$this->prefix."_terms t on t.term_id=tm.term_id
inner join ".$this->prefix."_term_taxonomy tt on t.term_id=tt.term_id
set tm.meta_value=(select count(distinct tr.object_id) from ".$this->prefix."_posts p inner join ".$this->prefix."_term_relationships tr on p.id=tr.object_id
inner join ".$this->prefix."_postmeta pm on pm.post_id=id and pm.meta_key='_stock_status' and pm.meta_value<>'outofstock'
 where tr.term_taxonomy_id=tt.term_taxonomy_id and p.post_status<>'trash')
where tm.meta_key='product_count_product_cat'";
        
        $sqlUpdate0 = "update ".$this->prefix."_postmeta set meta_value='instock' where meta_key='_stock_status'";
        
        $sqlUpdate3 = "delete from ".$this->prefix."_term_relationships where term_taxonomy_id IN (9) AND object_id IN (select p.id from ".$this->prefix."_posts p where post_type='product')";
        
        $this->db->query($sqlUpdate0);
        $this->db->query($sqlUpdate1);
        $this->db->query($sqlUpdate2);
        $this->db->query($sqlUpdate3);
        
    }
    
    
    function syncroGetProduct($url, $username, $password, $warehouseId, $pricelistId, $orgId, $posId, $product, $postid){
        // $product='2003A586';
        
        $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=getStockAndPriceStoreFull&warehouseId='.$warehouseId.'&pricelistId='.$pricelistId;
        
        
        $curl = curl_init($url_ws);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            "codes=$product");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        
        if($this->startsWith($curl_response,'<?xml')){
            $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));
            
            $this->transactionBegin();
            
            foreach($xml->product as $product){
                
                //BRAND. Y CAT.
           /*     $cat = $this->convertCategoryClute($product->productGroupValue);
                $brand =  $this->convertBrand($product->brandName);
                
                $sqlDel = "delete rel from ".$this->prefix."_term_relationships rel where rel.object_id='".$postid."' and (SELECT trim(tax.taxonomy) from ".$this->prefix."_term_taxonomy tax WHERE tax.term_taxonomy_id=rel.term_taxonomy_id  ) IN ('product_cat','pwb-brand')";
                
                //agregar brand y cat
                $sqlCat = "insert into ".$this->prefix."_term_relationships (object_id, term_taxonomy_id, term_order) VALUES (".$postid.",".$cat.", 0)";
                $sqlBrand = "insert into ".$this->prefix."_term_relationships (object_id, term_taxonomy_id, term_order) VALUES (".$postid.",".$brand.", 0)";
                
                //echo print_r($product, true);
                $result = $this->db->query($sqlDel);
                if($result) $result = $this->db->query($sqlCat);
                if($result) $result = $this->db->query($sqlBrand);
                */
                $var = $this->setToVariableProduct($postid);
                if(!$var){
                    $this->transactionEndForceRollback(); return false;
                }
                //actualizar postmeta attribute
                $this->insertPostMetaOnlyIfNotExists($postid, '_product_attributes', 'a:1:{s:9:"pa_modelo";a:6:{s:4:"name";s:9:"pa_modelo";s:5:"value";s:0:"";s:8:"position";s:1:"0";s:10:"is_visible";s:1:"1";s:12:"is_variation";s:1:"1";s:11:"is_taxonomy";s:1:"1";}}');
                $this->insertPostMeta($postid, '_download_limit', '-1', $verifyAlreadyExists);
                
                //CREAR MODELO
                $taxonomy = $this->existsTaxonomy(strtolower($product->value), 'pa_modelo');
                if($taxonomy==false){
                    $modelo = array( 'name' => html_entity_decode($product->name) ,  //modelo debe estar creado como atributo manualmente
                        'slug' => strtolower($product->value),
                        'term_group' => '0');
                    if(!$this->db->insert('terms', $modelo)){ $this->transactionEndForceRollback(); return false;}
                    $insert_id = $this->db->insert_id();
                    
                    
                    $taxonomy = array( 'term_id' => $insert_id ,  //modelo debe estar creado como atributo manualmente
                        'taxonomy' => 'pa_modelo',
                        'parent' => '0',
                        'count' => '1');
                    if(!$this->db->insert('term_taxonomy', $taxonomy)){ $this->transactionEndForceRollback(); return false;}
                    $taxonomy = $this->db->insert_id();
                }
                
                //ASOCIAR A PRODUCTO PRINCIPAL
                $taxonomy = array( 'object_id' => $postid ,  //modelo debe estar creado como atributo manualmente
                    'term_taxonomy_id' => $taxonomy,
                    'term_order' => '0');
                if(!$this->db->insert('term_relationships', $taxonomy)){ $this->transactionEndForceRollback(); return false;}
                
                
                //CREAR VARIACION
                $variacion = array( 'post_author' =>'1' ,
                    'post_date' => date('Y-m-d H:i:s'),
                    'post_date_gmt' => gmdate('Y-m-d H:i:s'),
                    'post_content' => '' ,
                    'post_title' => html_entity_decode($product->name) ,
                    'post_excerpt' => html_entity_decode($product->name),
                    'post_status' => 'publish',
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_password' => '',
                    'post_name' => html_entity_decode($product->name),
                    'to_ping' => '',
                    'pinged' => '',
                    'post_modified' => date('Y-m-d H:i:s'),
                    'post_modified_gmt' => gmdate('Y-m-d H:i:s'),
                    'post_content_filtered' => '',
                    'post_parent' => $postid,
                    'guid' => '',
                    'menu_order' => '1',
                    'post_type' => 'product_variation',
                    'post_mime_type' => '',
                    'comment_count' => '0' );
                if(!$this->db->insert('posts', $variacion)){ $this->transactionEndForceRollback(); return false;}
                
                $insert_id = $this->db->insert_id();
                $verifyAlreadyExists = false;
                //INSERTAR INFO EN VARIACION
                $this->insertPostMeta($insert_id, '_variation_description', html_entity_decode($product->name), $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, 'total_sales', '0', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_tax_status', 'taxable', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_tax_class', 'parent', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_manage_stock', 'yes', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_backorders', 'no', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_sold_individually', 'no', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_virtual', 'no', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_downloadable', 'no', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_download_limit', '-1', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_download_expiry', '-1', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_stock', $product->warehouseStock, $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_stock_status',  $product->warehouseStock >0 ? 'instock' : 'outofstock', $verifyAlreadyExists);
                if($product->warehouseStock >0){
                    $this->insertPostMeta($postid, '_stock_status', 'instock', true);
                    
                    $sqlDelOutofStock = "delete rel from ".$this->prefix."_term_relationships rel where rel.object_id='".$postid."' and (SELECT trim(tt.slug) from ".$this->prefix."_term_taxonomy tax inner join ".$this->prefix."_terms tt on tt.term_id=tax.term_id WHERE tax.term_taxonomy_id=rel.term_taxonomy_id  ) IN ('outofstock')";
                    //echo print_r($product, true);
                    $result = $this->db->query($sqlDelOutofStock);
                    
                }
                $this->insertPostMeta($insert_id, '_wc_average_rating', '0', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_wc_review_count', '0', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, 'attribute_pa_modelo',strtolower($product->value), $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_product_version', '1.0', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_sku', $product->value, $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_regular_price', round($product->priceListPEN*1.18,2), $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_weight', '0.00', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_length', '0.00', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_width', '0.00', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_height', '0.00', $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_price', round($product->priceListPEN*1.18,2), $verifyAlreadyExists);
                $this->insertPostMeta($insert_id, '_thumbnail_id', '', $verifyAlreadyExists);
                

		//unidad de medida para el post padre
	        $postParent = $postid;
                $caja = $product->caja;
		$step = $this->getPostMeta($postParent, '_wpbo_step');


		if($step==null || $step!=$caja ){
			$this->insertPostMeta($postParent, '_wpbo_override',  'on', 1);
			$this->insertPostMeta($postParent, '_wpbo_deactive',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_minimum',  $caja, 1);
			$this->insertPostMeta($postParent, '_wpbo_maximum',  1000000, 1);
			$this->insertPostMeta($postParent, '_wpbo_minimum_oos',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_maximum_oos',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_step',  $caja, 1);
			
			$this->log_message_text($username,'Producto '. $product->value .' ' . $product->name .' actualizado a unidad mínima: '. $caja);
//LOG de unidades por caja
		}

  		

//LOG de producto nuevo
				$this->log_message_text($username,'Producto nuevo insertado '. $product->value .' ' . $product->name);
                //insertar o actualizar wp_options
                $queryOptions =	$this->db->get_where('options', array('option_name'=> '_transient_wc_product_children_'.$postid), 1);
                $exists = $queryOptions->num_rows();
                $arrOption = array("all" => array(), "visible" => array());
                if($exists>0){ $arrOption = unserialize($queryOptions->row()->option_value); }
                $arrOption['all'][]=$insert_id;
                $arrOption['visible'][]=$insert_id;
                $sqlOptions = "INSERT INTO `".$this->prefix."_options` (`option_name`, `option_value`, `autoload`) VALUES ('_transient_wc_product_children_".$postid."', '".serialize($arrOption)."', 'no') ON DUPLICATE KEY UPDATE `option_name` = VALUES(`option_name`), `option_value` = VALUES(`option_value`), `autoload` = VALUES(`autoload`)";
                $result = $this->db->query($sqlOptions);
                if(!$result){ $this->transactionEndForceRollback(); return false; }
                
                break;
            }
            $commit = $this->transactionEnd();
            if(!$commit){
                $this->transactionEndForceRollback();
                return false;
            }
            
            
        }else{
            return false;
        }
        
        $sqlDelOutofStock = "delete from ".$this->prefix."_term_relationships where term_taxonomy_id IN (9) AND object_id IN (select p.id from ".$this->prefix."_posts p where post_type='product')";
        $result = $this->db->query($sqlDelOutofStock);
        
        
        return true;
    }
    
    
    function syncroProducts($url, $username, $password, $warehouseId, $pricelistId, $orgId, $posId){
        
        //products
        $sql = "select distinct p.post_parent, p.id, pm.meta_value from ".$this->prefix."_posts p
inner join ".$this->prefix."_postmeta pm on pm.post_id=p.id and pm.meta_key='_sku' and COALESCE(pm.meta_value,'')<>''
left join ".$this->prefix."_postmeta pu on pu.post_id=p.id and pu.meta_key='_price_update_at' and COALESCE(pu.meta_value,'')<>''
where p.post_type='product' OR p.post_type='product_variation' order by COALESCE(pu.meta_value,'2020-01-01') asc";
        
        $query = $this->db->query($sql);
        $arrobj = $query->result_array();
        $arrstr = '';
        
        foreach($arrobj as $res){
            $arrstr = $arrstr.'|'.$res['meta_value'];
        }
        $map = array();
        $mapParent = array();
        foreach ($arrobj as $res) {
            $map[''.$res['meta_value']] = $res['id'];
            $mapParent[''.$res['meta_value']] = $res['post_parent'];
        }

$this->log_message_text($username, 'Codigos actualizados desde ERP: '.$arrstr);
        
        $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=getStockAndPriceStoreFull&warehouseId='.$warehouseId.'&pricelistId='.$pricelistId;
        
        
        $curl = curl_init($url_ws);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,
            "codes=$arrstr");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        curl_close($curl);
        
        if($this->startsWith($curl_response,'<?xml')){
            
            $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));
            //echo $arrstr;
            foreach($xml->product as $product){
                //SOLO ACTUALIZA EL STOCK
               // echo "<br>";
                $post_id = $map[''.$product->value];
                //error_log(">>".$product->value);
                $this->insertPostMeta($post_id, '_stock', $product->warehouseStock, 1);
                $this->insertPostMeta($post_id, '_stock_status', $product->warehouseStock >0 ? 'instock' : 'outofstock', 1);
                $this->insertPostMeta($post_id, '_regular_price', round($product->priceListPEN*1.18,2), 1);
                $this->insertPostMeta($post_id, '_price', round($product->priceListPEN*1.18,2), 1);
                $this->insertPostMeta($post_id, '_price_update_at', date('Y-m-d H:i:s'), 1);
				
				/*$this->log_message_text($username,'Producto '. $product->value .' ' . $product->name .'actualizado a stock: '. $product->warehouseStock .' y precio:'. round($product->priceListPEN*1.18,2));*/
//LOG actualiza stock y precio
       
  		//unidad de medida para el post padre
	        $postParent = $mapParent[''.$product->value];
                $caja = $product->caja;
		$step = $this->getPostMeta($postParent, '_wpbo_step');
		if($step==null || $step!=$caja ){
			$this->insertPostMeta($postParent, '_wpbo_override',  'on', 1);
			$this->insertPostMeta($postParent, '_wpbo_deactive',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_minimum',  $caja, 1);
			$this->insertPostMeta($postParent, '_wpbo_maximum',  1000000, 1);
			$this->insertPostMeta($postParent, '_wpbo_minimum_oos',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_maximum_oos',  null, 1);
			$this->insertPostMeta($postParent, '_wpbo_step',  $caja, 1);
			
			$this->log_message_text($username,'Producto '. $product->value .' ' . $product->name .' actualizado a unidad mínima: '. $caja);
//LOG actualiza cantidad por caja
		}



            }
            
            
            
        }else{
            return false;
        }
        
        
        
        $sqlDelOutofStock = "delete from ".$this->prefix."_term_relationships where term_taxonomy_id IN (9) AND object_id IN (select p.id from ".$this->prefix."_posts p where post_type='product')";
        $result = $this->db->query($sqlDelOutofStock);
        
        
        $sqlUpdate1 = "UPDATE ".$this->prefix."_wc_product_meta_lookup lookup_table INNER JOIN (select p.post_parent as product_id, min(meta_value) as min_price, max(meta_value) as max_price from ".$this->prefix."_posts p inner join ".$this->prefix."_postmeta pm on p.id=pm.post_id where pm.meta_key like '_regular_price%' and p.post_parent<>'' group by p.post_parent) as source on source.product_id = lookup_table.product_id SET lookup_table.min_price = source.min_price, lookup_table.max_price = source.max_price";
        $sqlUpdate2 = "UPDATE ".$this->prefix."_wc_product_meta_lookup lookup_table INNER JOIN (select p.id as product_id, min(meta_value) as min_price, max(meta_value) as max_price from ".$this->prefix."_posts p inner join ".$this->prefix."_postmeta pm on p.id=pm.post_id where pm.meta_key like '_regular_price%' and p.post_parent<>'' group by p.id) as source on source.product_id = lookup_table.product_id SET lookup_table.min_price = source.min_price, lookup_table.max_price = source.max_price";
        //$sqlUpdate2 = "UPDATE ".$this->prefix."_wc_product_meta_lookup lookup_table LEFT JOIN ".$this->prefix."_postmeta meta1 ON lookup_table.product_id = meta1.post_id AND meta1.meta_key = '_manage_stock' LEFT JOIN ".$this->prefix."_postmeta meta2 ON lookup_table.product_id = meta2.post_id AND meta2.meta_key = '_stock' SET lookup_table.stock_quantity = meta2.meta_value WHERE meta1.meta_value = 'yes'";
        $sqlDelete2= "delete from ".$this->prefix."_options where option_name like '_transient_wc_var_prices_%'";
        
        $this->db->query($sqlUpdate1);
        $this->db->query($sqlUpdate2);
        $this->db->query($sqlDelete2);
        
        
        return true;
    }
    
    
    
    
    function desyncroSales($url, $username, $password, $warehouseId, $pricelistId, $orgId, $posId, $orderId){
        
        
        //error_log($xml->asXML());
        $this->db->select('postmeta.meta_value');
        $qm = $this->db->get_where('postmeta', array('post_id' => $orderId, 'meta_key' => 'erp_syncro'));
        foreach (($qm->result()) as $rowMeta) {
            if($rowMeta->meta_value == 'OK')//ya esta procesado
                return true;
        }
       
        $this->db->select('postmeta.meta_key, postmeta.meta_value');
        $qm = $this->db->get_where('postmeta', array('post_id' => $orderId));
        
        $_payment_method = ''; $_billing_first_name=''; $_billing_last_name=''; $_billing_address_1='';
        $_billing_email=''; $_order_total=''; $_f_isinvoice=''; $_f_dni=''; $_f_ruc=''; $_f_razon_social=''; $_f_region=''; $_f_provincia=''; $_f_distrito='';
        $_f_phone=''; $_f_phone2=''; $_f_f_region=''; $_f_f_provincia=''; $_f_f_distrito=''; $_f_tdoc='';
        $_f_address1='';$customid=''; $_shippingagency=''; $_order_tax='';
        
	$shippingCityCode='';
        $billingCityCode='';
        foreach (($qm->result()) as $rowMeta) {
            
            if($rowMeta->meta_key == '_payment_method') $_payment_method = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_billing_first_name') $_billing_first_name = $rowMeta->meta_value;
          //  if($rowMeta->meta_key == '_billing_last_name') $_billing_last_name = $rowMeta->meta_value;
            if($rowMeta->meta_key == '_billing_address_1') $_billing_address_1 = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_billing_email') $_billing_email = $rowMeta->meta_value;
            if($rowMeta->meta_key == '_billing_phone') $_f_phone = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_order_total') $_order_total = $rowMeta->meta_value;
            if($rowMeta->meta_key == '_order_tax') $_order_tax = $rowMeta->meta_value;
            
            
            if($rowMeta->meta_key == '_billing_department'){
                $_f_region = $this->Ubigeo->getDepartment($rowMeta->meta_value);
            }
            if($rowMeta->meta_key == '_billing_city'){
                $_f_provincia = $this->Ubigeo->getProvince(explode('-', $rowMeta->meta_value)[0]);
		$billingCityCode=$rowMeta->meta_value;

            }
            if($rowMeta->meta_key == '_billing_district'){
                $_f_distrito = $this->Ubigeo->getDistrict($rowMeta->meta_value);
            }
            
            if($rowMeta->meta_key == '_billing_wooccm10') $_f_dni = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_billing_wooccm9'){
                $_f_tdoc = $rowMeta->meta_value;//DNI CE PASAPORTE OTROS
            }
            
            if($rowMeta->meta_key == '_additional_wooccm0'){//Boleta o Factura
                if($rowMeta->meta_value=="Boleta")
                    $_f_isinvoice = '0';
                    else
                        $_f_isinvoice = '1';
            }
            if($rowMeta->meta_key == '_order_key') $customid = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_additional_wooccm2') $_f_ruc = $rowMeta->meta_value;
            if($rowMeta->meta_key == '_additional_wooccm1') $_f_razon_social = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_shipping_calle') $_f_address1 = $rowMeta->meta_value;
            
            if($rowMeta->meta_key == '_shipping_department'){
                $_f_f_region = $this->Ubigeo->getDepartment($rowMeta->meta_value);
            }
            if($rowMeta->meta_key == '_shipping_city'){
                $_f_f_provincia = $this->Ubigeo->getProvince(explode('-', $rowMeta->meta_value)[0]);
		$shippingCityCode=$rowMeta->meta_value;
            }
            if($rowMeta->meta_key == '_shipping_district'){
                $_f_f_distrito = $this->Ubigeo->getDistrict($rowMeta->meta_value);
            }
            
            if($rowMeta->meta_key == 'shipping_shipperagency') $_shippingagency = $rowMeta->meta_value;
            
        }
        
        
        
        //AHORA DATOS DE ORDERLINE
        $this->db->select('order_item_id');
        $qol = $this->db->get_where('woocommerce_order_items', array('order_id' => $orderId, 'order_item_type' => 'line_item'));
       
        $arrLines = array();
        $_order_total = 0;

        foreach (($qol->result()) as $rowOrderline) {
             
            $this->db->select('meta_key, meta_value');
            $qolm = $this->db->get_where('woocommerce_order_itemmeta', array('order_item_id' => $rowOrderline->order_item_id));
            $isvariation = false;
            foreach (($qolm->result()) as $rowOLMeta) {
                
                if($rowOLMeta->meta_key == '_qty') $_qty = $rowOLMeta->meta_value;
                if($rowOLMeta->meta_key == '_product_id' && $isvariation==false) $_product_id = $rowOLMeta->meta_value;
                if($rowOLMeta->meta_key == '_variation_id' && $rowOLMeta->meta_value!=null && $rowOLMeta->meta_value!='' && $rowOLMeta->meta_value!='0'){
                    $_product_id = $rowOLMeta->meta_value;
                    $isvariation = true;
                }
                if($rowOLMeta->meta_key == '_line_subtotal'){ $_line_subtotal = $rowOLMeta->meta_value; $_order_total = $_order_total + $rowOLMeta->meta_value; }
                if($rowOLMeta->meta_key == '_line_subtotal_tax'){ $_line_subtotal_tax = $rowOLMeta->meta_value; $_order_total = $_order_total + $rowOLMeta->meta_value; }
                
                
            }
            
            //encontrar que producto es el _product_id en prime
            $this->db->select('meta_value');
            $qprod = $this->db->get_where('postmeta', array('post_id' => $_product_id  , 'meta_key' => '_sku'));
            if($qprod->num_rows()==0){//error
                return false;
            }
            $arrLines[] = array('qty' => $_qty, 'product_id' => $qprod->row()->meta_value, 'subtotal' => $_line_subtotal, 'tax' => $_line_subtotal_tax, 'subtotalrp' => $_line_subtotal );
            
        }
        
        //SEKUR NO USA SHIPPING
        /*$this->db->select('order_item_id');
         $qol = $this->db->get_where('woocommerce_order_items', array('order_id' => $orderId, 'order_item_type' => 'shipping'));
         
         foreach (($qol->result()) as $rowOrderline) {
         $this->db->select('meta_key, meta_value');
         $qolm = $this->db->get_where('woocommerce_order_itemmeta', array('order_item_id' => $rowOrderline->order_item_id));
         
         foreach (($qolm->result()) as $rowOLMeta) {
         
         if($rowOLMeta->meta_key == 'cost'){ $_line_subtotal = $rowOLMeta->meta_value; $_order_total = $_order_total + $rowOLMeta->meta_value; }
         
         }
         if($_line_subtotal!=0)
         $arrLines[] = array('qty' => 1, 'product_id' => '99999', 'subtotal' => $_line_subtotal, 'tax' => 0, 'subtotalrp' => $_line_subtotal);
         
         
         }
         */
         
         //SEKUR NO USA CUPONES
         /*
          $this->db->select('order_item_id');
          $qol = $this->db->get_where('woocommerce_order_items', array('order_id' => $orderId, 'order_item_type' => 'coupon'));
          
          foreach (($qol->result()) as $rowOrderline) {
          $this->db->select('meta_key, meta_value');
          $qolm = $this->db->get_where('woocommerce_order_itemmeta', array('order_item_id' => $rowOrderline->order_item_id));
          
          foreach (($qolm->result()) as $rowOLMeta) {
          
          if($rowOLMeta->meta_key == 'discount_amount'){ $_line_subtotal = $rowOLMeta->meta_value; $_order_total = $_order_total - $rowOLMeta->meta_value; }
          
          }
          $al=0;
          
          while($_line_subtotal>0 && $al<count($arrLines)){
          $line = $arrLines[$al];
          if($line['subtotal']>$_line_subtotal){
          $line['subtotal'] = $line['subtotal'] -  $_line_subtotal;
          $_line_subtotal = 0;
          }else{
          $_line_subtotal = $_line_subtotal - $line['subtotal'];
          $line['subtotal'] = 0;
          
          }
          $arrLines[$al] = $line;
          $al++;
          }
          
          }
          */
         
         //preparar xml
         $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><syncrocustomers></syncrocustomers>");
         
     
         $bpartner = $xml->addChild('bpartner');
         if($_f_isinvoice=='1'){
             $bpartner->addChild('fullname', $_f_razon_social);
             $bpartner->addChild('taxid', $_f_ruc);
             $bpartner->addChild('iscompany', '1');
             $bpartner->addChild('customerdoc', '2');//RUC
             
             $bpartner->addChild('address', $_billing_address_1);
             $bpartner->addChild('address2', '');
             
             $bpartner->addChild('cityid', $_f_provincia);
             $bpartner->addChild('countryid', '277');
             $bpartner->addChild('districtid', $_f_distrito);
             $bpartner->addChild('regionid', $_f_region);
             
         }else{
           //  $bpartner->addChild('fullname', $_billing_first_name.' '.$_billing_last_name);
	     $bpartner->addChild('fullname', $_billing_first_name);
             $bpartner->addChild('taxid', $_f_dni);
             $bpartner->addChild('iscompany', '0');
             
             $codetdoc = "0";
             if($_f_tdoc=="DNI"){
                 $codetdoc = "1";
             }else if($_f_tdoc=="CE"){
                 $codetdoc = "4";
             }else if($_f_tdoc=="PASAPORTE"){
                 $codetdoc = "3";
             }else if($_f_tdoc=="RUC"){
                 $codetdoc = "2";
             }else{//OTROS
                 $codetdoc = "0";
             }
             //DNI CE PASAPORTE OTROS
             $bpartner->addChild('customerdoc', $codetdoc);//DNI
             
             
             $bpartner->addChild('address', $_billing_address_1);
             $bpartner->addChild('address2', '');
             
             $bpartner->addChild('cityid', $_f_provincia);
             $bpartner->addChild('countryid', '277');
             $bpartner->addChild('districtid', $_f_distrito);
             $bpartner->addChild('regionid', $_f_region);
             
         }
         
         $bpartner->addChild('email', $_billing_email);
         $bpartner->addChild('phone', $_f_phone);
         $bpartner->addChild('phone2', $_f_phone2);
         
         $bpartner->addChild('dni', $_f_dni);
         
	 $codigoCiudad=$shippingCityCode;
       
	 if($_f_address1!=''){
		 $bpartner->addChild('addressship', $_f_address1);
		 $bpartner->addChild('addressship2', '');
		 
		 $bpartner->addChild('cityshipid', $_f_f_provincia);
		 $bpartner->addChild('countryshipid', '277');
		 $bpartner->addChild('districtshipid', $_f_f_distrito);
		 $bpartner->addChild('regionshipid', $_f_f_region);
         }else{
		 $bpartner->addChild('addressship', $_billing_address_1);
		 $bpartner->addChild('addressship2', '');
		 
		 $bpartner->addChild('cityshipid', $_f_provincia);
		 $bpartner->addChild('countryshipid', '277');
		 $bpartner->addChild('districtshipid', $_f_distrito);
		 $bpartner->addChild('regionshipid', $_f_region);
		 $codigoCiudad = $billingCityCode;
	 }
         
         $bpartner->addChild('firstname', $_billing_first_name);
         
         
         if (strpos($_billing_last_name, ' ') !== false) {
             $pieces = explode(' ', $_billing_last_name);
             $last_space_position = strrpos($_billing_last_name, ' ');
             $lastname = substr($_billing_last_name, 0, $last_space_position);
             $bpartner->addChild('lastname', $lastname);
             $last_word = array_pop($pieces);
             $bpartner->addChild('lastname2', $last_word);
         }else{
             $bpartner->addChild('lastname', $_billing_last_name);
             $bpartner->addChild('lastname2', '');
         }
         
         
         
         
         
         //enviar bpartners
         $xmlToSend = urlencode($xml->asXML());
         
         
         $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=registerPartner&organizationId='.$orgId;
         
         $curl = curl_init($url_ws);
         
         curl_setopt($curl, CURLOPT_POST, 1);
         curl_setopt($curl, CURLOPT_POSTFIELDS,
             "xml=$xmlToSend");
         curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
         $curl_response = curl_exec($curl);
         curl_close($curl);
         
         
         if($this->startsWith($curl_response,'<?xml')){
             $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));
            
             if($xml->result != null && $xml->result[0]['status']=="Error"){
                 $this->msgError = "Error sincronizando cliente ".$xml->result[0]['documentNo'];
                 return false;
             }
             
             $customerId ='';
             foreach($xml->bpartner as $partnerRec){
                 $customerId = $partnerRec->prime_id;
                 
             }
			 
			 if($_f_isinvoice=='1'){
			 $this->log_message_text($username,'Cliente insertado/actualizado '.$_f_ruc . ' razón social: '.$_f_razon_social .' con dirección' . $_billing_address_1 .' - '. $_f_provincia.' - '.$_f_distrito.' - '.$_f_region . ' email: '. $bpartner->$_billing_email  .' teléfono1: '. $bpartner->$_f_phone .' teléfono2:' . $bpartner->$_f_phone2);
			 }
			 else{
			 $this->log_message_text($username,'Cliente insertado/actualizado '.$_f_dni . ' nombre: '.$_billing_first_name.' '.$_billing_last_name .' con dirección' . $_billing_address_1 .' - '. $_f_provincia.' - '.$_f_distrito.' - '.$_f_region . ' email: '. $bpartner->$_billing_email  .' teléfono1: '. $bpartner->$_f_phone .' teléfono2:' . $bpartner->$_f_phone2);
			 }
//LOG insertado/actualizado nuevo cliente en ERP
             
             //AHORA ORDEN
             $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><syncrosales></syncrosales>");
             
             
             $sale = $xml->addChild('sale');
             
             $sale->addChild('date', date("d-m-Y", strtotime("now")));
             $sale->addChild('total', $_order_total);
             $sale->addChild('totaltax', $_order_tax);
             
             if($_f_isinvoice=='1')
                 $sale->addChild('doctype_id', "1");//factura
                 else
                     $sale->addChild('doctype_id', "2");//boleta
                     
                     $sale->addChild('customer_id', $customerId);
                     $sale->addChild('pricelistId', $pricelistId);
                     $sale->addChild('warehouseId', $warehouseId);
                     $sale->addChild('referenceno', $orderId);
                     $sale->addChild('isfromweb', "1");
                     /*  if($customid=='')
                      $sale->addChild('referenceno', $orderId);
                      else
                      $sale->addChild('referenceno', $customid);*/
                      $sale->addChild('paymentmethod', $_payment_method);
                      
                      $sale->addChild('invoiceAddress', $_billing_address_1);
		
                      
                	
			if($_f_address1!=''){
				$sale->addChild('shipmentAddress', $_f_address1);
			}else{
				$sale->addChild('shipmentAddress', $_billing_address_1);
			}
                      //convertir shipping agency FIX-ME

                     
                      if($codigoCiudad=='127-Lima')
                          $_shippingagency="Despacho directo (SÓLO PARA LIMA)";
                          else if($_shippingagency=='0' && $codigoCiudad=='35-Arequipa')
                              $_shippingagency ="COTINSA (Urb. César Vallejo - Calle 1 Mz. 2 Lt. 2)";
                              else if($_shippingagency=='1' && $codigoCiudad=='35-Arequipa')
                                  $_shippingagency = "MARVISUR (CALLE GARCI CARBAJAL NRO. 511 URB. IV CENTENARIO - AREQUIPA)";
                                 /* else if($_shippingagency=='3')
                                      $_shippingagency = "GRUPO JyH EIRL (Productos Peligrosos) (Av. Aurelio Garcia y Garcia Nro. 1580)";*/
                                      else if($_shippingagency=='0' && $codigoCiudad=='112-Trujillo')
                                          $_shippingagency = "EMTRAFESA (157, Av Túpac Amaru 185, Trujillo 13001)";
                                          /*else if($_shippingagency=='5')
                                              $_shippingagency = "OLTURSA (Av Ejercito 342, Trujillo 13001)";*/
                                              else if($_shippingagency=='1' && $codigoCiudad=='112-Trujillo')
                                                  $_shippingagency  = "GRAU LOGISTICA EXPRESS SAC (Productos Peligrosos) (Av América Sur 2104, Trujillo 13006 / Auxiliar Panamericana Nte. KM 561, Moche 13008)";
                                                  /*else if($_shippingagency=='7')
                                                      $_shippingagency = "ITTSA (Av. Ignacio Merino. Ex Campamento Graña, Av. F, Talara)";*/
                                                    else $_shippingagency="S/N";
                                                      
                                                      $sale->addChild('shippingagency', $_shippingagency);
                                                      
                                                      foreach ($arrLines as $rowi) {
                                                          
                                                          if($rowi['qty']==0) continue;
                                                          
                                                          $peritem = $rowi['subtotal']/$rowi['qty'];
                                                          $realprice = $rowi['subtotalrp']/$rowi['qty'];
                                                          $salei = $sale->addChild('saleitem');
                                                          $salei->addChild('product_id', $rowi['product_id']);
                                                          $salei->addChild('quantity', $rowi['qty']);
                                                          $salei->addChild('unit_price', $peritem);
                                                          $salei->addChild('tax', $rowi['subtotal']*0.18);
                                                          $salei->addChild('subtotal', $rowi['subtotal']);
                                                          $salei->addChild('real_unit_price', $realprice);
                                                          $salei->addChild('comment', '');
                                                      }
                                                      
                                                      //enviar sales
                                                      $xmlToSend = urlencode($xml->asXML());
                                                             
                                                      $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=registerOrder&organizationId='.$orgId.'&posId='.$posId;
                                                      $curl = curl_init($url_ws);
                                                      curl_setopt($curl, CURLOPT_POST, 1);
                                                      curl_setopt($curl, CURLOPT_POSTFIELDS,
                                                          "xml=$xmlToSend");
                                                      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                                                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                                                      $curl_response = curl_exec($curl);
                                                      curl_close($curl);
                               //LOG nuevo pedido de venta en ERP                       
                       
                                                      //ACTUALIZAR ORDEN
                                                      if($this->startsWith($curl_response,'<?xml')){
                                                          $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));

                                                          if($xml->result != null && $xml->result[0]['status']=="Error"){
      								$this->msgError = "Error sincronizando venta ".$xml->result[0]['documentNo'];
                                                              return false;
                                                          }

                                                          $documentno = ''; $prime_id='';
                                                          foreach($xml->sale as $saleRec){
                                                              $documentno = $saleRec->documentno;
                                                              $prime_id= $saleRec->prime_id;
                                                          }
                                                 
                                                          //agregar metavalue con physicaldocumentno
                                                          $this->insertPostMeta($orderId, 'erp_syncro', 'OK', 1);
                                                          $this->insertPostMeta($orderId, 'erp_documentno', $documentno, 1);
                                                          $this->insertPostMeta($orderId, '_prime_id', $prime_id, 1);
                                                                
														
                                                      }else{
                                                          return false;
                                                      }
                                                      
                                                      
         }else{
             return false;
         }
         
         
         return true;
         
    }
    
    
    function desyncroPayments($url, $username, $password, $warehouseId, $pricelistId, $orgId, $posId){
        
        $this->db->select('ID, post_date');
        $this->db->from($this->prefix.'_posts');
        $this->db->where($this->prefix.'_posts.post_type', 'shop_order');
        $this->db->where($this->prefix.'_posts.post_status', 'wc-processing');
        $this->db->where('COALESCE((select 1 from '.$this->prefix.'_postmeta where '.$this->prefix.'_postmeta.post_id='.$this->prefix.'_posts.ID and '.$this->prefix.'_postmeta.meta_key=\'erp_syncro_invoice\' and '.$this->prefix.'_postmeta.meta_value=\'OK\'),0)=', 0, false);
        
        $q = $this->db->get();
  				$this->log_message_text($username,'Ejecutando generación de invoices.');      
        $ret = true;
        foreach (($q->result()) as $row) {
            
            $orderId = $row->ID;
            
            $this->db->select('postmeta.meta_key, postmeta.meta_value');
            $qm = $this->db->get_where('postmeta', array('post_id' => $row->ID));
            
            $prime_id = ''; $_transaction_id=''; $_paid_date=''; $order_syncro=''; $_payment_method='';
            
            foreach (($qm->result()) as $rowMeta) {
                
                if($rowMeta->meta_key == '_transaction_id'){ $_transaction_id = $rowMeta->meta_value;}
                if($rowMeta->meta_key == '_paid_date') $_paid_date = $rowMeta->meta_value;
                if($rowMeta->meta_key == '_prime_id') $prime_id = $rowMeta->meta_value;
                if($rowMeta->meta_key == 'erp_syncro') $order_syncro = $rowMeta->meta_value;
                if($rowMeta->meta_key == '_payment_method') $_payment_method = $rowMeta->meta_value;
                
            }
            
            if($order_syncro!='OK') continue; //NO HAY SIDO SINCRONIZADO AL ERP
            if($_paid_date=='' || $_paid_date==null) continue; //AUN NO HA SIDO PAGADO, NO EVALUAR
            
            $url_ws = $url.'/ws/pe.com.unifiedgo.webservice.MobileServices?l='.$username.'&p='.$password.'&method=registerInvoice&primeId='.$prime_id.'&posId='.$posId.'&isebill=Y&reference='.$_transaction_id.'&pmethod='.$_payment_method;

            $curl = curl_init($url_ws);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,
                "paiddate=$_paid_date");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            $curl_response = curl_exec($curl);
            curl_close($curl);
            
  
            //ACTUALIZAR ORDEN
            if($this->startsWith($curl_response,'<?xml')){
                $xml = new SimpleXMLElement(str_replace('"UTF-16"', '"UTF-8"', $curl_response));
                
                if($xml->result != null && $xml->errorcode[0]=="1"){
                    
                    
                    //agregar metavalue con physicaldocumentno
                    $this->insertPostMeta($orderId, 'erp_syncro_invoice', 'OK', 1);
                    
//LOG pago y factura en ERP
				$this->log_message_text($username,'Pedido '.$orderId.'. Pago realizado en fecha '.$_paid_date);
                }
                else{
                    
                    $ret=false;
                }
                
            }else{
                $ret=false;
            }
        }
        
        //Cancelar pedidos no facturados y completados con mas de 15 min.
        $sql = "SELECT posts.id FROM ".$this->prefix."_posts as posts WHERE posts.post_type = 'shop_order' AND posts.post_status IN ('wc-pending') AND posts.post_date < NOW()-INTERVAL 15 MINUTE";
        
        $query = $this->db->query($sql);
        $arrobj = $query->result_array();
        
        foreach($arrobj as $res){
            $postid = $res['id'];
            $sqlUpdate = "update ".$this->prefix."_posts set post_status='wc-cancelled' where id=".$postid;
            $this->db->query($sqlUpdate);
        }
        
        
        
        return $ret;
    }
    
    
    public function transactionBegin(){
        $this->db->trans_begin();
    }
    
    public function transactionEnd(){
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }
    
    public function transactionEndForceRollback(){
        
        $this->db->trans_rollback();
        return false;
        
    }
}

?>
