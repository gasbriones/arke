<?php
/**
 * @copyright	Copyright (C) 2014 Holest Engineering www.holest.com.
 * @license		GNU General Public License version 2 or later
 */
defined('_JEXEC') or die;
ob_clean();
header('Content-Type: text/html; charset=UTF-8');

$plem_errors = "";

if(!defined('DS'))
	define('DS', DIRECTORY_SEPARATOR ); 

if(isset($_REQUEST['keep_alive'])){
   
   if($_REQUEST['keep_alive']){
	   ob_clean();	
	   die('keep alive : OK');
   }   
}

$vm_lang = isset($_REQUEST['edit_language']) ? $_REQUEST['edit_language'] : ( isset( $_COOKIE['pelm_edit_language'] ) ? $_COOKIE['pelm_edit_language'] : "");

if($vm_lang){
	JRequest::setVar('vmlang',$vm_lang);
}
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
					
VmConfig::loadConfig();
$vm_lang = VMLANG;

// Load the language file of com_virtuemart.
JFactory::getLanguage()->load('com_virtuemart');
if (!class_exists( 'calculationHelper' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'calculationh.php');
if (!class_exists( 'CurrencyDisplay' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'currencydisplay.php');
if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'models'.DS.'vendor.php');
if (!class_exists( 'VmImage' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'image.php');
if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
if (!class_exists( 'calculationHelper' )) require(JPATH_COMPONENT_SITE.DS.'helpers'.DS.'cart.php');
if (!class_exists( 'VirtueMartModelProduct' )){
   JLoader::import( 'product', JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart' . DS . 'models' );
}

if(!defined('PLEM_VM_RUN')){
   $v = explode(".",vmVersion::$RELEASE);
   if($v[0] > 2 || ($v[0] == 2 && $v[1] >= 9))
	define('PLEM_VM_RUN',3);
   else	
	define('PLEM_VM_RUN',2);
}


$limit = isset($_COOKIE['pelm_txtlimit']) ? $_COOKIE['pelm_txtlimit'] : 1000;
$page  = 1;
$sortColumn = "p.virtuemart_product_id";
$sortOrder  = "ASC";
$product_sku = '';
$product_name = '';
$product_manufacturer = '';
$product_category = '';
$product_in_stock = "";
$product_show = '0';

if(isset($_REQUEST['limit'])){
	$limit = $_REQUEST['limit'];
}

if(isset($_REQUEST['page'])){
	$page = $_REQUEST['page'];
}

if(isset($_REQUEST['product_sku'])){
	$product_sku = $_REQUEST['product_sku'];
}

if(isset($_REQUEST['product_name'])){
	$product_name = $_REQUEST['product_name'];
}

if(isset($_REQUEST['product_manufacturer'])){
	$product_manufacturer = $_REQUEST['product_manufacturer'];
}

if(isset($_REQUEST['product_category'])){
	$product_category = $_REQUEST['product_category'];
}

$product_in_stock_f = "";
if(isset($_REQUEST['product_in_stock'])){
	$product_in_stock = $_REQUEST['product_in_stock'];
	
	if(str_ireplace(array('and','0','1','2','3','4','5','6','7','8','9',' ','=','>','<','>=','<=','!='),'', $product_in_stock))
		$product_in_stock = '';
	
	
	if($product_in_stock){
		if(is_numeric($product_in_stock)){
			$product_in_stock_f = " = ".$product_in_stock;
		}else{
			$product_in_stock_f = str_ireplace("AND"," AND p.product_in_stock ",$product_in_stock);
		}
	}
}

if(isset($_REQUEST['product_show'])){
	$product_show = $_REQUEST['product_show'];
}	

if(isset($_REQUEST['sortColumn'])){
	$sortColumn = $_REQUEST['sortColumn'];
	if($sortColumn == "virtuemart_product_id") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_sku") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_name") $sortColumn = "pl.".$sortColumn;
	elseif($sortColumn == "categories") $sortColumn = "cl.category_name";
	elseif($sortColumn == "virtuemart_manufacturer_id") $sortColumn = "ml.mf_name";
	elseif($sortColumn == "product_price") $sortColumn = "pr_p.product_price";
	elseif($sortColumn == "product_sales_price") $sortColumn = "pr_p.".$sortColumn;
	elseif($sortColumn == "product_override_price") $sortColumn = "pr_p.".$sortColumn." * coalesce((pr_p.override) = 1,0)";
	elseif($sortColumn == "slug") $sortColumn = "pl.".$sortColumn;
	elseif($sortColumn == "product_in_stock") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_ordered") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "published") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_special") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_s_desc") $sortColumn = "pl.".$sortColumn;
	elseif($sortColumn == "product_weight") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_weight_uom") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_length") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_width") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_height") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_lwh_uom") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_packaging") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_unit") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "metarobot") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "metaauthor") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_url") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_gtin") $sortColumn = "p.".$sortColumn;
	elseif($sortColumn == "product_mpn") $sortColumn = "p.".$sortColumn;
	
	elseif($sortColumn == "metadesc") $sortColumn = "pl.".$sortColumn;
	elseif($sortColumn == "metakey") $sortColumn = "pl.".$sortColumn;
	elseif($sortColumn == "customtitle") $sortColumn = "pl.".$sortColumn;
	else {
		$sortColumn = "p.virtuemart_product_id";
	}
}

if(isset($_REQUEST['sortOrder'])){
	$sortOrder = $_REQUEST['sortOrder'];
}

global $calculator;
$calculator = calculationHelper::getInstance ();

$vm_languages  = array();
$_COOKIE['pelm_edit_language'] = $vm_lang;
foreach(VmConfig::get('active_languages',array()) as $lng){
   $vm_languages[$lng] = str_replace("-","_",strtolower($lng));
}

if(empty($vm_languages)){
  $L = explode("_",$vm_lang);
  $L[1] = strtoupper($L[1]);
  $L =implode("-",$L);
  $vm_languages[$L] = $vm_lang;
}

$productModel      = VmModel::getModel('Product');
$categoryModel     = VmModel::getModel('Category');
$manufacturerModel = VmModel::getModel('Manufacturer');
$customfieldModel  = VmModel::getModel ('Customfields');

$manufacturerCategoriesModel = VmModel::getModel('Manufacturercategories');
$user = JFactory::getUser();

$db = JFactory::getDBO();
/////////////////////////////////////


global $has_gtinmpn;
$has_gtinmpn = false;

$db->setQuery("SHOW COLUMNS FROM #__virtuemart_products WHERE Field LIKE 'product_gtin' OR  Field LIKE 'product_mpn';");
$pr_cols = $db->loadObjectList();
$has_gtinmpn = (count($pr_cols) == 2);
 

$hasCatfn = false;

$pref = $db->getPrefix();
$config = JFactory::getConfig();
$databaseName=$config->get('db');

if( substr($pref, strlen($pref) - 1) != "_"){
	$pref.= "_";
}

try{
	$db->setQuery("SHOW FUNCTION STATUS WHERE `Type` LIKE 'FUNCTION' AND Db Like '$databaseName' AND Name LIKE '".$pref."plem_product_in_cats'");
	$hasCatfn =  $db->loadObjectList();
	
	if(count($hasCatfn) == 0){
		$db->setQuery("
			CREATE FUNCTION `".$pref."plem_product_in_cats`(product_id int, cats text) RETURNS bit(1)
			BEGIN
			  DECLARE parent int;
			  DECLARE in_cat bit;
			  DECLARE ccount int;
			  SET parent = (SELECT p.product_parent_id FROM ".$pref."virtuemart_products as p WHERE p.virtuemart_product_id = product_id); 
			  WHILE parent > 0 DO
				SET product_id = parent;
				SET parent = (SELECT p.product_parent_id FROM ".$pref."virtuemart_products as p WHERE p.virtuemart_product_id = product_id); 
			  END WHILE;
			 
			  SET ccount = (SELECT count(*) FROM
							  ".$pref."virtuemart_product_categories as pc
							WHERE
							  pc.virtuemart_product_id = product_id
							AND  
							  LOCATE (concat(',',pc.virtuemart_category_id,','), concat(',',cats,',')) > 0);  
			  
			  IF ccount > 0 THEN SET in_cat = 1;
			  ELSE SET in_cat = 0;
			  END IF;

			  RETURN in_cat;
			END;
		");

		$db->query();

		$db->setQuery("SHOW FUNCTION STATUS WHERE `Type` LIKE 'FUNCTION' AND Db Like '$databaseName' AND Name LIKE '".$pref."plem_product_in_cats'");
		$hasCatfn =  $db->loadObjectList();
	}
}catch(Exception $ex){
//
}

/////////////////////////////////////
$q = "SELECT virtuemart_custom_id, custom_parent_id, custom_title, custom_value, field_type, is_cart_attribute FROM #__virtuemart_customs WHERE NOT custom_title LIKE 'COM_VIRTUEMART_%' AND published = 1";//" AND field_type IN ('S','I','B');";
$db->setQuery($q);	
$custom_fields = $db->loadAssocList('virtuemart_custom_id');

/////////////////////////////////////
function search_files($current_path, &$el) { 
    $dir = opendir($current_path); 
    
    while(false !== ( $file = readdir($dir)) ) { 
	
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($current_path . "/" . $file) ) { 
			    $item = new stdClass;
				$item->t = "d"; 
				$item->n = $file;
				$item->c    = array();
				$el->c[]    = $item;
				
			    search_files($current_path . DS . $file, $item); 
				
		    } else {
			    $match = true;
			    $ext = strtolower(end(explode('.', $file)));
				if(!in_array($ext,array("jpg","png","gif","bmp","jpeg")))
					continue;
					
				$item = new stdClass;
				$item->t = "f"; 
				$item->n = $file;
				$el->c[]    = $item;	
		    } 
        } 
    } 
    closedir($dir); 
	return true;
};

function SaveSettings(&$db,&$settings){
   
   $ssettings = $settings;
   if(!is_string($ssettings))
	$ssettings = json_encode($ssettings);
   
   $q = "UPDATE #__extensions SET params = '$ssettings' WHERE element = 'com_vmexcellikeinput';";
   $db->setQuery($q);	
   $db->query();

};

global $SETTINGS;
$SETTINGS = null;
if(isset($_REQUEST['save_settings'])){
   $store_settings = $_REQUEST['save_settings']; 
   $SETTINGS = json_decode($store_settings);
   $SETTINGS->surogates = array();
	
   SaveSettings($db, $SETTINGS);
   
}else{
   $q = "SELECT params FROM #__extensions WHERE element = 'com_vmexcellikeinput';";
   $db->setQuery($q);	
   $res = $db->loadObject();
   $SETTINGS = json_decode($res->params);
   
   if(isset($SETTINGS->surogates))
	$SETTINGS->surogates = (array)$SETTINGS->surogates;
      
   if($_SERVER['REQUEST_METHOD'] === 'GET'){
	   if(isset($SETTINGS->surogates)){
		if(!empty($SETTINGS->surogates)){
			$SETTINGS->surogates = array();
			SaveSettings($db, $SETTINGS);
		}
	   }	
   }
   
   if(!isset($SETTINGS->surogates))
	$SETTINGS->surogates = array();
}
//DEFAULTS//////////////////////////
if(!isset($SETTINGS))
	$SETTINGS = new stdClass();
	
if(!isset($SETTINGS->frozen_columns))
    $SETTINGS->frozen_columns = "2";
if(!is_numeric($SETTINGS->frozen_columns))
	$SETTINGS->frozen_columns = "2";
if(!isset($SETTINGS->allow_delete))	
	$SETTINGS->allow_delete   = 0;
if(!isset($SETTINGS->allow_add))	
	$SETTINGS->allow_add   = 1;	
if(!isset($SETTINGS->override_price))	
	$SETTINGS->override_price   = 1;	
if(!isset($SETTINGS->surogates))	
	$SETTINGS->surogates        = array();
if(!isset($SETTINGS->custom_import))
	$SETTINGS->custom_import    = 0;
if(!isset($SETTINGS->first_row_header))
	$SETTINGS->first_row_header = 1;
if(!isset($SETTINGS->custom_import_columns))
	$SETTINGS->custom_import_columns = array();	
if(!isset($SETTINGS->hidden_columns))
	$SETTINGS->hidden_columns = array();		
if(!isset($SETTINGS->show_prices))
	$SETTINGS->show_prices = 1;
if(!isset($SETTINGS->prices))
	$SETTINGS->prices = 1;	
if(!isset($SETTINGS->csv_separator))
	$SETTINGS->csv_separator = ',';
if(!isset($SETTINGS->german_numbers))
	$SETTINGS->german_numbers = 0;		

/////////////////////////////////////

//CHECK LANGUAGE TABLES
if($vm_lang != VMLANG){
	$q = "SELECT
			(SELECT count(*) FROM #__virtuemart_products_$vm_lang) as countPR,
			(SELECT count(*) FROM #__virtuemart_categories_$vm_lang) as countCT,
			(SELECT count(*) FROM #__virtuemart_manufacturercategories_$vm_lang) as countMN;";

	$db->setQuery($q);		
	$counts = $db->loadObject();		

	if($counts->countPR == 0){
		$q = "INSERT INTO #__virtuemart_products_".$vm_lang."(  
			   virtuemart_product_id
			  ,product_s_desc
			  ,product_desc
			  ,product_name
			  ,metadesc
			  ,metakey
			  ,customtitle
			  ,slug)
			SELECT 
			   virtuemart_product_id
			  ,product_s_desc
			  ,product_desc
			  ,product_name
			  ,metadesc
			  ,metakey
			  ,customtitle
			  ,slug
			FROM #__virtuemart_products_". VMLANG; 
		$db->setQuery($q);	
		$db->query();
	
	}

	if($counts->countCT == 0){
		$q = "INSERT INTO #__virtuemart_categories_".$vm_lang."(
				   virtuemart_category_id
				  ,category_name
				  ,category_description
				  ,metadesc
				  ,metakey
				  ,customtitle
				  ,slug
				) 
				SELECT 
				   virtuemart_category_id
				  ,category_name
				  ,category_description
				  ,metadesc
				  ,metakey
				  ,customtitle
				  ,slug
				FROM #__virtuemart_categories_". VMLANG;
		$db->setQuery($q);	
		$db->query();		
	}

	if($counts->countMN == 0){
		$q = "INSERT INTO #__virtuemart_manufacturers_".$vm_lang."(
				   virtuemart_manufacturer_id
				  ,mf_name
				  ,mf_email
				  ,mf_desc
				  ,mf_url
				  ,slug
				) 
				SELECT
				   virtuemart_manufacturer_id
				  ,mf_name
				  ,mf_email
				  ,mf_desc
				  ,mf_url
				  ,slug
				FROM #__virtuemart_manufacturers_". VMLANG;
		$db->setQuery($q);	
		$db->query();
	}
}


///////////////////////////////////////////////////////////////////////////////////////
if(isset($_REQUEST['P_CONTENT'])){

	if($_REQUEST['P_CONTENT'] == "set"){
		$json    = file_get_contents('php://input');
		$obj = json_decode($json);
		$content = $obj->product_desc;
		
		if(isset($_REQUEST["language"])){
			$lng = $_REQUEST["language"];
			if(!$lng)
				$lng = "en_gb";
		}
		
		$q = "UPDATE #__virtuemart_products_". $lng . " SET product_desc = '$content' WHERE virtuemart_product_id = ". $_REQUEST["virtuemart_product_id"];
		$db->setQuery($q);	
		$db->query();
	}
	
	if($_REQUEST['P_CONTENT'] == "get" || $_REQUEST['P_CONTENT'] == "set"){
		$lng = "en_gb";
		if(isset($_REQUEST["language"])){
			$lng = $_REQUEST["language"];
			if(!$lng)
				$lng = "en_gb";
		}
		$q = "SELECT product_desc FROM #__virtuemart_products_". $lng . " WHERE virtuemart_product_id = ". $_REQUEST["virtuemart_product_id"];
		$db->setQuery($q);	
		$cnt = $db->loadObject();	
		echo json_encode($cnt);
	}

	die;
}else if(isset($_REQUEST['P_IMAGES'])){
	//VmConfig::get('img_width',array());
	//VmConfig::get('img_height',array());

	if($_REQUEST['P_IMAGES'] == "set"){
	
		$json   = file_get_contents('php://input');
		$images = json_decode($json);
		
		$p_id = $_REQUEST["virtuemart_product_id"];
		//DELETE
		
		$IIDS   = array();
		foreach($images as $img){
			if($img->virtuemart_media_id)
				$IIDS[] = $img->virtuemart_media_id;
		}
		
		$f_q = "SELECT 
					virtuemart_media_id As virtuemart_media_id,
					count( virtuemart_product_id ) As repeats
				FROM #__virtuemart_product_medias 
					WHERE
						virtuemart_product_id = ".$p_id."
						".( count($IIDS) ? " AND NOT virtuemart_media_id IN (". implode(",", $IIDS ) .")" : " " )."
				GROUP BY virtuemart_media_id";
					
		$db->setQuery($f_q);
		$res = $db->loadObjectList();	

		if(count($res)){
			if(count($IIDS)){
				$del_q = "DELETE FROM #__virtuemart_product_medias WHERE virtuemart_product_id = ".$p_id." AND NOT virtuemart_media_id IN ('". implode(",", $IIDS ) ."')";
				$db->setQuery($del_q);
				$db->query();
			}
			
			$dels   = array();
			foreach($res as $r){
				if($r->repeats <  2 ){
					$dels[] = $r->virtuemart_media_id;
				}
			}
			
			if(count($dels)){
				$del_q = "DELETE FROM #__virtuemart_medias WHERE virtuemart_media_id IN ('". implode(",", $dels ) ."')";
				$db->setQuery($del_q);
				$db->query();
			}
		}
		
		foreach($images as $img){
			if($img->virtuemart_media_id){//UPDATE
				$u_q = "UPDATE #__virtuemart_medias
						SET
						  published              =  ". $img->published ."
						 ,file_is_product_image  =  ". $img->file_is_product_image ."
						 ,file_description       = '". $img->file_description ."'
						 ,file_meta              = '". $img->file_meta ."'
						 ,file_title             = '". $img->file_title ."'
						WHERE virtuemart_media_id = ". $img->virtuemart_media_id;
				$db->setQuery($u_q);
				$db->query();
				
				$u_q = "UPDATE #__virtuemart_product_medias
						SET
						  ordering              =  ". $img->ordering ."
						WHERE virtuemart_product_id = ".$p_id." AND virtuemart_media_id = ". $img->virtuemart_media_id;
				
				$db->setQuery($u_q);
				$db->query();
				
			}else{//ADD
				
				$img->file_name = str_ireplace(' ','_',$img->file_name);
				$imgpath = JPATH_SITE. DS . 'images' . DS . 'stories' . DS . 'virtuemart' . DS . 'product'. DS;
				
				//$ifp  = fopen($imgpath . DS . $img->file_name , "wb"); 
				$data = $img->file_url;
				$dind = stripos($data, ',');
				$data = substr($data, $dind + 1);
				
				$image = imagecreatefromstring(base64_decode($data));
				
				if(stripos($img->file_mimetype,'png') !== false || stripos($img->file_mimetype,'gif') !== false){
					imagealphablending($image, false);
					imagesavealpha($image, true);
					$images = imagecolorallocatealpha($image, 255, 255, 255, 127);
				}
				
				$width = imagesx($image);
				$height = imagesy($image);
				
				if(stripos($img->file_mimetype,'jpg') !== false || stripos($img->file_mimetype,'jpeg') !== false){
					imagejpeg($image, $imgpath . DS . $img->file_name );
				}elseif(stripos($img->file_mimetype,'gif') !== false){
					imagegif($image, $imgpath . DS . $img->file_name);
				}else{
					imagepng($image, $imgpath . DS . $img->file_name);
				}
				
				if($writen !== false){
					
					$twidth  = VmConfig::get('img_width',array());
					$theight = VmConfig::get('img_height',array());
					
					if(!$twidth && $theight){
						$twidth = $theight * ($width/$height);
					}elseif(!$theight && $twidth){
						$theight = $twidth * ($height/$width);
					}elseif(!$theight && !$twidth){
						$twidth  = 220;
						$theight = 220;
					}
						
					$thumb_image = imagecreatetruecolor($twidth , $theight);
					if(stripos($img->file_mimetype,'png') !== false  || stripos($img->file_mimetype,'gif') !== false){
						imagealphablending($thumb_image, false);
						imagesavealpha($thumb_image, true);
						$transparent = imagecolorallocatealpha($thumb_image, 255, 255, 255, 127);
						imagefilledrectangle($thumb_image, 0, 0, $twidth, $theight, $transparent);
					}	
					
					$scale_width  = 0;
					$scale_height = 0;
					$rat_i = $width  / $height;
					$rat_t = $twidth / $theight;
					
					if($rat_i > $rat_t){
						$scale_width  = $twidth * ( $height /  $theight);
						$scale_height = $height;
					}else{
						$scale_width  = $width;
						$scale_height = $theight * ( $width /  $twidth);
					}
					
					imagecopyresampled($thumb_image, $image, 0, 0, ($width - $scale_width)/2, ($height - $scale_height)/2, $twidth, $theight, $scale_width, $scale_height);
				
					$thumbname = explode(".",$img->file_name);
					$thumbname = $thumbname[0];
					
					if(stripos($img->file_mimetype,'jpg') !== false || stripos($img->file_mimetype,'jpeg') !== false){
						$thumbname = $thumbname . '_' . $twidth . 'x' . $theight . '.jpg';
						imagejpeg($thumb_image, $imgpath . DS . 'resized' . DS . $thumbname);
					}elseif(stripos($img->file_mimetype,'gif') !== false){
						$thumbname = $thumbname . '_' . $twidth . 'x' . $theight . '.gif';
						imagegif($thumb_image, $imgpath . DS . 'resized' . DS . $thumbname);
					}else{
					    $thumbname = $thumbname . '_' . $twidth . 'x' . $theight . '.png';
						imagepng($thumb_image, $imgpath . DS . 'resized' . DS . $thumbname);
					}
					
					$i_sql = "INSERT INTO #__virtuemart_medias( virtuemart_media_id,virtuemart_vendor_id,file_title,file_description,file_meta,file_mimetype,file_type,file_url,file_url_thumb,file_lang ,file_is_product_image,file_is_downloadable,file_is_forSale,file_params,shared,published,created_on,created_by,modified_on,modified_by,locked_on,locked_by) VALUES (
							   NULL
							  ,0
							  ,'". $img->file_name ."'
							  ,'". $img->file_description ."'
							  ,'". $img->file_meta ."'
							  ,'". $img->file_mimetype ."'
							  ,'product'
							  ,'". 'images/stories/virtuemart/product/'. $img->file_name ."'
							  ,'". 'images/stories/virtuemart/product/resized/'. $thumbname ."'
							  ,''
							  ,". $img->file_is_product_image ."
							  ,0
							  ,0
							  ,''
							  ,0
							  ,". $img->published ."
							  ,NOW()
							  ,". $user->id ."
							  ,NOW()
							  ,". $user->id ."
							  ,'0000-00-00 00:00:00'
							  ,0
							)";
							
					$db->setQuery($i_sql);
					$db->query();
					$mid = $db->insertid();		
					
					$i_sql = "INSERT INTO #__virtuemart_product_medias(id,virtuemart_product_id,virtuemart_media_id ,ordering) VALUES (
								   NULL
								  ,".$p_id."
								  ,".$mid."
								  ,".$img->ordering."
								)";
								
					$db->setQuery($i_sql);
					$db->query();
				}
			}
		}
	}
	
	if($_REQUEST['P_IMAGES'] == "get" || $_REQUEST['P_IMAGES'] == "set"){
	
		$pi_q = "SELECT 
				PM.virtuemart_product_id,
				PM.virtuemart_media_id,
				PM.ordering,
				M.file_mimetype,
				M.published,
				M.file_is_product_image,
				M.file_url,
				M.file_url_thumb,
				M.file_description,
				M.file_meta,
				M.file_title
				FROM 
				#__virtuemart_product_medias as PM
				LEFT JOIN
				#__virtuemart_medias As M on M.virtuemart_media_id = PM.virtuemart_media_id
				WHERE 
				" . ( (isset($_REQUEST['virtuemart_product_id']) && $_REQUEST['virtuemart_product_id']) ?  ("PM.virtuemart_product_id = ". $_REQUEST['virtuemart_product_id']) : "") . "
				AND (M.file_mimetype LIKE '%jpeg' || M.file_mimetype LIKE '%png'  || M.file_mimetype LIKE '%gif' || M.file_mimetype LIKE '%bmp')
				ORDER BY PM.ordering ASC
				";
		
		$db->setQuery($pi_q);	
		$res = $db->loadObjectList();
		echo json_encode($res);
	}
	
	die;
}else if(isset($_REQUEST['I_BROWSE'])){

	if($_REQUEST['I_BROWSE'] == "browse"){
		$fs = new stdClass;
		$fs->c = array(); 
		search_files(JPATH_SITE.DS.'images', $fs);
		echo json_encode($fs);
	}elseif($_REQUEST['I_BROWSE'] == "upload"){
	
	}
	
	die;
}




///////////////////////////////////////////////////////////////////////////////////////
$ManufacturerCategories = $manufacturerCategoriesModel->getManufacturerCategories(false,true);


$db->setQuery("SELECT
	c.virtuemart_category_id As virtuemart_category_id
	FROM 
	#__virtuemart_categories as c
	LEFT JOIN
	#__virtuemart_category_categories as cc on cc.category_child_id = c.virtuemart_category_id 
	WHERE coalesce(cc.category_parent_id,0) = 0 ORDER BY c.ordering;");		

	
$records = $db->loadObjectList();
$categories = array();
$cat_asoc = array();
global $catway_asoc, 
	   $catway_asoc_reverse;

$catway_asoc         = array();
$catway_asoc_reverse = array();

function fillCats(&$categoryModel,&$cats,&$acats, $recs, $preffix, $path){
	foreach($recs as $record){
		global $catway_asoc, $catway_asoc_reverse;
		
		$cat = new stdClass();
		$vm_cat = $categoryModel->getCategory($record->virtuemart_category_id, true);
		
		$cat->virtuemart_category_id = $vm_cat->virtuemart_category_id;
		$cat->category_name          = $vm_cat->category_name;
		$cat->category_path          = $preffix . $vm_cat->category_name;
		$cat->category_pathway       = ($path ? $path."/": "" ) . $vm_cat->category_name;
		
		$acats[intval($vm_cat->virtuemart_category_id)] = $cat->category_path;
		$catway_asoc[intval($vm_cat->virtuemart_category_id)] = $cat->category_pathway;
		$catway_asoc_reverse[ str_replace("  "," ", str_replace("  "," ", strtolower(trim($cat->category_pathway)))) ] = intval($vm_cat->virtuemart_category_id);
		$cats[] = $cat;
		
		if($vm_cat->haschildren){
			fillCats($categoryModel,$cats,$acats, $vm_cat->children, "-" . $preffix, $cat->category_pathway);
		}
	}
}; 

fillCats($categoryModel,$categories,$cat_asoc,$records,"","");



/*
foreach($records as $record){
	$cat = new stdClass();
	$vm_cat = $categoryModel->getCategory($record->virtuemart_category_id, false);
	$cat->virtuemart_category_id = $vm_cat->virtuemart_category_id;
	$cat->category_name          = $vm_cat->category_name;
	$cat->category_path          = $vm_cat->category_name;
	$par = $categoryModel->getParentCategory($cat->virtuemart_category_id);
	if($par){
		while($par->virtuemart_category_id){
			$cat->category_path = '-' . $cat->category_path;	
			$par = $categoryModel->getParentCategory($par->virtuemart_category_id);
			if(!$par)
				break;
		}
	}
	
	$cat_asoc[intval($vm_cat->virtuemart_category_id)] = $cat->category_path;
	
	$categories[] = $cat;
}
*/


$manCats = array();
$man_asoc = array();
if(!empty($ManufacturerCategories)){
	foreach($ManufacturerCategories as $mancat){

		$mc = new stdClass();
		$mc->virtuemart_manufacturercategories_id = $mancat->virtuemart_manufacturercategories_id;
		$mc->mf_category_name = $mancat->mf_category_name;
		
		
		JRequest::setVar('virtuemart_manufacturercategories_id', $mancat->virtuemart_manufacturercategories_id,'');
		
		$manufacturers     = array();
		$vm_manufacturers  = $manufacturerModel->getManufacturers(false,true);
		
		foreach($vm_manufacturers as $vm_man){
			$man = new stdClass();
			$man->virtuemart_manufacturer_id = $vm_man->virtuemart_manufacturer_id;
			$man->mf_name                    = $vm_man->mf_name; 
			
			$man_asoc[intval($vm_man->virtuemart_manufacturer_id)] = $vm_man->mf_name;
			
			$manufacturers[] = $man;
		}
		
		$mc->manufacturers = $manufacturers;
		
		$manCats[] = $mc;
		
	}
}else{
	$mc = new stdClass();
	$mc->virtuemart_manufacturercategories_id = "0";
	$mc->mf_category_name = "";
	$manufacturers     = array();
	$vm_manufacturers  = $manufacturerModel->getManufacturers(false,true);
	
	foreach($vm_manufacturers as $vm_man){
		$man = new stdClass();
		$man->virtuemart_manufacturer_id = $vm_man->virtuemart_manufacturer_id;
		$man->mf_name                    = $vm_man->mf_name; 
		
		$man_asoc[intval($vm_man->virtuemart_manufacturer_id)] = $vm_man->mf_name;
		
		$manufacturers[] = $man;
	}
	$mc->manufacturers = $manufacturers;
	$manCats[] = $mc;
}



//FIX//////////////////////////////////////////////////////////////////////////////////////////////////////
function fix_prices_table(&$db,$user_id,$pr_id = 0, $nochildren = true){
    if(!$pr_id)
		$pr_id = '0';
		
	$fixq =	"INSERT INTO #__virtuemart_product_prices(
			   virtuemart_product_price_id
			  ,virtuemart_product_id
			  ,virtuemart_shoppergroup_id
			  ,product_price
			  ,override
			  ,product_override_price
			  ,product_tax_id
			  ,product_discount_id
			  ,product_currency
			  ,product_price_publish_up
			  ,product_price_publish_down
			  ,price_quantity_start
			  ,price_quantity_end
			  ,created_on
			  ,created_by
			  ,modified_on
			  ,modified_by
			  ,locked_on
			  ,locked_by
			)
			SELECT 

			   0
			  ,pr.virtuemart_product_id
			  ,null
			  ,0
			  ,null
			  ,0
			  ,0
			  ,0
			  ,(SELECT vendor_currency FROM #__virtuemart_vendors LIMIT 0,1)
			  ,NULL
			  ,NULL
			  ,NULL
			  ,NULL
			  ,'0000-00-00 00:00:00'
			  ,0
			  ,CURRENT_DATE()
			  ,".$user_id."
			  ,'0000-00-00 00:00:00'
			  ,0

			FROM
			  #__virtuemart_products as pr
			LEFT JOIN
			  #__virtuemart_product_prices as pr_p on pr_p.virtuemart_product_id = pr.virtuemart_product_id 
			WHERE
			  "
			  .
			  ($nochildren ? "coalesce(pr.product_parent_id,0) = 0 AND " : " ")
			  .
			  "pr_p.virtuemart_product_price_id IS NULL
			  AND 
			  (".$pr_id." = 0 OR pr.virtuemart_product_id = ".$pr_id.") ";

	$db->setQuery($fixq);
	$db->query();
};


function clear_product_default_price(&$db,$pr_id){
    if(!$pr_id)
		return;
		
	$fixq =	"DELETE FROM #__virtuemart_product_prices
			  WHERE
				  virtuemart_product_id     = $pr_id
			  AND coalesce(product_price,0)              = 0
			  AND coalesce(product_override_price,0)     = 0
			  AND coalesce(virtuemart_shoppergroup_id,0) = 0
			  AND coalesce(price_quantity_start,0)       = 0
			  AND coalesce(price_quantity_end,0)         = 0;";
			  
	$db->setQuery($fixq);
	$db->query();
};

fix_prices_table($db,$user->id);

///////////////////////////////////////////////////////////////////////////////////////////////////////////

function vmel_addProduct(&$db,&$vm_languages,$user_id){
	
	$db->setQuery(
		"INSERT INTO #__virtuemart_products(
		   published, product_available_date, created_on  ,created_by  ,modified_on  ,modified_by  ,product_params 
		   ) VALUES (
		   1, CURRENT_DATE(), NOW() ,".$user_id." ,NOW()  ,".$user_id." ,'min_order_level=0|max_order_level=0|'
		   );"
	);
	$db->query();
	$id = $db->insertid();
	
	foreach($vm_languages as $lng => $db_suffix){
		$db->setQuery(
			"INSERT INTO #__virtuemart_products_".$db_suffix."(
			   virtuemart_product_id ,product_name  ,product_s_desc  ,product_desc ,metadesc ,metakey  ,customtitle ,slug
			) VALUES (
			   ". $id ."
			  ,''  ,''  ,''  ,''  ,''  ,''
			  ,concat('product-',". $id .")
			);"
		);
		$db->query();
	}
	
	fix_prices_table($db,$user_id,$id);

	return $id;
};

function jsnum($val){
	if($val)
		return $val;
	else
		return null;
}

function vmel_getProduct($pr_id,&$productModel,&$db,&$custom_fields,&$cat_asoc,&$man_asoc){
	 global $SETTINGS, $has_gtinmpn;
	 global $catway_asoc, $catway_asoc_reverse;
	 $forexport  = isset($_REQUEST["do_export"]);
	 $pr = $productModel->getProduct($pr_id,false,true,false); 
	 if(!$pr->virtuemart_product_id)	
		 return NULL;
	
	
	 $prod = new stdClass();
	 $prod->virtuemart_product_id      = $pr->virtuemart_product_id;
	 $prod->product_sku                = $pr->product_sku ? $pr->product_sku : '' ;
	 $prod->slug                       = $pr->slug ? $pr->slug : '';
	 $prod->virtuemart_manufacturer_id = $pr->virtuemart_manufacturer_id;
	 $prod->categories                 = $pr->categories;

	 if(PLEM_VM_RUN > 2)
	 $q = "SELECT virtuemart_customfield_id, virtuemart_custom_id, customfield_value as custom_value , customfield_price as custom_price FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = ".$prod->virtuemart_product_id." order by ordering;";
	 else 
	 $q = "SELECT virtuemart_customfield_id, virtuemart_custom_id, custom_value, custom_price FROM #__virtuemart_product_customfields WHERE virtuemart_product_id = ".$prod->virtuemart_product_id." order by ordering;";
	 
	 $db->setQuery($q);	
	 $product_custom_fields = $db->loadObjectList();
	 
     foreach($custom_fields as $cf){
	 
		$filed_name = "custom_field_".$cf['virtuemart_custom_id'];
		if($forexport){
			$filed_name = str_replace(array(" ","-",":",";","?",">","<","!","'",'"'),"_", strtolower($cf['custom_title'])).'_cf'.$cf['virtuemart_custom_id'];
		}
	
		$prod->{$filed_name} = null;
		
		foreach($product_custom_fields as $pcf){
			if($cf['virtuemart_custom_id'] == $pcf->virtuemart_custom_id){
				if($prod->{$filed_name} === null)
					$prod->{$filed_name} = "";
				
				if( $cf['is_cart_attribute'] )
					$prod->{$filed_name} .= (($prod->{$filed_name} ? ";" : "") . $pcf->custom_value .":".round($pcf->custom_price ,2));
				else
					$prod->{$filed_name} .= (($prod->{$filed_name} ? ";" : "") . $pcf->custom_value );
			
			}
		}
	 }
	 
	 //$productModel->addImages(array($pr));
	 
	 if($forexport){
	     if(isset($prod->virtuemart_manufacturer_id))  
			if(isset($man_asoc[intval($prod->virtuemart_manufacturer_id)]))
				$prod->manufacturer_name = $man_asoc[intval($prod->virtuemart_manufacturer_id)];
			else	
				$prod->manufacturer_name = "";
		 else	
			$prod->manufacturer_name = "";

		 $cnames = array();
		 foreach($prod->categories as $c){
			$cn = $catway_asoc[intval($c)];
			$cnames[] = $cn; 
		 }	
			
		 $prod->categories_names     = implode(",",$cnames);
		 
		 unset($prod->categories);
		 unset($prod->virtuemart_manufacturer_id);
		 
	 }
	 
	$prod->product_name               = $pr->product_name;
	$prod->product_in_stock           = $pr->product_in_stock;  
	$prod->product_ordered            = $pr->product_ordered;  
	if(PLEM_VM_RUN > 2)
		$prod->product_price              = $pr->prices["basePrice"];
	else
		$prod->product_price              = $pr->product_price;
	
	if(PLEM_VM_RUN > 2){
		$prod->product_override_price     = 0;
		if(isset($pr->prices["override"])){
			if(intval($pr->prices["override"]) !== 0)
				$prod->product_override_price = $pr->prices["product_override_price"];
		}
	}else
		$prod->product_override_price     = $pr->override !== 0 ? $pr->product_override_price : 0;
	
	$prod->product_sales_price        = $pr->prices["salesPrice"];  
	
	$prod->product_price              = jsnum($prod->product_price);
	$prod->product_override_price     = jsnum($prod->product_override_price);
	$prod->product_sales_price        = jsnum($prod->product_sales_price);
	
	$prod->published                  = $pr->published ? true : false;
	$prod->product_special            = $pr->product_special ? true : false;
	$prod->product_s_desc             = $pr->product_s_desc ;
	
	
	$prod->metadesc                   = $pr->metadesc ;
	$prod->metakey                    = $pr->metakey ;
	$prod->customtitle                = $pr->customtitle ;
	 
	$prod->product_weight       = $pr->product_weight;
	$prod->product_weight_uom   = $pr->product_weight_uom;
	$prod->product_length       = $pr->product_length;
	$prod->product_width        = $pr->product_width;
	$prod->product_height       = $pr->product_height;      
	$prod->product_lwh_uom      = $pr->product_lwh_uom; 
	$prod->product_packaging    = $pr->product_packaging;
	$prod->product_unit         = $pr->product_unit;
	
	$prod->metarobot            = $pr->metarobot;
    $prod->metaauthor			= $pr->metaauthor;
	
	$prod->product_url                = $pr->product_url;

	if($has_gtinmpn){
		$prod->product_gtin           = $pr->product_gtin;
		$prod->product_mpn            = $pr->product_mpn;
	}
	
	if(!$forexport){
		$prod->i_id  = $pr->virtuemart_product_id;
		$prod->c_id  = $pr->virtuemart_product_id;
		$prod->link           		= str_ireplace('/administrator','',JRoute::_($pr->link));
	    
		
		if($SETTINGS->prices){
			$prod->prices = array();
			$pric_sql = "SELECT 
							*
						 FROM 
						#__virtuemart_product_prices as vpp
						WHERE
						vpp.virtuemart_product_id = ". $prod->virtuemart_product_id ."
						ORDER BY vpp.price_quantity_start, vpp.price_quantity_end, vpp.virtuemart_shoppergroup_id, vpp.virtuemart_product_price_id ASC LIMIT 1,999";			
						
			$db->setQuery($pric_sql);			
			$allprices = $db->loadObjectList();
			
			if(!empty( $allprices )){
				foreach($allprices as $price){
					$pr->prices = (array)$price;
					$pr->prices = $productModel->getPrice($pr,1);
					$p = new stdClass;
					$p->pp_id           = $price->virtuemart_product_price_id;
					$p->sg_id           = $price->virtuemart_shoppergroup_id;
					$p->price           = $price->product_price;
					$p->price_override  = $price->override;
					$p->q_start         = $price->price_quantity_start;
					$p->q_end           = $price->price_quantity_end;
					$p->sales_price     = isset($pr->prices["salesPrice"]) ? ($pr->prices["salesPrice"]? $pr->prices["salesPrice"] : "") : "" ;
					
					$p->price          = jsnum($p->price);
					$p->price_override = jsnum($p->price_override);
					$p->sales_price    = jsnum($p->sales_price);  
					
					$prod->prices[]     = $p;
				}
			}
		}
	}
	
	unset($pr);
	return $prod;
}


///////////////////////////////////////////////////////////////////////////////////////

function Getfloat($str) { 
  global $SETTINGS;
  if($SETTINGS->german_numbers){
	  if(strstr($str, ".")) { 
		$str = str_replace(".", "", $str); // replace ',' with '.' 
	  }
	  $str = str_replace(",", ".", $str);  
  }else{
	  if(strstr($str, ",")) { 
		$str = str_replace(",", "", $str); // replace ',' with '.' 
	  }
  }
  return $str;
}; 

function default_val($val,$default){
   if($val === null)
     return $default;
   if(!isset($val))
     return $default;
   if(strlen($val) === 0)
     return $default;
   return $val;	 
};

function default_val_num($val,$default){
   if($val === null)
     return $default;
   if(!isset($val))
     return $default;
   if(strlen($val) === 0)
     return $default;
	 
   return Getfloat($val);	 
};

function dbnum($val){
	$val = default_val_num($val,"");
	if(!$val)
		return "NULL";
	else
		return "'".$val."'";
};


if(isset($_REQUEST['DO_UPDATE'])){
if($_REQUEST['DO_UPDATE'] == '1' && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST'){
	$json = file_get_contents('php://input');
	$tasks = json_decode($json);
	
	$res = array();
	$temp = '';
	
	foreach($tasks as $key => $task){
       $return_added = false;  
       $res_item = new stdClass();
	   $res_item->returned = new stdClass();
	   $res_item->returned->dg_index = $task->dg_index;
	   
	   $sKEY = "".$key;
	   if($sKEY[0] == 's'){
			if(isset($SETTINGS->surogates[$sKEY]))
				$key = $SETTINGS->surogates[$sKEY];
			else{	
				$key = vmel_addProduct($db,$vm_languages,$user->id);
				$SETTINGS->surogates[$sKEY] = $key;
				SaveSettings($db, $SETTINGS);
			}
			$return_added = true;
	   }
	   
	   $res_item->virtuemart_product_id = $key;
	   $res_item->success = true;
	   
	   if(isset($task->DO_DELETE)){
	     if($task->DO_DELETE === 'delete'){
		 
		    $tables = array();
			$tables[] = '#__virtuemart_product_categories';
			$tables[] = '#__virtuemart_product_customfields';
			
			if(PLEM_VM_RUN < 3) $tables[] = '#__virtuemart_product_downloads';
			$tables[] = '#__virtuemart_product_manufacturers';
			$tables[] = '#__virtuemart_product_medias';
			$tables[] = '#__virtuemart_product_prices';
			if(PLEM_VM_RUN < 3) $tables[] = '#__virtuemart_product_relations';
			$tables[] = '#__virtuemart_product_shoppergroups';
			$tables[] = '#__virtuemart_products';
			
		 
		    foreach($vm_languages as $lng => $db_suffix)
				$tables[] = ('#__virtuemart_products_'.$db_suffix);
            
			foreach($tables as $ind => $tbl){
				$db->setQuery("DELETE FROM ".$tbl." WHERE virtuemart_product_id = ".$key);
				$db->query();
			}
				
			$res[] = $res_item;
			continue;
		 }
	   }
	   
	   $upd_prop = array();
	   
	   $db->setQuery("SELECT * FROM #__virtuemart_products WHERE virtuemart_product_id = ".$key);
	   $prod_info = $db->loadObject();
	   
	   $arrtask = get_object_vars($task);
	   
	   foreach($arrtask as $pname => $pvalue ){
		   if($task->{$pname}){
			   if(is_string($task->{$pname}))
				   $task->{$pname} = addslashes($task->{$pname});
		   }
	   }
	   
	   if(isset($task->product_sku)) $upd_prop[] = " p.product_sku = '".$task->product_sku . "' ";
	   
	   if(isset($task->published)) $upd_prop[] = " p.published = '". ($task->published ? "1" : "0" ) . "' ";
	   if(isset($task->product_special)) $upd_prop[] = " p.product_special = '".($task->product_special ? "1" : "0") . "' ";
	   if(isset($task->product_in_stock)) $upd_prop[] = " p.product_in_stock = ".$task->product_in_stock  . " ";
	   if(isset($task->product_ordered)) $upd_prop[] = " p.product_ordered = ".$task->product_ordered  . " ";
	   
	   if(isset($task->product_url))  $upd_prop[]  = "p.product_url = '".$task->product_url."'";
	   
	   if($has_gtinmpn){
	   if(isset($task->product_gtin)) $upd_prop[]  = "p.product_gtin = '".$task->product_gtin."'";
	   if(isset($task->product_mpn))  $upd_prop[]  = "p.product_mpn = '".$task->product_mpn."'";
	   }
	   
	   if(isset($task->product_name)) $upd_prop[] = " pl.product_name    = '".$task->product_name  . "' ";
	   if(isset($task->product_s_desc)) $upd_prop[] = " pl.product_s_desc    = '".$task->product_s_desc  . "' ";
	   
	   
		if(isset($task->metadesc)) $upd_prop[] = " pl.metadesc = '".$task->metadesc  . "' ";
		if(isset($task->metakey)) $upd_prop[] = " pl.metakey = '".$task->metakey  . "' ";
		if(isset($task->customtitle)) $upd_prop[] = " pl.customtitle  = '".$task->customtitle  . "' ";
	   
	   
	   
	   if(isset($task->slug)) $upd_prop[] = " pl.slug            = '".$task->slug  . "' ";
	   
	   $any_price_set = false;
	   
	   if(!isset($task->product_sales_price) && isset($task->product_price)){ 
			$upd_prop[] = " pr_p.product_price = ". dbnum($task->product_price)  . " " ;
			$any_price_set = true;
	   }
	   
	   if(isset($task->product_override_price)){
	       if(!$task->product_override_price){
		    $upd_prop[] = " pr_p.product_override_price = null " ;
		    $upd_prop[] = " pr_p.override = 0 ";
		   }else{
			$upd_prop[] = " pr_p.product_override_price = ". dbnum($task->product_override_price)  . " " ;
			$upd_prop[] = " pr_p.override = " . $SETTINGS->override_price;
		   }
		   $any_price_set = true;
	   }
	   
	   if(isset($task->product_sales_price)){
			global $calculator;
			$pdata = array();
			$pdata["salesPrice"] = $task->product_sales_price;
			$product_price = $calculator->calculateCostprice ($key, $pdata);
			
			$upd_prop[] = " pr_p.product_price = ". dbnum($product_price) . " " ;
			$res_item->returned->product_price = jsnum($product_price);
			$any_price_set = true;
	   } 
	   
	   if($any_price_set && $prod_info->product_parent_id){
			fix_prices_table( $db, $user->id, $key, false); 	
	   }
	   
	   if(isset($task->product_weight)) $upd_prop[] = " p.product_weight = ".dbnum($task->product_weight) . " ";
	   if(isset($task->product_weight_uom)) $upd_prop[] = " p.product_weight_uom = '".$task->product_weight_uom . "' ";
	   if(isset($task->product_length)) $upd_prop[] = " p.product_length = ".dbnum($task->product_length) . " ";
	   if(isset($task->product_width)) $upd_prop[] = " p.product_width = ".dbnum($task->product_width) . " ";
	   if(isset($task->product_height)) $upd_prop[] = " p.product_height = ".dbnum($task->product_height) . " ";
	   if(isset($task->product_lwh_uom)) $upd_prop[] = " p.product_lwh_uom = '".$task->product_lwh_uom . "' ";
	   if(isset($task->metarobot)) $upd_prop[] = " p.metarobot = '".$task->metarobot . "' ";
	   if(isset($task->metaauthor)) $upd_prop[] = " p.metaauthor = '".$task->metaauthor . "' ";
	   
	   if(isset($task->product_packaging)) $upd_prop[] = " p.product_packaging = ".dbnum($task->product_packaging) . " ";
	   if(isset($task->product_unit)) $upd_prop[] = " p.product_unit = '".$task->product_unit . "' ";
	   
	   if(count($upd_prop)){
		   $u_query = "UPDATE 
						   #__virtuemart_products as p
						LEFT JOIN
						   #__virtuemart_product_prices as pr_p on pr_p.virtuemart_product_id = p.virtuemart_product_id AND (coalesce( pr_p.price_quantity_start,0) + coalesce(pr_p.price_quantity_end,0) = 0) AND NOT coalesce(pr_p.virtuemart_shoppergroup_id,0) > 0  
						LEFT JOIN
						   #__virtuemart_products_".$vm_lang." as pl on pl.virtuemart_product_id = p.virtuemart_product_id
						SET
						
						  ". implode(",",$upd_prop) ."
						  
						WHERE p.virtuemart_product_id = ".$key.";";
		  
		   $db->setQuery($u_query);
	       $res_item->success = $res_item->success && $db->query();
	   }
	   
	   if(isset($task->virtuemart_manufacturer_id)){
			if($task->virtuemart_manufacturer_id){
				$db->setQuery("SELECT count(*) as `exists` FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id = ".$key);
				if($db->loadObject()->exists){
					$db->setQuery("UPDATE #__virtuemart_product_manufacturers SET virtuemart_manufacturer_id = ".$task->virtuemart_manufacturer_id." WHERE virtuemart_product_id = ".$key);
				}else{
					$db->setQuery("INSERT INTO #__virtuemart_product_manufacturers(id ,virtuemart_product_id,virtuemart_manufacturer_id) VALUES (NULL,".$key.",".$task->virtuemart_manufacturer_id.")");
				}
				$res_item->success = $res_item->success && $db->query();
			}else{
				$db->setQuery("DELETE FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id = ".$key);
				$res_item->success = $res_item->success && $db->query();
			}
	   }
	   
	   if(isset($task->categories)){
	   
		    $db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id = ".$key. ( $task->categories ? " AND NOT virtuemart_category_id in (".$task->categories.")" : "" ));
			$db->query();
		    
            $db->setQuery("SELECT virtuemart_category_id FROM #__virtuemart_product_categories WHERE virtuemart_product_id = ".$key);
  			$cur_cats_o = $db->loadObjectList();
			$cur_cats = array();
			
			foreach($cur_cats_o as $cat)
			   $cur_cats[] = $cat->virtuemart_category_id;
			
			$cats = explode(",",$task->categories);
			
			foreach($cats as $c){
			  if(!in_array($c,$cur_cats)){
				$db->setQuery("INSERT INTO #__virtuemart_product_categories(id,virtuemart_product_id,virtuemart_category_id,ordering) VALUES (NULL,".$key.",".$c.",0)");
				$db->query();
			  }
			}
	   }
	   
	   foreach($custom_fields as $cf_id => $cf){
	     if(isset($task->{"custom_field_".$cf_id})){
		    if($task->{"custom_field_".$cf_id} === null || $task->{"custom_field_".$cf_id} === ''){
				$Q = "DELETE FROM #__virtuemart_product_customfields
					  WHERE
					   virtuemart_product_id = ".$key."
 					  AND
					   virtuemart_custom_id  = ".$cf_id;
				$db->setQuery($Q);	  
			    $res_item->success = $res_item->success && ($db->query() !== false);
			 }else{ 
				 $values = explode(";",$task->{"custom_field_".$cf_id});
				 $Q = "SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields
						WHERE
						   virtuemart_product_id = ".$key."
						AND
						   virtuemart_custom_id  = ".$cf_id;
				 
				 $db->setQuery($Q);
				 $existing = $db->loadObjectList();
				 
				 for($I = 0; $I < count($values); $I++){
					$value = explode(":",$values[$I]);
					$price = 'NULL';
					if(count($value)> 1){
						$price = $value[1];
						$value = $value[0];
					}else
						$value = $value[0];
						
					if(!$price && $price !== '0')					
						$price = 'NULL';
				  
					if($I < count($existing)){
						 
						 $Q = "UPDATE #__virtuemart_product_customfields
							SET
							   custom".(PLEM_VM_RUN > 2 ? "field" : "")."_value = '".$value."',
							   custom".(PLEM_VM_RUN > 2 ? "field" : "")."_price = ".dbnum($price)."
							WHERE
							   virtuemart_customfield_id = ".$existing[$I]->virtuemart_customfield_id;
						 
						 $db->setQuery($Q);
						 $res_item->success = $res_item->success && ($db->query() !== false);
						 
					}else{
						
						 $Q = "INSERT INTO #__virtuemart_product_customfields(
								   virtuemart_product_id, virtuemart_custom_id, custom".(PLEM_VM_RUN > 2 ? "field" : "")."_value ,custom".(PLEM_VM_RUN > 2 ? "field" : "")."_price
								) VALUES (".$key.",".$cf_id.",'".$value."',".$price.")"; 
						 $db->setQuery($Q);	
						 $res_item->success = $res_item->success && ($db->query() !== false);
						 
					}
				 }

				 for($I = count($values); $I < count($existing) ; $I++){
					$Q = "DELETE FROM #__virtuemart_product_customfields
						  WHERE
						   virtuemart_customfield_id = ".$existing[$I]->virtuemart_customfield_id;
						   
					$db->setQuery($Q);	  
					$res_item->success = $res_item->success && ($db->query() !== false);
				 }
			}	
		 }
	   }
	   
	   if(isset($task->prices)){
	      $prices_ids = array();
		  $cprices_add        = array();
		  foreach($task->prices as $cprice){
			 if( strpos($cprice->pp_id,'s') === false){
				$prices_ids[] = $cprice->pp_id;
			 }else
				$cprices_add[] = &$cprice;
		  }
		 
		  //DELETE
		  $del_sql = "SELECT 
						d.virtuemart_product_price_id
					  FROM 
						#__virtuemart_product_prices as d
					  WHERE
						d.virtuemart_product_id = ". $key ."
						" . (!empty($prices_ids) ?	(" AND NOT d.virtuemart_product_price_id IN (". implode(",",$prices_ids) .") ") : "") . "
					  ORDER BY d.virtuemart_product_price_id ASC LIMIT 1,999";
		  $db->setQuery($del_sql);	
		  $delete = $db->loadObjectList();	
		  if(!empty($delete)){
		    $del_ids = array(); 
		    foreach($delete as $d){
				$del_ids[] = $d->virtuemart_product_price_id;
			}
			
			$del_sql = "DELETE FROM #__virtuemart_product_prices
						WHERE 
						virtuemart_product_price_id IN (".implode(",",$del_ids).")";
		    $db->setQuery($del_sql);	
		    $db->query();	
		  }
		  
		  $psurog = array();
		  //ADD
		  $model_price = null;
		  if(!empty($cprices_add)){
			$db->setQuery("SELECT 
								*
							FROM 
							  #__virtuemart_product_prices
							WHERE
							  virtuemart_product_id = ".$key."
							ORDER BY virtuemart_product_price_id ASC  
							LIMIT 1 ");
							
			$model_price = $db->loadObject();
			$model_price = get_object_vars($model_price);
			foreach($model_price as $name => $value){
				if($value === null)
					$model_price[$name] = "NULL";
				else
					$model_price[$name] = "'".$model_price[$name]."'";
			}
			
			foreach($cprices_add as $addprice){
				$addprice->surogate = $addprice->pp_id;
				$model_price["virtuemart_product_price_id"] = 0;
				$ins_q = "INSERT INTO #__virtuemart_product_prices (" . implode(",",array_keys($model_price)) . ") VALUES (" . implode(",",array_values($model_price)) . ");";	
				$db->setQuery($ins_q);	
				$db->query();	
				$addprice->pp_id = $db->insertid();
				$psurog[$addprice->pp_id] = $addprice->surogate;
				
			}
		  }
		  
		  //UPDATE
		  foreach($task->prices as $updateprice){
			 $upd = array();
			 if(!$updateprice->price_override)
				$updateprice->price_override = 0;
				
			 if($updateprice->price_override > 0){
				$upd[] = " product_override_price = " . dbnum($updateprice->price_override);
				$upd[] = " override = " . $SETTINGS->override_price;
			 }else{
				$upd[] = " product_override_price = null " ;
				$upd[] = " override = 0 ";
			 }
			 
			 if($updateprice->sales_price && ($updateprice->lastset == "sales" || !$updateprice->price)){
				global $calculator;
				$pdata = array();
				$pdata["salesPrice"] = $updateprice->sales_price;
				$pprice = $calculator->calculateCostprice ($key, $pdata);
				$upd[] = " product_price = " . dbnum($pprice) . " ";
			 }else if($updateprice->price){
				$upd[] = " product_price = " . dbnum($updateprice->price) . " ";
			 }else{
				$upd[] = " product_price = null ";
			 }
			 
			 $upd_sql = "UPDATE #__virtuemart_product_prices
						 SET
							 virtuemart_shoppergroup_id = ".$updateprice->sg_id."
							,price_quantity_start       = ".($updateprice->q_start ? $updateprice->q_start : 0)."
							,price_quantity_end         = ".($updateprice->q_end ? $updateprice->q_end : 0)."
							,".implode(",", $upd )."
						 WHERE virtuemart_product_price_id = ". $updateprice->pp_id;
						 
			 $db->setQuery($upd_sql);	
			 $db->query();			 
		  }
		  
		  $ret_prices = array();
		  $pric_sql = "SELECT 
							*
						 FROM 
						#__virtuemart_product_prices as vpp
						WHERE
						vpp.virtuemart_product_id = ". $key ."
						ORDER BY vpp.price_quantity_start, vpp.price_quantity_end, vpp.virtuemart_shoppergroup_id, vpp.virtuemart_product_price_id ASC LIMIT 1,999";
						
		  $db->setQuery($pric_sql);			
		  $allprices = $db->loadObjectList();
			
		  if(!empty( $allprices )){
				$lpr = $productModel->getProduct($key,false,true,false);
				foreach($allprices as $price){
					$lpr->prices = (array)$price;
					$lpr->prices = $productModel->getPrice($lpr,1);
					
					$p = new stdClass;
					$p->pp_id           = $price->virtuemart_product_price_id;
					$p->sg_id           = $price->virtuemart_shoppergroup_id;
					$p->price           = $price->product_price;
					$p->price_override  = $price->override;
					$p->q_start         = $price->price_quantity_start;
					$p->q_end           = $price->price_quantity_end;
					$p->sales_price     = isset($lpr->prices["salesPrice"]) ? ($lpr->prices["salesPrice"]? $lpr->prices["salesPrice"] : "") : "" ;
					
					if(isset($psurog[$p->pp_id]))
						$p->surogate = $psurog[$p->pp_id];
					$ret_prices[] = $p;
				}
		  }
		  $res_item->returned->prices = $ret_prices;
	   }
	   
	   if($prod_info->product_parent_id){
			if(isset($task->product_sales_price) || isset($task->product_override_price) || isset($task->product_price)){
				clear_product_default_price( $db,  $key); 	
			}
	   }
	   
	   if($return_added){
		$res_item->surogate = $sKEY;
		$res_item->full     = vmel_getProduct($key,$productModel,$db,$custom_fields,$cat_asoc,$man_asoc);;
	   }
	 
	   if(!isset($task->product_sales_price) && (isset($task->product_price) || isset($task->product_override_price))){ 
			$npp = $productModel->getProduct($key,false,true,false);
			$res_item->returned->product_sales_price = $npp->prices["salesPrice"];
			$res_item->returned->product_price       = $npp->prices["basePrice"];
	   }
		
	   $res[] = $res_item;
	}
	
	echo json_encode($res);
    exit; 
	return;
}
}


$import_count = 0;
if(isset($_REQUEST["do_import"])){
	if($_REQUEST["do_import"] = "1"){
	    //$fileContent = file_get_contents($_FILES['file']['tmp_name']);
	   
	    $n = 0;
		if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
			$id_index           	 = -1;
			$price_index        	 = -1;
			$price_o_index      	 = -1;
			$product_sales_price_index = -1;
			$stock_index        	 = -1;
			$sku_index          	 = -1;
			$ordered_index      	 = -1;
			
			$weight_index        	 = -1;
			$weight_uom_index   	 = -1;
			$length_index       	 = -1;
			$width_index        	 = -1;
			$height_index        	 = -1;
			$lwh_uom_index       	 = -1;
			$packaging_index     	 = -1;
			$unit_index          	 = -1;
			
			$metarobot_index         = -1;
		    $metaauthor_index        = -1;
			$metadesc_index          = -1;
			$metakey_index           = -1;
			$customtitle_index       = -1;
			
			$product_url_index       = -1;
			$product_gtin            = -1;
			$product_mpn             = -1;
			
			$manufacturer_name_index = -1; 
			$categories_names_index  = -1;
			$product_name_index      = -1;
			$product_s_desc_index    = -1;
			$product_special_index   = -1;
			$published_index         = -1;
			$slug_index              = -1;
			
			$cf_indexes = array();
			$col_count = 0;
			
			$cic = array();
			foreach($SETTINGS->custom_import_columns as $col){
			   if($col)
				$cic[] = $col;
			}
			$SETTINGS->custom_import_columns = $cic;
			
			$exit = false;
			if($SETTINGS->custom_import){
				$col_count = count($SETTINGS->custom_import_columns);
				$data      = $SETTINGS->custom_import_columns;
				for($i = 0 ; $i < $col_count; $i++){
					if    ($data[$i] == "virtuemart_product_id")  $id_index                = $i;
					elseif($data[$i] == "product_price")          $price_index             = $i;
					elseif($data[$i] == "product_override_price") $price_o_index           = $i;
					elseif($data[$i] == "product_sales_price")    $product_sales_price_index     = $i;
					elseif($data[$i] == "product_sku")            $sku_index               = $i;
					elseif($data[$i] == 'product_in_stock')       $stock_index             = $i;
					elseif($data[$i] == 'product_ordered')    	  $ordered_index           = $i;
					elseif($data[$i] == 'product_weight')     	  $weight_index            = $i;
					elseif($data[$i] == 'product_weight_uom') 	  $weight_uom_index        = $i;
					elseif($data[$i] == 'product_length')     	  $length_index            = $i;
					elseif($data[$i] == 'product_width')      	  $width_index             = $i;
					elseif($data[$i] == 'product_height')     	  $height_index            = $i;
					elseif($data[$i] == 'product_lwh_uom')    	  $lwh_uom_index           = $i;
					elseif($data[$i] == 'metarobot')              $metarobot_index         = $i;
					elseif($data[$i] == 'metaauthor')             $metaauthor_index        = $i;
					elseif($data[$i] == 'metadesc')               $metadesc                = $i;
					elseif($data[$i] == 'metakey')                $metakey                 = $i;
					elseif($data[$i] == 'customtitle')            $customtitle             = $i;
					elseif($data[$i] == 'product_url')            $product_url_index       = $i;
					elseif($data[$i] == 'product_gtin')           $product_gtin_index      = $i;
					elseif($data[$i] == 'product_mpn')            $product_mpn_index       = $i;
					elseif($data[$i] == 'product_packaging')  	  $packaging_index         = $i;
					elseif($data[$i] == 'product_unit')       	  $unit_index              = $i;
					elseif($data[$i] == 'product_special')    	  $product_special_index   = $i;
					elseif($data[$i] == 'published')          	  $published_index         = $i;
					elseif($data[$i] == 'product_name')       	  $product_name_index      = $i;
					elseif($data[$i] == 'product_s_desc')     	  $product_s_desc_index    = $i;
					elseif($data[$i] == 'slug')               	  $slug_index              = $i;
					
					elseif($data[$i] == 'virtuemart_manufacturer_id') $manufacturer_name_index = $i; 
					elseif($data[$i] == 'categories')   	      	  $categories_names_index  = $i;
					
					elseif($data[$i] == 'manufacturer_name')  	  	  $manufacturer_name_index = $i; 
					elseif($data[$i] == 'categories_names')   	      $categories_names_index  = $i;
					
					else{
						foreach($custom_fields as $cf_id => $cf){
							$filed_name = str_replace(array(" ","-",":",";","?",">","<","!","'",'"'),"_", strtolower($cf['custom_title'])).'_cf'.$cf_id;
							if($filed_name == $data[$i]){
								$cf_indexes[$cf_id] = $i;
								break;
							}
						}
					}
				}
			}
			global $calculator;
			while (($data = fgetcsv($handle, 8192 * 4, $SETTINGS->csv_separator)) !== FALSE  && !$exit) {
			    if($n == 0 && $SETTINGS->custom_import && $SETTINGS->first_row_header){
					//NOTHING 
				}elseif($n == 0 && !$SETTINGS->custom_import){
				   	$id_index    = 0;
					//$price_index = count($data) - 1;
					//$stock_index = count($data) - 2;
					
					
					$col_count = count($data);
					for($i = 0 ; $i < $col_count; $i++){
				        if($data[$i]     == "virtuemart_product_id")  $id_index                = $i;
						elseif($data[$i] == "product_price")          $price_index             = $i;
						elseif($data[$i] == "product_override_price") $price_o_index           = $i;
						elseif($data[$i] == "product_sales_price")    $product_sales_price_index     = $i;
						elseif($data[$i] == "product_sku")            $sku_index               = $i;
						elseif($data[$i] == 'product_in_stock')       $stock_index             = $i;
						elseif($data[$i] == 'product_ordered')    	  $ordered_index           = $i;
						elseif($data[$i] == 'product_weight')     	  $weight_index            = $i;
						elseif($data[$i] == 'product_weight_uom') 	  $weight_uom_index        = $i;
						elseif($data[$i] == 'product_length')     	  $length_index            = $i;
						elseif($data[$i] == 'product_width')      	  $width_index             = $i;
						elseif($data[$i] == 'product_height')     	  $height_index            = $i;
						elseif($data[$i] == 'product_lwh_uom')    	  $lwh_uom_index           = $i;
						elseif($data[$i] == 'metarobot')              $metarobot_index         = $i;
						elseif($data[$i] == 'metaauthor')             $metaauthor_index        = $i;
						
						elseif($data[$i] == 'metadesc')               $metadesc                = $i;
					    elseif($data[$i] == 'metakey')                $metakey                 = $i;
					    elseif($data[$i] == 'customtitle')            $customtitle             = $i;
						
					    elseif($data[$i] == 'product_url')            $product_url_index       = $i;
					    elseif($data[$i] == 'product_gtin')           $product_gtin_index      = $i;
					    elseif($data[$i] == 'product_mpn')            $product_mpn_index       = $i;						
						elseif($data[$i] == 'product_packaging')  	  $packaging_index         = $i;
						elseif($data[$i] == 'product_unit')       	  $unit_index              = $i;
						elseif($data[$i] == 'product_special')    	  $product_special_index   = $i;
						elseif($data[$i] == 'published')          	  $published_index         = $i;
						
						elseif($data[$i] == 'product_name')       	  $product_name_index      = $i;
						elseif($data[$i] == 'product_s_desc')     	  $product_s_desc_index    = $i;
						elseif($data[$i] == 'slug')               	  $slug_index              = $i;
						
						elseif($data[$i] == 'manufacturer_name')  	  $manufacturer_name_index = $i; 
						elseif($data[$i] == 'categories_names')   	  $categories_names_index  = $i;
						
						else{
							foreach($custom_fields as $cf_id => $cf){
							    $filed_name = str_replace(array(" ","-",":",";","?",">","<","!","'",'"'),"_", strtolower($cf['custom_title'])).'_cf'.$cf_id;
								if($filed_name == $data[$i]){
									$cf_indexes[$cf_id] = $i;
									break;
								}
							}
						}
					}
					
					
					
				}else{
				
				    if($id_index != -1 || $sku_index != -1){
					    while(count($data) < $col_count)
							$data[] = NULL;
						
						for($I = 0; $I < $col_count ; $I++){
							if($data[$I])
								$data[$I] = addslashes($data[$I]);
						}
					
						$id  = 0;
						if($id_index > -1)
							$id = $data[$id_index];
						
						$sku = 'XXXXXXX';
						if($sku_index > -1)
							$sku = $data[$sku_index];
							
						///////////////////////////////////////////
						
						
						
						if(!$id){
						  $db->setQuery("SELECT virtuemart_product_id FROM #__virtuemart_products WHERE product_sku LIKE '".$sku."';");
						  $obj = $db->loadObject();
						  if($obj){
							$id = $obj->virtuemart_product_id;
						  }
						}
						$is_add = false;
						if(!$id && $sku){
						   $id = vmel_addProduct($db,$vm_languages,$user->id);
						   if($id < 1 || !$id)
								continue;
						   $is_add = true;	
						}
						
						if(!$id && !$sku){
							$n++;
							continue;
						}
							
					
						///////////////////////////////////////////
						
						$db->setQuery("SELECT * FROM #__virtuemart_products WHERE virtuemart_product_id = ".$id);
						$prod_info = $db->loadObject();
						
						$uset = array();
						
						if($sku_index > -1)
							$uset[] = " pr.product_sku = '". $data[$sku_index] ."' ";
						
						$any_price_set = false;
						$price_set = false;
						if($product_sales_price_index != -1){
							if( is_numeric($data[$product_sales_price_index])){
								$pdata = array();
								$pdata["salesPrice"] = $data[$product_sales_price_index];
								$uset[] = " pr_p.product_price = ". dbnum($calculator->calculateCostprice ($id, $pdata)) . " " ;
								$price_set = true;
								$any_price_set = true;
							}
						}
						
						if($price_index != -1 && !$price_set){
							$uset[] = " pr_p.product_price  = ".default_val_num($data[$price_index],'NULL')." ";
							$any_price_set = true;
						}
						
						if($price_o_index != -1){
							$uset[] = " pr_p.product_override_price  = ".default_val_num($data[$price_o_index],'NULL')." ";	
							if(!$data[$price_o_index])
								$uset[] = " pr_p.override = 0";	
							else
								$uset[] = " pr_p.override = ".$SETTINGS->override_price;	
							$any_price_set = true;
						}
						
						if($any_price_set && $prod_info->product_parent_id){
							fix_prices_table( $db, $user->id, $id, false); 	
						}
						
                        if($stock_index != -1)
							$uset[] = " pr.product_in_stock = ".default_val_num($data[$stock_index],'0')." ";
						
						if($ordered_index != -1)
							$uset[] = " pr.product_ordered = ".default_val_num($data[$ordered_index],'0')." ";	
							
						if($weight_index      != -1)
							$uset[] = " pr.product_weight  = ".dbnum($data[$weight_index])." ";
							
						if($weight_uom_index  != -1)
							$uset[] = " pr.product_weight_uom  = '".$data[$weight_uom_index]."' ";
						
						if($length_index      != -1)
							$uset[] = " pr.product_length  = ".dbnum($data[$length_index])." "; 
						
						if($width_index       != -1)
							$uset[] = " pr.product_width  = ".dbnum($data[$width_index])." ";
						
						if($height_index      != -1)
							$uset[] = " pr.product_height  = ".dbnum($data[$height_index])." ";
						
						if($lwh_uom_index     != -1)
							$uset[] = " pr.product_lwh_uom  = '".$data[$lwh_uom_index]."' ";
							
						if($metarobot_index     != -1)
							$uset[] = " pr.metarobot  = '".$data[$metarobot_index]."' ";

						if($metaauthor_index     != -1)
							$uset[] = " pr.metaauthor  = '".$data[$metaauthor_index]."' ";
							
						if($product_url_index != -1)
							$uset[] = " pr.product_url  = '".$data[$product_url_index]."' ";
						
						if($has_gtinmpn){
							if($product_gtin_index != -1)
								$uset[] = " pr.product_gtin  = '".$data[$product_gtin_index]."' ";
							
							if($product_mpn_index != -1)
								$uset[] = " pr.product_mpn  = '".$data[$product_mpn_index]."' ";						
						}
						
						if($packaging_index   != -1)
							$uset[] = " pr.product_packaging  =  ".dbnum($data[$packaging_index])." ";
						
						if($unit_index        != -1)
							$uset[] = " pr.product_unit  = '".$data[$unit_index]."' ";
							
                        if($product_special_index != -1)
						    $uset[] = " pr.product_special  = '".(($data[$product_special_index] || strtolower($data[$product_special_index]) == "yes") ? "1" : "0")."' ";
							
						if($published_index != -1)
							$uset[] = " pr.published  = '".(($data[$published_index] || strtolower($data[$published_index]) == "yes") ? "1" : "0")."' ";
						elseif($is_add)
							$uset[] = " pr.published  = '1'";
						
						$sql_q = " UPDATE 
											#__virtuemart_product_prices as pr_p
											LEFT JOIN
											#__virtuemart_products as pr on pr.virtuemart_product_id = pr_p.virtuemart_product_id
										SET
											".implode(",",$uset)."
										WHERE
											NOT (coalesce(pr_p.price_quantity_start,0) > 0 OR coalesce(pr_p.price_quantity_end,0) > 0) AND NOT coalesce(pr_p.virtuemart_shoppergroup_id,0) > 0
											AND
											pr_p.virtuemart_product_id = $id;";	
						
                        $db->setQuery($sql_q);
						$db->query();
						
						if($prod_info->product_parent_id){
							if($any_price_set){
								clear_product_default_price( $db,  $id); 	
							}
					    }
						
						$import_count ++;
						
						
						foreach($custom_fields as $cf_id => $cf){
							if(isset($cf_indexes[$cf_id])){
							
							    $delete_it = false;
								
							    if(!isset($data[$cf_indexes[$cf_id]]))
									$delete_it = true;
							    elseif( $data[$cf_indexes[$cf_id]] === null || $data[$cf_indexes[$cf_id]] === '')
									$delete_it = true;
							
							    if( $delete_it ){
								
									$Q = "DELETE FROM #__virtuemart_product_customfields
										  WHERE
											   virtuemart_product_id = ".$id."
										  AND
											   virtuemart_custom_id  = ".$cf_id;
											   
									$db->setQuery($Q);
									$db->query();
									
								}else{	 
								
								    $t_values = explode(";",$data[$cf_indexes[$cf_id]]);
									$values = array();
									foreach($t_values as $t_v)
										if(isset($t_v))if($t_v)
											$values[] = $t_v;
									
									$Q = "SELECT virtuemart_customfield_id FROM #__virtuemart_product_customfields
											WHERE
											   virtuemart_product_id = ".$id."
											AND
											   virtuemart_custom_id  = ".$cf_id;
									 
									 $db->setQuery($Q);
									 $existing = $db->loadObjectList();
									 
									for($I = 0; $I < count($values); $I++){
										$value = explode(":",$values[$I]);
										$price = 'NULL';
										if(count($value)> 1){
											$price = $value[1];
											$value = $value[0];
										}else
											$value = $value[0];
											
										if(!$price && $price !== '0')					
											$price = 'NULL';
									  
										if($I < count($existing)){
											 
											 $Q = "UPDATE #__virtuemart_product_customfields
												SET
												   custom".(PLEM_VM_RUN > 2 ? "field" : "")."_value = '".$value."',
												   custom".(PLEM_VM_RUN > 2 ? "field" : "")."_price = ".$price."
												WHERE
												   virtuemart_customfield_id = ".$existing[$I]->virtuemart_customfield_id;
											 
											 $db->setQuery($Q);
											 $db->query();
										}else{
											 $Q = "INSERT INTO #__virtuemart_product_customfields(
													   virtuemart_product_id, virtuemart_custom_id, custom".(PLEM_VM_RUN > 2 ? "field" : "")."_value ,custom".(PLEM_VM_RUN > 2 ? "field" : "")."_price
													) VALUES (".$id.",".$cf_id.",'".$value."',".$price.")"; 
													
											 $db->setQuery($Q);
											 $db->query();
										}
									 }

									 for($I = count($values); $I < count($existing) ; $I++){
										$Q = "DELETE FROM #__virtuemart_product_customfields
											  WHERE
											   virtuemart_customfield_id = ".$existing[$I]->virtuemart_customfield_id;
											   
										$db->setQuery($Q);	
										$db->query();										
									 }
								}					
							}
						}
						
						if($product_name_index != -1 || $product_s_desc_index != -1 || $slug_index != -1 || $metadesc_index != -1 || $metakey_index != -1 || $customtitle_index != -1){
						      $pl_user = array();
							  if( $product_name_index != -1)
								$pl_user[] = "pr_l.product_name = '".$data[$product_name_index]."'";
							  if( $product_s_desc_index != -1)
								$pl_user[] = "pr_l.product_s_desc = '".$data[$product_s_desc_index]."'";
						      if( $slug_index != -1)
								$pl_user[] = "pr_l.slug = '".$data[$slug_index]."'";
								
							  if( $metadesc_index != -1 )
								$pl_user[] = "pr_l.metadesc = '".$data[$metadesc_index]."'";
							  if( $metakey_index != -1 ) 
								$pl_user[] = "pr_l.metakey = '".$data[$metakey_index]."'";
							  if( $customtitle_index != -1)	
								$pl_user[] = "pr_l.customtitle = '".$data[$customtitle_index]."'";
								
							  $pl_sql =	"UPDATE 
											#__virtuemart_products_$vm_lang as pr_l
										 SET
											".implode(",",$pl_user)." 
										 WHERE
											pr_l.virtuemart_product_id = $id;";
											
							  $db->setQuery($pl_sql);		
							  $db->query();			
						}
						
						if($manufacturer_name_index != -1 || $categories_names_index != -1){
						
							if($manufacturer_name_index != -1){
								$man_name = trim( $data[$manufacturer_name_index] );
								$db->setQuery("SELECT id, virtuemart_product_id, virtuemart_manufacturer_id 
												FROM #__virtuemart_product_manufacturers WHERE virtuemart_product_id = ".$id);
								$mp_obj = $db->loadObject();
								
								$db->setQuery("SELECT virtuemart_manufacturer_id, mf_name, mf_email, mf_desc, mf_url, slug 
												FROM #__virtuemart_manufacturers_$vm_lang WHERE mf_name LIKE '$man_name';");
								
								$m_obj  = $db->loadObject();
								
								if($m_obj){
								  if($mp_obj){
										if($mp_obj->virtuemart_manufacturer_id != $m_obj->virtuemart_manufacturer_id){
											$db->setQuery("
												UPDATE #__virtuemart_product_manufacturers 
													SET virtuemart_manufacturer_id = ". $m_obj->virtuemart_manufacturer_id. "
												WHERE id = ". $mp_obj->id );
											$db->query();	
										}
									}else{
										$db->setQuery("
											INSERT INTO #__virtuemart_product_manufacturers(id ,virtuemart_product_id ,virtuemart_manufacturer_id) 
												VALUES 
											( NULL,".$id.",".$m_obj->virtuemart_manufacturer_id.")");
										$db->query();	
									
									}
								}else if(!$man_name && $mp_obj){
									$db->setQuery("DELETE FROM #__virtuemart_product_manufacturers WHERE id=". $mp_obj->id);
									$db->query();	
								}
							}
							
							if($categories_names_index != -1){
							    $categories_names = explode(",", $data[$categories_names_index]);
							    $new_categories_ids = array();
								
                                for($I = 0; $I < count($categories_names); $I++){
									
									$cat_name = strtolower(str_replace("  "," ",str_replace("  ", " ",  trim($categories_names[$I]))));
									$cat_name = str_replace("\\","/",$cat_name);
									$cat_name = str_replace(" /","/",$cat_name);
									$cat_name = str_replace("/ ","/",$cat_name);
									
									if(isset($catway_asoc_reverse[$cat_name])){
										$new_categories_ids[] = $catway_asoc_reverse[$cat_name];
									}else{
										foreach($categories as $cat){
											if(strtolower(str_replace("  "," ",str_replace("  "," ",trim($cat->category_name)))) == $cat_name){
													$new_categories_ids[] = $cat->virtuemart_category_id;
													break;
											}
										}	
									}
								}
								
								/*
								$db->setQuery("SELECT 
												CL.virtuemart_category_id,
												CL.category_name,
												CL.slug
											   FROM 
												#__virtuemart_categories_$vm_lang as CL
											   WHERE '|". implode("|",$categories_names) ."|' LIKE concat('%|', CL.category_name ,'|%') ");
								
								$new_categories = $db->loadObjectList();
								*/
								
								
								$db->setQuery("SELECT 
												CL.virtuemart_category_id,
												CL.category_name,
												CL.slug
											   FROM 
												#__virtuemart_product_categories as PC
											   LEFT JOIN
												#__virtuemart_categories_$vm_lang as CL ON CL.virtuemart_category_id = PC.virtuemart_category_id
											   WHERE PC.virtuemart_product_id = $id");
								
								$old_categories = $db->loadObjectList();
								$old_categories_ids = array();
								//foreach($new_categories as $cat)
								//	$new_categories_ids[] = $cat->virtuemart_category_id;
								foreach($old_categories as $cat)
								    $old_categories_ids[] = $cat->virtuemart_category_id;
								
							    $to_remove = array();
                                $to_add    = array();							    
								
								foreach($new_categories_ids as $ncid){
									if(!in_array($ncid,$old_categories_ids))
										$to_add[] = $ncid;
								}
								
								foreach($old_categories_ids as $ocid){
									if(!in_array($ocid,$new_categories_ids))
										$to_remove[] = $ocid;
								}
								
								if(!empty($to_remove)){
									$db->setQuery("DELETE FROM #__virtuemart_product_categories WHERE virtuemart_product_id=".$id." and virtuemart_category_id IN (".implode( ",", $to_remove ).");");
									$db->query();
								}
								
								if(!empty($to_add)){
								    $db->setQuery("INSERT INTO #__virtuemart_product_categories(
													   id ,virtuemart_product_id ,virtuemart_category_id  ,ordering) 
													SELECT DISTINCT
													  NULL,
													  ".$id.",
													  virtuemart_category_id,
													  0
													FROM 
													  #__virtuemart_categories 
													WHERE
													  virtuemart_category_id IN (".implode(",",$to_add).")");
									$db->query();				  
								}
							}
							
						}
					
					}
				}
				$n++;			
			}
			fclose($handle);
		}
	}
}

$mu_res = 0;

if(isset($_REQUEST["mass_update_val"])){
 
  $ucol  = "";
  $uprop = "pr_p.product_price";
  
  
  if(isset($_REQUEST['mass_update_override'])){
   if($_REQUEST['mass_update_override']){
	 $ucol = " pr_p.override = ".$SETTINGS->override_price." , ";
	 $uprop = "pr_p.product_override_price";
   }
  } 
  
  
  if($_REQUEST["mass_update_percentage"]){
     $ucol .= "$uprop = $uprop * (1 +  ".$_REQUEST["mass_update_val"]." / 100)";
  }else{
     $ucol .= "$uprop = $uprop + ".$_REQUEST["mass_update_val"];
  }

  $muquery ="   UPDATE 
				   #__virtuemart_products as p
				LEFT JOIN
				   #__virtuemart_product_prices as pr_p on pr_p.virtuemart_product_id = p.virtuemart_product_id    
				LEFT JOIN
				   #__virtuemart_products_".$vm_lang." as pl on pl.virtuemart_product_id = p.virtuemart_product_id
				LEFT JOIN
				   #__virtuemart_product_categories as pc on pc.virtuemart_product_id = p.virtuemart_product_id
				LEFT JOIN
				   #__virtuemart_categories_".$vm_lang." as cl on cl.virtuemart_category_id = pc.virtuemart_category_id 
				LEFT JOIN
				   #__virtuemart_product_manufacturers as pm on pm.virtuemart_product_id = p.virtuemart_product_id
				LEFT JOIN  
				   #__virtuemart_manufacturers_".$vm_lang." as ml on ml.virtuemart_manufacturer_id = pm.virtuemart_manufacturer_id
				SET
				  " . $ucol . " 
				WHERE  p.virtuemart_product_id > 0
				" .($product_sku ? " AND p.product_sku LIKE '%".$product_sku."%' " : ""). "
				" .($product_name ? " AND pl.product_name LIKE '%".$product_name."%' " : "")."
				" .
					( $hasCatfn ?
					   ($product_category ? " AND #__plem_product_in_cats(p.virtuemart_product_id,'".$product_category."') " : ""):
					   ($product_category ? " AND pc.virtuemart_category_id IN (".$product_category.") " : "")
					)
				."
				" .($product_manufacturer ? " AND pm.virtuemart_manufacturer_id = ".$product_manufacturer." " : "")."
				" .($product_in_stock ? " AND p.product_in_stock ".$product_in_stock_f." " : "")."
				" .($product_show  ? ($product_show == 1 ? " AND coalesce(p.published,0) = 0 " : "")  : " AND coalesce(p.published,0) = 1 ");
				
	$db->setQuery($muquery);
	$mu_res = $db->query();
	
	$db->setQuery("UPDATE #__virtuemart_product_prices SET override = 0 WHERE coalesce(product_override_price,0) = 0");
	$db->query();
	
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////

$query = "	FROM 
			  #__virtuemart_products as p
			LEFT JOIN
			  #__virtuemart_product_prices as pr_p on pr_p.virtuemart_product_id = p.virtuemart_product_id  
			LEFT JOIN
			  #__virtuemart_products_".$vm_lang." as pl on pl.virtuemart_product_id = p.virtuemart_product_id
			LEFT JOIN
			  #__virtuemart_product_categories as pc on pc.virtuemart_product_id = p.virtuemart_product_id
			LEFT JOIN
			  #__virtuemart_categories_".$vm_lang." as cl on cl.virtuemart_category_id = pc.virtuemart_category_id 
			LEFT JOIN
			  #__virtuemart_product_manufacturers as pm on pm.virtuemart_product_id = p.virtuemart_product_id
			LEFT JOIN  
			  #__virtuemart_manufacturers_".$vm_lang." as ml on ml.virtuemart_manufacturer_id = pm.virtuemart_manufacturer_id
			WHERE p.virtuemart_product_id > 0
			 " .($product_sku ? " AND p.product_sku LIKE '%".$product_sku."%' " : ""). "
			 " .($product_name ? " AND pl.product_name LIKE '%".$product_name."%' " : "")."
			 " .
			 ( $hasCatfn ?
			   ($product_category ? " AND #__plem_product_in_cats(p.virtuemart_product_id,'".$product_category."') " : ""):
			   ($product_category ? " AND pc.virtuemart_category_id IN (".$product_category.") " : "")
			 )
			 ."
			 " .($product_manufacturer ? " AND pm.virtuemart_manufacturer_id = ".$product_manufacturer." " : "")."
			 " .($product_in_stock ? " AND p.product_in_stock ".$product_in_stock_f." " : "")."
			 " .($product_show  ? ($product_show == 1 ? " AND coalesce(p.published,0) = 0 " : "")  : " AND coalesce(p.published,0) = 1 ");
			 
			

$db->setQuery("SELECT count( DISTINCT p.virtuemart_product_id) as len " . $query);
$count = $db->loadObject();
$count = $count->len;



$_num_sample = "0.0";
$db->setQuery("SELECT 1 / 2 as `numeric`"); 
$_num_sample = $db->loadObject()->numeric;
//echo "SELECT DISTINCT p.virtuemart_product_id as pr_id " . $query . " ORDER BY " . $sortColumn . " " . $sortOrder . ($limit ? " LIMIT ".( ($page > 1 ? ($page - 1) : 0) * $limit).",".$limit  : "");
$db->setQuery("SELECT DISTINCT p.virtuemart_product_id as pr_id " . $query . " ORDER BY " . $sortColumn . " " . $sortOrder . ($limit ? " LIMIT ".( ($page > 1 ? ($page - 1) : 0) * $limit).",".$limit  : ""));
$records = $db->loadObjectList();
$products = array(); 



if(count($records)){
	foreach($records as $record){
		try{
			$prod       = vmel_getProduct($record->pr_id,$productModel,$db,$custom_fields,$cat_asoc,$man_asoc);  
			if($prod === NULL){
				continue;
			}	
			$products[] = $prod;		 
		}catch(Exception $e){
			$plem_errors .= "Product id:" . $record->pr_id . " broken data!";
		}
	}
}

if(isset($_REQUEST["do_export"])){
	if($_REQUEST["do_export"] = "1"){
	
		$filename = "data_export_" . date("Y-m-d") . ".csv";
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");

		// force download  
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");

		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename={$filename}");
		header("content-type:application/csv;charset=UTF-8");
		header("Content-Transfer-Encoding: binary");
		echo "\xEF\xBB\xBF"; // UTF-8 BOM
		
		
	    $df = fopen("php://output", 'w');
	   
	    
		if(count($products)){
		   $pprops =  (array)$products[0];
		   $props = array();
		   foreach( $pprops as $key => $pprop){
			$props[] = $key;
		   }
		   fputcsv($df, $props,$SETTINGS->csv_separator);   
		}
	   
	    
	     
	    foreach ($products as $row) {
		   fputcsv($df, (array)$row,$SETTINGS->csv_separator);
	    }
		
	    fclose($df);
		
		die();
	    exit;  
	    return;
	}
}


?>
<html>
<head>
<meta charset="UTF-8">
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/lib/jquery-2.0.3.min.js'; ?>" type="text/javascript"></script>

<link rel="stylesheet" href="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/handsontable/jquery.handsontable.full.css'; ?>">
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/handsontable/jquery.handsontable.full.js'; ?>" type="text/javascript"></script>
<!--
//FIX IN jquery.handsontable.full.js:

WalkontableTable.prototype.getLastVisibleRow = function () {
  return this.rowFilter.visibleToSource(this.rowStrategy.cellCount - 1);
};

//changed to:

WalkontableTable.prototype.getLastVisibleRow = function () {
  var hsum = 0;
  var sizes_check = jQuery(".htCore tbody tr").toArray().map(function(s){var h = jQuery(s).innerHeight(); hsum += h; return h;});
  var o_size = this.rowStrategy.cellSizesSum;
  
  if(hsum - o_size > 20){
	this.rowStrategy.cellSizes = sizes_check;
	this.rowStrategy.cellSizesSum = hsum - 1;
	this.rowStrategy.cellCount = this.rowStrategy.cellSizes.length;
	this.rowStrategy.remainingSize = hsum - o_size;
  }
	
  return this.rowFilter.visibleToSource(this.rowStrategy.cellCount - 1);
};
-->
 <?php if( $SETTINGS->allow_delete){ ?>
<link rel="stylesheet" href="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/handsontable/jquery.handsontable.removeRow.css'; ?>">
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/handsontable/jquery.handsontable.removeRow.js'; ?>" type="text/javascript"></script>
<?php } ?>

<link rel="stylesheet" href="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/lib/chosen.min.css'; ?>">
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/lib/chosen.jquery.min.js'; ?>" type="text/javascript"></script>

<link rel="stylesheet" href="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/assets/style.css'; ?>">
<link rel="stylesheet" href="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/assets/tinyeditor.css'; ?>">
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/lib/tiny.editor.packed.js'; ?>" type="text/javascript"></script>
<script type="text/javascript">
function cleanLayout(){
	localStorage.clear();
	doLoad();
	return false;
}

function showSettings(){
    jQuery('#settings-panel').show();
}

jQuery(document).ready(function(){
    jQuery('#cmdSettingsSave').click(function(){
		doLoad(true);
	});
	
	jQuery('#cmdSettingsCancel').click(function(){
		jQuery('#settings-panel').hide();
	});
});

try{
  if(localStorage['dg_manualColumnWidths']){
    localStorage['dg_manualColumnWidths'] = JSON.stringify( eval(localStorage['dg_manualColumnWidths']).map(function(s){
	   if(!s) return null;
	   if(s > 220)
			return 220;
	   return s;	
	}));
  }  
}catch(e){}

<?php
  if($plem_errors){
?>
	 jQuery(window).load(function(){
		alert('<?php echo $plem_errors ;?>');	
	 });  
<?php	  
  }
?>
</script>
</head>
<body>


<div class="header">
<a class="cmdBackToJoomla" href="<?php echo JURI::root(1) . '/administrator/'; ?>" > <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_BACK_TO_JOOMLA"); ?> </a>

<ul class="menu">
  <li><span class="undo"><button id="cmdUndo" onclick="undo();" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_UNDO"); ?></button></span></li>
  <li><span class="redo"><button id="cmdRedo" onclick="redo();" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_REDO"); ?></button></span></li>
  <li>
   <span><span> <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_EXPORT_IMPORT"); ?> &#9655;</span></span>
   <ul>
     <li><span><button onclick="do_export();return false;" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_EXPORT"); ?></button></span></li>
     <li><span><button onclick="do_import();return false;" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT"); ?></button></span></li>
   </ul>
  </li>
  <li>
   <span><span> <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OPTIONS"); ?> &#9655;</span></span>
   <ul>
     <li><span><button onclick="cleanLayout();return false;" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_CLEAN_CACHE"); ?></button></span></li>
	 <li><span><button onclick="showSettings();return false;" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SETTINGS"); ?></button></span></li>
   </ul>
  </li>
  <!--
  <li style="font-weight: bold;">
   <span><a style="color: cyan;font-size: 16px;" href="http://holest.com/index.php/holest-outsourcing/joomla-wordpress/virtuemart-excel-like-product-manager.html">Buy this component!</a></span> 
  </li>
  -->
</ul>
<ul class="lng-menu" style="float:right;">
	<li>
     <span>
		 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_LANGUAGE"); ?></label>
		 <select id="edit_language" style="color:black;" class="save-state" >
		  <?php
		  foreach($vm_languages as $lng => $db_suffix){
			$selected = "";
			if($db_suffix == $vm_lang)
				$selected = ' selected="selected" ';
			
		  ?>
		  <option value="<?php echo $lng;?>" <?php echo $selected;?> ><?php echo $lng; ?></option>
		  <?php
		  }
		  ?>
		 </select>	 
	 </span>
   </li>
</ul>


</div>
<div class="content">
<div class="right_panel opened filtering">
<span class="right_panel_label" ><span class="toggler"><span><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FILTERS");?></span></span></span>

<div class="filter_holder">
  <?php if($SETTINGS->prices) { ?>
  <div id="custom_prices">
   <h4><?php echo JText::sprintf("Quantity"); ?>/<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SHOPPERGROUP"); ?> <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_PRICE"); ?><span></span>:</h4>
   <table cellpadding="0" cellspacing="0" >
	<thead>
	<tr>
		<th><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SHOPPERGROUP"); ?></th>
		<th>&gt;</th>
		<th>&lt;</th>
		<th><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_PRICE"); ?></th>
		<th><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SALES"); ?></th>
		<th><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_OVERRIDE"); ?></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<th colspan="7" ><a class="cmdAddPrice" >+ <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_PRICE"); ?></a></th>
	</tfoot>
   </table>
  </div>
  <?php } ?>
  
  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SKU");?></label>
	 <input placeholder="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SKU");?>" type="text" name="product_sku" value="<?php echo $product_sku;?>"/>
  </div>
  
  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_NAME");?></label>
	 <input placeholder="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_NAME");?>" type="text" name="product_name" value="<?php echo $product_name;?>"/>
  </div>
  
  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_MANUFACTURER");?></label>
	 <select placeholder="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_MANUFACTURER"); ?>" name="product_manufacturer">
	 <option value=""></option>
	 <?php
		foreach($manCats as $mancat){
		  if(count($manCats) > 1) echo "<optgroup label='".$mancat->mf_category_name."'>";
		  foreach($mancat->manufacturers as $man){
		    echo '<option value="'.$man->virtuemart_manufacturer_id.'">'.$man->mf_name.'</option>';
		  }
		  if(count($manCats) > 1) echo "</optgroup>";
		}
	 ?>
	 </select>
  </div>
  
  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_CATEGORY");?></label>
	 <?php
		$categorylist = ShopFunctions::categoryListTree(array(0));
		if(!$categorylist){
			$html = '<select data-placeholder="'.JText::sprintf("COM_VMEXCELLIKEINPUT_SELECT_CATEGORIES").'" class="inputbox" multiple name="product_category" >';
			$html .= '<option value=""></option>';
			foreach($categories as $cat){
				$html .= '<option value="'.$cat->virtuemart_category_id.'" >'.$cat->category_name.'</option>';
			}
			$html .="</select>";
			echo $html;
		}else{
			$html = '<select data-placeholder="'.JText::sprintf("COM_VMEXCELLIKEINPUT_SELECT_CATEGORIES").'" class="inputbox" multiple name="product_category" >';
			$html .= '<option value=""></option>';
			$html .= $categorylist;
			$html .="</select>";
			echo $html;
		}
        
		
		
	 ?>
  </div>

  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_STOCK");?> (<, >, >=, <=, AND)</label>
	 <input placeholder="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_STOCK");?>" type="text" name="product_in_stock" value="<?php echo $product_in_stock;?>"/>
  </div>
  
  <div class="filter_option">
     <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SHOW");?></label>
	 <select name="product_show">
		 <option value="0"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_ONLY_PUBLISHED");?></option>
		 <option value="1"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_ONLY_ULPUBLISHED");?></option>
		 <option value="2"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_ALL");?></option>
	 </select>
  </div>
  
  <div class="filter_option">
     <input id="cmdRefresh" type="submit" class="cmd" value="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_REFRESH");?>" onclick="doLoad();" />
  </div>
  
  <br/>
  <br/>
  <hr/>
  
  <div class="filter_option">
	  <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_MASS_UPDATE"); ?></label> 
	  <input style="width:110px;float:left;" placeholder="<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_MU_WATERMARK",'%'); ?>" type="text" id="txtMassUpdate" value="" /> 
	  <button id="cmdMassUpdate" class="cmd" onclick="massUpdate(false);return false;" style="float:right;"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRICE_UPDATE"); ?></button>
	  <button id="cmdMassUpdateOverride" class="cmd" onclick="massUpdate(true);return false;" style="float:right;"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OVERRIDE_UPDATE"); ?></button>
	  
  </div>
  
</div>

<div id="images_browser" class="aux-editor" >
	<div class="mask"></div>
</div>

<div id="images_editor" class="aux-editor" >
	<div class="mask"></div>
	<button class="back-to-filter">&lt;&lt;&nbsp;&nbsp;<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FILTERS");?></button>
    <button class="save_product_images"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE");?></button>
	<h3 class="product_info"></h3>
	<div class="main_image">
	
	</div>
	<table id="dg_images" cellpadding="0" cellspacing="0" >
		<thead>
		<tr class="header">
		    <th class="move" ></th>
			<th class="order" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_ORDER");?></th>
			<th class="published" >Publish</th>
			
			<th class="ismain" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMAGE");?></th>
			<th class="alt_title" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_TITLE");?> / <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_DESCRIPTION");?> / <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_METAINFO");?></th>
			<th class="thumb" ></th>
			<th class="delete"></th>
		</tr>
		</thead>
		<tbody>
		
		</tbody>
		<tfoot>
		<tr class="footer">
		<td colspan="7">
		    <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_ADDIMAGES") ;?>:</label>  
			<input type="file" name="file" id="p_img_upload" multiple class="product-image-upload">
			<div class="data">
			</div>
		</td>
		</tr>	
		</tfoot>
	</table>
	
</div>
<div id="content_editor" class="aux-editor" >
	<div class="mask"></div>
	<button class="back-to-filter">&lt;&lt;&nbsp;&nbsp;<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FILTERS");?></button>
    <button class="save_product_content"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE");?></button>
	<h3 class="product_info"></h3>
	<textarea id="txtContent">
	</textarea>
</div>

</div>

<div id="dg" style="margin-left:1px;margin-top:1px;overflow: scroll;background:#FBFBFB;">
</div>

</div>
<div class="footer">
 <div class="pagination">
   <label for="txtLimit" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_LIMIT");?></label><input id="txtlimit" class="save-state" style="width:40px;text-align:center;" value="<?php echo $limit;?>"  />
   <?php
       if($limit && ceil($count / $limit) > 1){
	    ?>
	       <input type="hidden" id="paging_page" value="<?php echo $page ?>" />	
		   
		<?php
		  if($page > 1){
		   ?>
		   <span class="page_number" onclick="setPage(this,1);return false;" ><<</span>
		   <span class="page_number" onclick="setPage(this,'<?php echo ($page - 1); ?>');return false;" ><</span>
		   <?php
		  }
		  
	      for($i = 0; $i < ceil($count / $limit); $i++ ){
		    if(($i + 1) < $page - 2 ) continue;
			if(($i + 1) > $page + 2) {
              echo "<label>...</label>";			  
			  break;
			}
		    ?>
              <span class="page_number <?php echo ($i + 1) == $page ? " active " : "";  ?>" onclick="setPage(this,'<?php echo ($i + 1); ?>');return false;" ><?php echo ($i + 1); ?></span>
            <?php			
		  }
		  
		  if($page < ceil($count / $limit)){
		   ?>
		   <span class="page_number" onclick="setPage(this,'<?php echo ($page + 1); ?>');return false;" >></span>
		   <span class="page_number" onclick="setPage(this,'<?php echo ceil($count / $limit); ?>');return false;" >>></span>
		   <?php
		  }
		  
	   }
   ?>
   <span class="pageination_info"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PAGINATION",$page,ceil($count / $limit),$count); ?></span>
   
 </div>
 
 <span class="note" style="float:right;"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_CHANGES_ARE_AUTOSAVED");?></span>
 <span class="wait save_in_progress" ></span>
 
</div>
<iframe id="frameKeepAlive" style="display:none;"></iframe>

<form id="operationFRM" method="POST" >

</form>

<script type="text/javascript">
var categories = <?php echo json_encode($categories);?>;
var manufacturers = new Array();
var manCategories = <?php echo json_encode($manCats); ?>;
var asoc_cats  = {};
var asoc_mans  = {};
var tasks      = {};
var DG = null;
var SUROGATES  = {};
var site_url   = '<?php echo JURI::root(1); ?>/';

window.onbeforeunload = function() {
    try{
		pelmStoreState();
	}catch(e){}  
	
    var n = 0;
	for(var key in tasks)
		n++;
     
	if(n > 0){
	  doSave();
	  return "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PAGE_LEAVE");?>";
	}else
	  return;	   
}

for(var c in categories){
  asoc_cats[categories[c].virtuemart_category_id] = categories[c].category_path;
}

for(var mc in manCategories){
  for(var m in manCategories[mc].manufacturers){
    manufacturers.push(manCategories[mc].manufacturers[m]);
	asoc_mans[ manCategories[mc].manufacturers[m].virtuemart_manufacturer_id ] = manCategories[mc].manufacturers[m].mf_name;
  }
}

var keepAliveTimeoutHande = null;
var resizeTimeout
  , availableWidth
  , availableHeight
  , $window = $(window)
  , $dg     = $('#dg');


var calculateSize = function () {
  var offset = $dg.offset();
  
  $('div.content').outerHeight(window.innerHeight - $('BODY > DIV.header').outerHeight() - $('BODY > DIV.footer').outerHeight());
  
  availableWidth = $('div.content').innerWidth() - offset.left + $window.scrollLeft() - (jQuery('.right_panel').innerWidth() + parseInt(jQuery('.right_panel').css('right')));
  availableHeight = $('div.content').innerHeight();
  $('.right_panel').css('height',(availableHeight) + 'px');
  
  //$('#dg').handsontable('render');
  if(DG)
	DG.updateSettings({ width: availableWidth, height: availableHeight });

  jQuery('.right_panel_label .toggler').outerHeight(jQuery('.right_panel').innerHeight());
  
  var etoolbars = 0;
  jQuery(".tinyeditor > DIV:not(.edit-panel)").each(function(i){
	etoolbars += jQuery(this).outerHeight();
  });
  
  jQuery(".tinyeditor > DIV.edit-panel").innerHeight(jQuery('DIV.right_panel').innerHeight() - etoolbars - 90);
	
};

calculateSize();
$window.on('resize', calculateSize);  


jQuery(document).ready(function(){calculateSize();});
jQuery(window).load(function(){calculateSize();});  

jQuery('#frameKeepAlive').blur(function(e){
     e.preventDefault();
	 return false;
   });
   
function setKeepAlive(){
   if(keepAliveTimeoutHande)
	clearTimeout(keepAliveTimeoutHande);
	
   keepAliveTimeoutHande = setTimeout(function(){
	  jQuery('#frameKeepAlive').attr('src',window.location.href + "&keep_alive=1&diff=" + Math.random());
	  setKeepAlive();
   },30000);
}

function setPage(sender,page){
	jQuery('#paging_page').val(page);
	jQuery('.page_number').removeClass('active');
	jQuery(sender).addClass('active');
	doLoad();
	return false;
}

var pending_load = 0;

function getSortProperty(){
    if(!DG)
		DG = $('#dg').data('handsontable');
				
    var frozen =  <?php echo $SETTINGS->frozen_columns; ?>;
	if(DG.sortColumn <= frozen)
		return DG.colToProp( DG.sortColumn);
	else
		return DG.colToProp( DG.sortColumn + DG.colOffset());
}

function doLoad(withSettingsSave){
    pending_load++;
	if(pending_load < 6){
		var n = 0;
		for(var key in tasks)
			n++;
			
		if(n > 0) {
		  setTimeout(function(){
			doLoad();
		  },2000);
		  return;
		}
	}

    var POST_DATA = {};
	
	POST_DATA.sortOrder            = $('#dg').data('handsontable').sortOrder ? "ASC" : "DESC";
	POST_DATA.sortColumn           = getSortProperty();
	POST_DATA.limit                = $('#txtlimit').val();
	POST_DATA.page                 = $('#paging_page').val();
	
 	POST_DATA.product_sku          = $('.filter_option *[name="product_sku"]').val();
	POST_DATA.product_name         = $('.filter_option *[name="product_name"]').val();
	POST_DATA.product_manufacturer = $('.filter_option *[name="product_manufacturer"]').val();
	POST_DATA.product_category     = $('.filter_option *[name="product_category"]').val();
	POST_DATA.product_in_stock     = $('.filter_option *[name="product_in_stock"]').val();
	POST_DATA.product_show         = $('.filter_option *[name="product_show"]').val();
	POST_DATA.edit_language        = $('#edit_language').val(); 
	
	if(withSettingsSave){
	  var settings = {};
	  jQuery('#settings-panel INPUT[name],#settings-panel TEXTAREA[name],#settings-panel SELECT[name]').each(function(i){
		if(jQuery(this).attr('type') == "checkbox")
			settings[jQuery(this).attr('name')] = jQuery(this)[0].checked ? 1 : 0;
		else
			settings[jQuery(this).attr('name')] = jQuery(this).val(); 
	  });
	  POST_DATA.save_settings = JSON.stringify(settings);
	}
	
    jQuery('#operationFRM').empty();
	
	for(var key in POST_DATA){
		if(POST_DATA[key])
			jQuery('#operationFRM').append("<INPUT type='hidden' name='" + key + "' value='" + POST_DATA[key] + "' />");
	}
	
    jQuery('#operationFRM').submit();
}

function massUpdate(update_override){
    if(!jQuery.trim(jQuery('#txtMassUpdate').val())){
	  alert("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_NO_VALUE");?>");
	  return;
	} 

	if(confirm("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_MASS_CONFIRM");?>")){
		var POST_DATA = {};
		
		POST_DATA.mass_update_val        = parseFloat(jQuery('#txtMassUpdate').val()); 
		POST_DATA.mass_update_percentage = (jQuery('#txtMassUpdate').val().indexOf("%") >= 0) ? 1 : 0;
		POST_DATA.mass_update_override   = update_override ? '1' : '0';
		
		POST_DATA.sortOrder            = $('#dg').data('handsontable').sortOrder ? "ASC" : "DESC";
		POST_DATA.sortColumn           = getSortProperty();
		POST_DATA.limit                = $('#txtlimit').val();
		POST_DATA.page                 = $('#paging_page').val();
		
		POST_DATA.product_sku          = $('.filter_option *[name="product_sku"]').val();
		POST_DATA.product_name         = $('.filter_option *[name="product_name"]').val();
		POST_DATA.product_manufacturer = $('.filter_option *[name="product_manufacturer"]').val();
		POST_DATA.product_category     = $('.filter_option *[name="product_category"]').val();
		POST_DATA.product_in_stock     = $('.filter_option *[name="product_in_stock"]').val();
		POST_DATA.product_show         = $('.filter_option *[name="product_show"]').val();
		POST_DATA.edit_language        = $('#edit_language').val();
		
		jQuery('#operationFRM').empty();
		
		for(var key in POST_DATA){
			if(POST_DATA[key])
				jQuery('#operationFRM').append("<INPUT type='hidden' name='" + key + "' value='" + POST_DATA[key] + "' />");
		}
		jQuery('#operationFRM').submit();
	}
}

var saveHandle = null;
var save_in_progress = false;


function doSave(){
	var update_data = JSON.stringify(tasks); 	   
	save_in_progress = true;
	jQuery(".save_in_progress").show();

	jQuery.ajax({
	url: window.location.href + "&DO_UPDATE=1&diff=" + Math.random(),
	type: "POST",
	dataType: "json",
	data: update_data,
	success: function (data) {
	
		for(var i in data){
		
			if(data[i].surogate){
				var row_ind = SUROGATES[data[i].surogate];
				for(var prop in data[i].full){
					DG.setDataAtRowProp(row_ind, prop, data[i].full[prop], 'skip');
				}
			}else{
				if(data[i].returned){
					for(var prop in data[i].returned){
						if(prop != 'dg_index'){
							DG.setDataAtRowProp(data[i].returned.dg_index, prop, data[i].returned[prop], 'skip');
							if(prop == 'prices'){
								if(jQuery('#custom_prices > table')[0]){
									if(data[i].returned.dg_index == jQuery('#custom_prices > table').attr("dg_index")){
										refreshCustomPrices(data[i].returned[prop]);
									}
								}
							}
						}
					}
				}
			}
		}
		
		var updated = eval("(" + update_data + ")");
		for(key in updated){
		 if(tasks[key]){
			if(JSON.stringify(tasks[key]) == JSON.stringify(updated[key]))
				delete tasks[key];
		 }
		}

		save_in_progress = false;
		jQuery(".save_in_progress").hide();

	},
	error: function(a,b,c){

		save_in_progress = false;
		jQuery(".save_in_progress").hide();
		callSave();
		
	}
	});
}

function callSave(){
    if(saveHandle){
	   clearTimeout(saveHandle);
	   saveHandle = null;
	}
	
	saveHandle = setTimeout(function(){
	   saveHandle = null;
	   
	   if(save_in_progress){
	       setTimeout(function(){
			callSave();
		   },2000);
		   return;
	   }
       doSave();
	},2000);
}

function undo(){
	$('#dg').data('handsontable').undo();
}

function redo(){
	$('#dg').data('handsontable').redo();
}

function numf(num,dec){
	if(!num)
		return "";
		
	var res = "";	
	if(dec && dec > 0){
		res = parseFloat(num).toFixed(dec);
	}else{
		res = parseInt(num);	
	}
	if(isNaN(res))
		return "";
	else 
		return res;
}

function loadCustomPrices(product_id, dg_index , prices, product_name){
    if(prices){
		jQuery('#custom_prices').show();
		var pGrid = jQuery('#custom_prices > table > tbody');
		pGrid.find('> tr').remove();
		jQuery('#custom_prices > table').attr('virtuemart_product_id', product_id)
										.attr('dg_index',dg_index);
		
		jQuery('#custom_prices > h4 > span').html("(" + product_name + ")");
		for( var i = 0; i < prices.length; i++){
			var row = jQuery("<tr alter_no='0'>"
							   +"<td class='group' >" + jQuery(".hidden-control-models .shopper-groups").html() + "</td>"
							   +"<td class='qstart text-center' ><input class='integer' type='text' /></td>"
							   +"<td class='qend text-center' ><input class='integer' type='text' /></td>"
							   +"<td class='price text-right' ><input class='numeric' type='text' /></td>"
							   +"<td class='sales text-right' ><input class='numeric' type='text' /></td>"
							   +"<td class='override numeric text-right'><input type='text' /></td>"
							   +"<td class='remove'><a>&times;</a></td>"
							+"</tr>");
			
			row.find(".group SELECT").val( prices[i].sg_id );
			row.find(".qstart INPUT").val( numf( prices[i].q_start));
			row.find(".qend INPUT").val( numf(prices[i].q_end));
			row.find(".price INPUT").val( numf(prices[i].price,2));
			row.find(".sales INPUT").val( numf(prices[i].sales_price,2));
			row.find(".override INPUT").val(prices[i].override != "0" ?  numf(prices[i].price_override,2) : "0.00");
			pGrid.append(row);
			row.attr('id', prices[i].pp_id );
		}
	}else
		jQuery('#custom_prices').hide();
	return false;
};

function saveCustomPrices(){
	var virtuemart_product_id = jQuery('#custom_prices > table').attr('virtuemart_product_id');
	var dg_index              = jQuery('#custom_prices > table').attr('dg_index');
	var prices     = [];
	
	jQuery('#custom_prices > table > tbody > TR').each(function(i){
	    var row   = jQuery(this);
		var price = {};
		price.pp_id          = row.attr("id");
		price.sg_id          = row.find(".group SELECT").val();
		price.q_start        = row.find(".qstart INPUT").val();
		price.q_end          = row.find(".qend INPUT").val();
		price.price_override = row.find(".override INPUT").val();
		price.price          = row.find(".price INPUT").val();
		price.sales_price    = row.find(".sales INPUT").val();
		if((parseInt(row.find(".price INPUT").attr("alter_no")) | 0) < (parseInt(row.find(".sales INPUT").attr("alter_no")) | 0)){
			price.lastset = "sales";
		}else{
			price.lastset = "price";	
		}
		prices.push(price);
	});
	
	if(!tasks[virtuemart_product_id])
		tasks[virtuemart_product_id] = {};
	tasks[virtuemart_product_id]["prices"]   = prices;
	tasks[virtuemart_product_id]["dg_index"] = dg_index;
	callSave();
};

function refreshCustomPrices(prices){
	try{
		for( var i = 0; i < prices.length; i++ ){
			var row = null;
			if(prices[i].surogate){
				row = jQuery('#custom_prices > table > tbody > TR[id="' + prices[i].surogate + '"]');
				if(row[0])
					row.attr("id", prices[i].pp_id );
			}else
				row = jQuery('#custom_prices > table > tbody > TR[id="' + prices[i].pp_id + '"]');
			if(row[0]){
				if( parseFloat( prices[i].price ) !=  parseFloat( row.find(".price INPUT").val()))
					row.find(".price INPUT").val( numf(prices[i].price,2));
				if( parseFloat( prices[i].sales_price ) !=  parseFloat( row.find(".sales INPUT").val()))
					row.find(".sales INPUT").val( numf(prices[i].sales_price,2));
			}
		}
	}catch(e){}
};

jQuery(document).on("click touchstart",'a.cmdAddPrice',function(e){
	e.preventDefault();
	var pGrid = jQuery('#custom_prices > table > tbody');
	var row = jQuery("<tr alter_no='0' >"
						   +"<td class='group' >" + jQuery(".hidden-control-models .shopper-groups").html() + "</td>"
						   +"<td class='qstart text-center' ><input class='integer' type='text' /></td>"
						   +"<td class='qend text-center' ><input class='integer' type='text' /></td>"
						   +"<td class='price text-right' ><input class='numeric' type='text' /></td>"
						   +"<td class='sales text-right' ><input class='numeric' type='text' /></td>"
						   +"<td class='override numeric text-right'><input type='text' /></td>"
						   +"<td class='remove'><a>&times;</a></td>"
					    +"</tr>");
	pGrid.append(row);
	row.attr('id', "s_" + parseInt( Math.random() * 100000) );
	
	return false;
});

jQuery(document).on("click touchstart",'#custom_prices > table > tbody TD.remove a' ,function(e){
	e.preventDefault();
	var TR = jQuery(this).closest("TR");
	TR.remove();	
	saveCustomPrices();
});

jQuery(document).on("change",'#custom_prices > table > tbody TD INPUT, #custom_prices > table > tbody TD SELECT' ,function(e){
	var TR = jQuery(this).closest("TR");
	TR.attr('alter_no', parseInt(TR.attr('alter_no')) + 1);
	jQuery(this).attr('alter_no', TR.attr('alter_no'));
	
	if(jQuery(this).val()){
		if(jQuery(this).is(".numeric")){
		   jQuery(this).val(numf(jQuery(this).val(),2));
		}else if(jQuery(this).is(".integer")){
		   jQuery(this).val(numf(jQuery(this).val(),0));
		}
	}
	
	saveCustomPrices();
});



jQuery(document).ready(function(){

	var CustomSelectEditor = Handsontable.editors.BaseEditor.prototype.extend();
	CustomSelectEditor.prototype.init = function(){
	   // Create detached node, add CSS class and make sure its not visible
	   this.select = $('<select multiple="1" ></select>')
		 .addClass('htCustomSelectEditor')
		 .hide();
		 
	   // Attach node to DOM, by appending it to the container holding the table
	   this.instance.rootElement.append(this.select);
	};
	
	// Create options in prepare() method
	CustomSelectEditor.prototype.prepare = function(){
       
		//Remember to invoke parent's method
		Handsontable.editors.BaseEditor.prototype.prepare.apply(this, arguments);
		
		var options = this.cellProperties.selectOptions || [];

		var optionElements = options.map(function(option){
			var optionElement = $('<option />');
			if(typeof option === typeof {}){
			  optionElement.val(option.value);
			  optionElement.html(option.name);
			}else{
			  optionElement.val(option);
			  optionElement.html(option);
			}

			return optionElement
		});

		this.select.empty();
		this.select.append(optionElements);
		
		
		var widg = this.select.next();
		var self = this;
		if(!widg.is('.chosen-container')){
			if(!this.cellProperties.select_multiple){
			   this.select.removeAttr('multiple');
			   this.select.change(function(){
					self.finishEditing()
					$('#dg').handsontable("selectCell", self.row , self.col);					
			   });
			}			   

			var chos;

			if(this.cellProperties.allow_random_input)
				chos = this.select.chosen({
					create_option: true,
					create_option_text: 'value',
					persistent_create_option: true,
					skip_no_results: true
				}).data('chosen');
			else
				chos = this.select.chosen().data('chosen');

			chos.container.bind('keyup', function (event) {
			   
			   if(event.keyCode == 13){
				  var src_inp = jQuery(this).find('LI.search-field > INPUT[type="text"]:first');
				  if(src_inp[0])
					if(src_inp.val() == ''){
					   event.stopImmediatePropagation();
					   event.preventDefault();
					   self.finishEditing()
					   self.focus();
					   
					   $('#dg').handsontable("selectCell", self.row + 1, self.col);
					}
			   }
			});
		}
	};
	
	
	CustomSelectEditor.prototype.getValue = function () {
	   return this.select.val() || [];
	};

	CustomSelectEditor.prototype.setValue = function (value) {
	   if(!(value instanceof Array))
		value = value.split(',');
	   
	   this.select.val(value);
	   this.select.trigger("chosen:updated");
	};
	
	CustomSelectEditor.prototype.open = function () {
		//sets <select> dimensions to match cell size
		
		var widg = this.select.next();
		widg.css({
		   height: $(this.TD).height(),
		   'min-width' : $(this.TD).outerWidth() > 250 ? $(this.TD).outerWidth() : 250
		});
		
		widg.find('LI.search-field > INPUT').css({
		   'min-width' : $(this.TD).outerWidth() > 250 ? $(this.TD).outerWidth() : 250
		});

		//display the list
		widg.show();

		//make sure that list positions matches cell position
		widg.offset($(this.TD).offset());
	};
	
	CustomSelectEditor.prototype.focus = function () {
	     this.instance.listen();
    };

	CustomSelectEditor.prototype.close = function () {
		 this.select.next().hide();
	};
	/////////////////////////////////////////////////////////////////////////////////////////////
	var CustomFieldWithPriceEditor = Handsontable.editors.TextEditor.prototype.extend();
	var cleanValueName = function(val){
	   if(!val)
		return '';
       else{
	    var ret = new Array();
		
		val.split(';').map(function(v){
				if(v.replace(" ","") != ""){
					v = v.split(':');
					if(v.length < 2){
					   ret.push( v[0] + ":0");
					}else{
					   var price = v[1];
					   if(!isNaN(parseFloat(price)) && isFinite(price)){
						  ret.push(v[0] + ":" + price); 
					   }else
						  ret.push(v[0] + ":0");	
					}
				}
				return true;
			});
			
		return ret.join(";");
		
	   } 	   
	};
	
	CustomFieldWithPriceEditor.prototype.getValue = function () {
	   return cleanValueName(this.TEXTAREA.value)  || '';
	};

	CustomFieldWithPriceEditor.prototype.setValue = function (value) {
	   this.TEXTAREA.value = cleanValueName(value);
	};
	///////////////////////////////////////////////////////////////////////////////////////////
	


	var clonableARROW = document.createElement('DIV');
	clonableARROW.className = 'htAutocompleteArrow';
	clonableARROW.appendChild(document.createTextNode('\u25BC'));
		
	var CustomSelectRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	    try{
		  
			var ARROW = clonableARROW.cloneNode(true); //this is faster than createElement

			Handsontable.renderers.TextRenderer(instance, td, row, col, prop, value, cellProperties);
			
			var fc = td.firstChild;
			while(fc) {
				td.removeChild( fc );
				fc = td.firstChild;
			}
			
			td.appendChild(ARROW); 
			
			if(value){
				if(cellProperties.select_multiple){ 
					var rval = value;
					if(!(rval instanceof Array))
						rval = rval.split(',');
					
					td.appendChild(document.createTextNode(rval.map(function(s){ 
							if(cellProperties.dictionary[s])
								return cellProperties.dictionary[s];
							else
								return s;
						}).join(', ')
					));
				}else{
					td.appendChild(document.createTextNode(cellProperties.dictionary[value] || value));
				}
			}else{
				//$(td).html('');
			}
			
			Handsontable.Dom.addClass(td, 'htAutocomplete');

			if (!td.firstChild) {
			  td.appendChild(document.createTextNode('\u00A0')); //\u00A0 equals &nbsp; for a text node
			}

			if (!instance.acArrowListener) {
			  //not very elegant but easy and fast
			  instance.acArrowListener = function () {
				instance.view.wt.getSetting('onCellDblClick', null, new WalkontableCellCoords(row, col), td);
			  };

			  instance.rootElement.on('mousedown.htAutocompleteArrow', '.htAutocompleteArrow', instance.acArrowListener); //this way we don't bind event listener to each arrow. We rely on propagation instead

			  //We need to unbind the listener after the table has been destroyed
			  instance.addHookOnce('afterDestroy', function () {
				this.rootElement.off('mousedown.htAutocompleteArrow');
			  });

			}
		}catch(e){
			$(td).html('');
		}
	};
		
	var centerCheckboxRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.renderers.CheckboxRenderer.apply(this, arguments);
	  $(td).css({
		'text-align': 'center',
		'vertical-align': 'middle'
	  });
	};

	
	var centerTextRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.renderers.TextRenderer.apply(this, arguments);
	  $(td).css({
		'text-align': 'center',
		'vertical-align': 'middle'
	  });
	};
	
	var linkRenderer = function (instance, td, row, col, prop, value, cellProperties) {
	   Handsontable.renderers.HtmlRenderer.apply(this, arguments);

		   td.innerHTML  = "";
		   var a = document.createElement("a");
		   a.class  = "view-product";
		   a.target = "_blank";
		   a.href   = decodeURIComponent(value);
		   a.innerHTML = "&gt;&gt;"; 
		   td.appendChild(a);
	   
	};
	
	var contentEditorInvoker = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.renderers.HtmlRenderer.apply(this, arguments);

		   td.innerHTML  = "";
		   var a = document.createElement("a");
		   a.className  = "edit-content";
		   a.target = "_blank";
		   a.href   = "?" + value;
		   a.rel    = instance.getDataAtRowProp(row,'product_sku') + ", " + instance.getDataAtRowProp(row,'product_name');
		   a.innerHTML = "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_EDIT_CONTENT");?>"; 
		   td.appendChild(a);
	  
	};
	
	var imagesEditorInvoker = function (instance, td, row, col, prop, value, cellProperties) {
	  Handsontable.renderers.HtmlRenderer.apply(this, arguments);	

		   td.innerHTML  = "";
		   var a = document.createElement("a");
		   a.className  = "edit-images";
		   a.target = "_blank";
		   a.href   = "?" + value;
		   a.rel    = instance.getDataAtRowProp(row,'product_sku') + ", " + instance.getDataAtRowProp(row,'product_name');
		   a.innerHTML = "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_EDIT_IMAGES");?>"; 
		   td.appendChild(a);
	   
	};
	
	var weight_units  = [
	  { "name":"Kilogramme", "value":"KG" }
	 ,{ "name":"Gramme", "value":"G" }
	 ,{ "name":"Milligramme", "value":"MG" }
	 ,{ "name":"Pounds", "value":"LB" }
	 ,{ "name":"Ounce", "value":"OZ" }
	];
	
	var length_units  = [
	  { "name":"Metres", "value":"M" }
	 ,{ "name":"Centimetres", "value":"CM" }
	 ,{ "name":"Millimetres", "value":"MM" }
	 ,{ "name":"Yards", "value":"YD" }
	 ,{ "name":"Foot", "value":"FT" }
	 ,{ "name":"Inches", "value":"IN" }
	];
	
	var product_units = [
 	  { "name":"kg", "value":"KG" }
	 ,{ "name":"100 g", "value":"100G" }
	 ,{ "name":"m", "value":"M" }
	 ,{ "name":"m²", "value":"SM" }
	 ,{ "name":"m³", "value":"CUBM" }
	 ,{ "name":"l", "value":"L" }
	 ,{ "name":"100 ml", "value":"100ML" }
	];
	
	var namevalueToDictionary = function(arr){
	    var d = {};
		for(var ind in arr){
		    d[arr[ind].value] = arr[ind].name;
		}
		return d; 
	};
/*	
	PM.virtuemart_product_id,
	PM.virtuemart_media_id,
	PM.ordering,
	M.file_mimetype,
	M.published,
	M.file_is_product_image,
	M.file_url,
	M.file_url_thumb
*/
	
	$('#dg').handsontable({
	  data: <?php echo json_encode($products);?>,
	  minSpareRows: <?php echo $SETTINGS->allow_add ? "1" : "0"; ?>,
	  colHeaders: true,
	  rowHeaders: true,
	  contextMenu: false,
	  manualColumnResize: true,
	  manualColumnMove: true,
	  columnSorting: true,
	  persistentState: true,
	  variableRowHeights: false,
	  fillHandle: 'vertical',
	  fixedColumnsLeft: <?php echo $SETTINGS->frozen_columns; ?>,
	  currentRowClassName: 'currentRow',
      currentColClassName: 'currentCol',
	  colWidths:[80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80,80],
	  <?php if( $SETTINGS->allow_delete){ ?>
	  outsideClickDeselects: false,
	  removeRowPlugin: true,
	  <?php } ?>
	  beforeRemoveRow: function (index, amount){
		 if(<?php echo $SETTINGS->allow_delete ? "true" : "false" ?>){
			 if(!DG.getDataAtRowProp(index,"virtuemart_product_id"))
				 return false;
			 
			 if(confirm("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_REMOVE_PRODUCT");?> <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SKU");?>:" + DG.getDataAtRowProp(index,"product_sku") + ", <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_NAME");?>: '" + DG.getDataAtRowProp(index,"product_name") + "', ID:" +  DG.getDataAtRowProp(index,"virtuemart_product_id") + "?")){
				
				var virtuemart_product_id = DG.getDataAtRowProp(index,"virtuemart_product_id");
				
				if(!tasks[virtuemart_product_id])
					tasks[virtuemart_product_id] = {};
				
				tasks[virtuemart_product_id]["DO_DELETE"] = 'delete';
				
				callSave();
				
				return true;		 
			 }else
				return false;
		 }else
			return false;
	  },
	  width: function () {
		if (availableWidth === void 0) {
		  calculateSize();
		}
		return availableWidth ;
	  },
	  height: function () {
		if (availableHeight === void 0) {
		  calculateSize();
		}
		return availableHeight;
	  },
	  colHeaders:[
		"ID"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SKU");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_AVAILABLE");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_FEATURED");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_MANUFACTURER");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_CATEGORY");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_NAME");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_STOCK");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_BACKORDERS");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_PRICE");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_OVERRIDE");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SALES");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SLUG");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_SDESC");?>"
		<?php
		foreach($custom_fields as $cf_id => $cf)
		    echo "\n" . ',"'.JText::sprintf($cf['custom_title']).'"';
		?>
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_WEIGHT");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_WEIGHT_UOM");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_LENGTH");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_WIDTH");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_HEIGHT");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_LWH_UOM");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_PACKAGING");?>"
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_UNIT");?>"
		,"Meta - robot"
		,"Meta - author"
		,"Meta - description"
		,"Meta - keywords"
		,"Custom title"
		,"URL"
		<?php if($has_gtinmpn){ ?>
		,"GTIN (EAN,ISBN)"
		,"MPN"
		<?php } ?>
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMAGES");?>..."
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_CONTENT");?>..."
		,"<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_VEIWPRODUCT");?>"
	  ],
	  
	  columns: [
	   { data: "virtuemart_product_id", readOnly: true , type: 'numeric' }
	  ,{ data: "product_sku"}
	  ,{ data: "published", type: "checkbox", renderer: centerCheckboxRenderer  }
	  ,{ data: "product_special", type: "checkbox", renderer: centerCheckboxRenderer  }
	  ,{
	      data: "virtuemart_manufacturer_id",
		  editor: CustomSelectEditor.prototype.extend(),
		  renderer: CustomSelectRenderer,
		  dictionary: asoc_mans,
		  select_multiple: false,
		  selectOptions: manufacturers.map(function(source){
						   return {
							 "name": source.mf_name , 
							 "value": source.virtuemart_manufacturer_id
						   }
						})
	   }
	  ,{
	    data: "categories",
	    editor: CustomSelectEditor.prototype.extend(),
		renderer: CustomSelectRenderer,
		dictionary: asoc_cats,
		select_multiple: true,
        selectOptions: categories.map(function(source){
						   return {
							 "name": source.category_path , 
							 "value": source.virtuemart_category_id
						   }
						})
	   }
	  ,{ data: "product_name"  }
	  ,{ data: "product_in_stock" ,type: 'numeric',format: '0'}
	  ,{ data: "product_ordered" ,type: 'numeric',format: '0'}
	  ,{ data: "product_price"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
	  ,{ data: "product_override_price"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
	  ,{ data: "product_sales_price"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}	  
	  ,{ data: "slug", type: 'text'  }
	  ,{ data: "product_s_desc" , type: 'text' }
	  <?php
	  foreach($custom_fields as $cf_id => $cf){
		 if($cf['is_cart_attribute'] )
			echo "\n" . ',{ data: "' . "custom_field_" . $cf_id . '" , editor: CustomFieldWithPriceEditor.prototype.extend() }';
		 else
			echo "\n" . ',{ data: "' . "custom_field_" . $cf_id . '" , type: "text" }';
	  }
	  ?>
	  
		,{ data: "product_weight"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
		,{  
		    data: "product_weight_uom",  
		    editor: CustomSelectEditor.prototype.extend(),
			renderer: CustomSelectRenderer,
			dictionary: namevalueToDictionary(weight_units),
			select_multiple: false,
			selectOptions: weight_units
		 }
		,{ data: "product_length"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
		,{ data: "product_width"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
		,{ data: "product_height"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
		,{ 
		    data: "product_lwh_uom",  
		    editor: CustomSelectEditor.prototype.extend(),
			renderer: CustomSelectRenderer,
			dictionary: namevalueToDictionary(length_units),
			select_multiple: false,
			selectOptions: length_units
		}
		,{ data: "product_packaging"  ,type: 'numeric',format: '0<?php echo substr($_num_sample,1,1);?>00'}
		,{ 
		    data: "product_unit" ,  
		    editor: CustomSelectEditor.prototype.extend(),
			renderer: CustomSelectRenderer,
			dictionary: namevalueToDictionary(product_units),
			select_multiple: false,
			selectOptions: product_units
		 }
		 ,{ data: "metarobot"}
		 ,{ data: "metaauthor"}
		 
		 ,{ data: "metadesc"}
		 ,{ data: "metakey"}
		 ,{ data: "customtitle"}
	
		 ,{ data: "product_url"}
		 <?php if($has_gtinmpn){ ?>
		 ,{ data: "product_gtin"}
		 ,{ data: "product_mpn"}
		 <?php } ?>
		 ,{ data: "i_id", readOnly: true,  renderer:  imagesEditorInvoker }
		 ,{ data: "c_id", readOnly: true,  renderer:  contentEditorInvoker }
		 ,{ data: "link", readOnly: true,  renderer:  linkRenderer }
	  ]
	  
	  //,outsideClickDeselects: false
	  //,removeRowPlugin: true
	 
		,afterChange: function (change, source) {
			if (source === 'loadData') return;
			if (source === 'skip') return;
			if (JSON.stringify(change[0][2]) == JSON.stringify(change[0][3]))
				return;
			
			if(!DG)
				DG = $('#dg').data('handsontable');
			
			change.map(function(data){
				var virtuemart_product_id = DG.getDataAtRowProp (data[0],'virtuemart_product_id');	
				
				if(!virtuemart_product_id){
					 if(!data[3])
						return;
				     var surogat = "s" + parseInt( Math.random() * 10000000); 
					 DG.setDataAtRowProp (data[0],'virtuemart_product_id',surogat,'skip');
					 virtuemart_product_id = surogat;
					 SUROGATES[surogat] = data[0];
				}
				
				var prop = data[1];
				var val  = data[3];
				if(!tasks[virtuemart_product_id])
					tasks[virtuemart_product_id] = {};
				tasks[virtuemart_product_id][prop] = val;
				tasks[virtuemart_product_id]["dg_index"] = data[0];
			});
			callSave();
		}
		,afterColumnResize: function(currentCol, newSize){
			//if(!DG)
			//	DG = $('#dg').data('handsontable');

			//DG.view.wt.wtSettings.instance.rowHeightCache = DG.$table.find(' > TBODY > TR').toArray().map(function(s){return $(s).innerHeight();}); 
			//DG.forceFullRender = true;
			//DG.view.render(); //updates all
	    }
		<?php if($SETTINGS->prices) { ?>
		,afterSelection:function(r, c, r_end, c_end){
			
			var prid = DG.getDataAtRowProp(r,'virtuemart_product_id');
			if(prid){
				if(String(prid).indexOf('s') == -1){	
					var pinf = DG.getDataAtRowProp(r,'product_sku');
					if(pinf)
						pinf += ", ";
					else
						pinf = "";
					pinf += DG.getDataAtRowProp(r,'product_name');			
					loadCustomPrices( prid, DG.getCellMeta(r,c).row, DG.getDataAtRowProp(r,'prices'), pinf); 
				}
			}else
				jQuery('#custom_prices').hide();
			
		}
		<?php } ?>
	});
	
	if(!DG){
		DG = $('#dg').data('handsontable');
		
		if(!DG.sortColumn){
			DG.updateSettings({ sortColumn: 0 });
		}
	}
	setKeepAlive();
	
	jQuery('.right_panel_label').click(function(){
		if( jQuery(this).parent().is('.opened')){
			jQuery(this).parent().removeClass('opened').addClass('closed');
		}else{
			jQuery(this).parent().removeClass('closed').addClass('opened');
		}
		jQuery(window).trigger('resize');
	});
	
	jQuery(window).load(function(){
		jQuery(window).trigger('resize');
	});
	
	if('<?php echo $product_manufacturer?>') jQuery('.filter_option *[name="product_manufacturer"]').val("<?php echo $product_manufacturer;?>");
	if('<?php echo $product_category;?>') jQuery('.filter_option *[name="product_category"]').val("<?php echo $product_category;?>".split(','));
	jQuery('.filter_option *[name="product_show"]').val(<?php echo $product_show;?>);
	jQuery('SELECT[name="product_category"]').chosen();
	
});



  <?php
    if($mu_res){
	   $upd_val = $_REQUEST["mass_update_val"].(  $_REQUEST["mass_update_percentage"] ? "%" : "" );
	   ?>
	   jQuery(window).load(function(){
	   alert('<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_MASS_UPDATE_RESULT",$upd_val); ?>');
	   });
	   <?php
	}
	
	if($import_count){
	   ?>
	   jQuery(window).load(function(){
	   alert('<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT_RESULT",$import_count); ?>');
	   });
	   <?php
	}
	
  ?>


function do_export(){
    var link = window.location.href + "&do_export=1" ;
   
    var QUERY_DATA = {};
	QUERY_DATA.sortOrder            = $('#dg').data('handsontable').sortOrder ? "ASC" : "DESC";
	QUERY_DATA.sortColumn           = getSortProperty();
	
	QUERY_DATA.limit                = "9999999999";
	QUERY_DATA.page                 = "1";
	
	QUERY_DATA.product_sku          = $('.filter_option *[name="product_sku"]').val();
	QUERY_DATA.product_name         = $('.filter_option *[name="product_name"]').val();
	QUERY_DATA.product_manufacturer = $('.filter_option *[name="product_manufacturer"]').val();
	QUERY_DATA.product_category     = $('.filter_option *[name="product_category"]').val();
	QUERY_DATA.product_in_stock     = $('.filter_option *[name="product_in_stock"]').val();
	QUERY_DATA.product_show         = $('.filter_option *[name="product_show"]').val();
	QUERY_DATA.edit_language        = $('#edit_language').val();
	
	for(var key in QUERY_DATA){
		if(QUERY_DATA[key])
			link += ("&" + key + "=" + QUERY_DATA[key]);
	}
	
	window.location =  link;
    return false;
}

function do_import(){
    var import_panel = jQuery("<div class='import_form'><form method='POST' enctype='multipart/form-data'><span><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT_MESSAGE"); ?></span><br/><label for='file'><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FILENAME"); ?></label><input type='file' name='file' id='file' /><br/><br/><button class='cmdImport' ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT_SUBMIT"); ?></button><button class='cancelImport'><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT_CANCEL"); ?></button></form></div>"); 
    import_panel.appendTo(jQuery("BODY"));
	
	import_panel.find('.cancelImport').click(function(){
		import_panel.remove();
		return false;
	});
	
	import_panel.find('.cmdImport').click(function(){
		if(!jQuery("#file").val()){
		  alert('<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_NO_VALUE");?>');
		  return false;
		}
	    var frm = import_panel.find('FORM');
		var POST_DATA = {};
		
		POST_DATA.do_import            = "1";
		POST_DATA.sortOrder            = $('#dg').data('handsontable').sortOrder ? "ASC" : "DESC";
		POST_DATA.sortColumn           = getSortProperty();
		POST_DATA.limit                = $('#txtlimit').val();
		POST_DATA.page                 = $('#paging_page').val();
		
		POST_DATA.product_sku          = $('.filter_option *[name="product_sku"]').val();
		POST_DATA.product_name         = $('.filter_option *[name="product_name"]').val();
		POST_DATA.product_manufacturer = $('.filter_option *[name="product_manufacturer"]').val();
		POST_DATA.product_category     = $('.filter_option *[name="product_category"]').val();
		POST_DATA.product_in_stock     = $('.filter_option *[name="product_in_stock"]').val();
		POST_DATA.product_show         = $('.filter_option *[name="product_show"]').val();
		POST_DATA.edit_language        = $('#edit_language').val();
		
		for(var key in POST_DATA){
			if(POST_DATA[key])
				frm.append("<INPUT type='hidden' name='" + key + "' value='" + POST_DATA[key] + "' />");
		}
			
		frm.submit();
		return false;
	});
}

$(document).ready(function(){
	$('#edit_language').change(function(){
		setTimeout(function(){ doLoad();},50);
	});
});



</script>
<script src="<?php echo JURI::root(1) . '/administrator/components/com_vmexcellikeinput/lib/script.js'; ?>" type="text/javascript"></script>

<div id="settings-panel" style="display:none;">
<div>
  <h2> <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SETTINGS"); ?> </h2>
  <table>
    <tr>
	 <td>
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FROZEN"); ?></label>
	 </td>
	 <td>
	   <input type="text" name="frozen_columns" value="<?php echo $SETTINGS->frozen_columns; ?>" />
	 </td>
	</tr>
	
	<tr>
	 <td colspan="2" >
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_HIDECOLUMNS"); ?></label>
	 <br/>
	 <select id="hidden_columns" multiple="multiple" name="hidden_columns"> 
	 </select>
	 </td>
	</tr>
	
	

	<tr>
	 <td>
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OTHER_PRICES"); ?></label>
	 </td>
	 <td>
	   <input type="checkbox" value="1" name="prices" <?php echo $SETTINGS->prices ? " checked='checked' " : ""; ?> />
	 </td>
	</tr>

	<tr>
	 <td>
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_ALLOW_ADD"); ?></label>
	 </td>
	 <td>
	   <input type="checkbox" value="1" name="allow_add" <?php echo $SETTINGS->allow_add ? " checked='checked' " : ""; ?> />
	 </td>
	</tr>

	
	<tr>
	 <td>
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_ALLOW_DELETE"); ?></label>
	 </td>
	 <td>
	   <input type="checkbox" value="1" name="allow_delete" <?php echo $SETTINGS->allow_delete ? " checked='checked' " : ""; ?> />
	 </td>
	</tr>
	
	<tr>
	 <td>
	 <label><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OVERRIDE_PRICE"); ?></label>
	 </td>
	 <td>
	   <select name="override_price" >
	     <option value="1"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OVERRIDE_FINAL"); ?></option>
		 <option value="-1"><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_OVERRIDE_WIHOUT_TAX"); ?></option>
	   </select>
	   <script type="text/javascript">
	     jQuery(document).ready(function(){
		      jQuery('#settings-panel SELECT[name="override_price"]').val('<?php echo $SETTINGS->override_price;?>');   
		 });
	   </script>
	 </td>
	</tr>
	
	
	<tr>
	 <td>
	 <label><?php echo JText::sprintf("CSV separator"); ?></label>
	 </td>
	 <td>
	   <input name="csv_separator" type="text" value="<?php echo $SETTINGS->csv_separator ;?>" />
	 </td>
	</tr>
	
	
	<tr>
	 <td colspan="2" >
		<hr/>
		<label style="font-size:16px;" ><?php echo JText::sprintf("Custom Import Options"); ?></label>
	    <label  class="note" ><?php echo JText::sprintf("(Allows you to use CSV files exported form external programs)"); ?></label>
		
		
		<br/>
		<input type="checkbox" value="1" name="german_numbers" <?php echo $SETTINGS->german_numbers ? " checked='checked' " : ""; ?> /><label><?php echo JText::sprintf("Decimal separator is , (comma)"); ?></label>
		<br/>
		<input type="checkbox" value="1" name="custom_import" <?php echo $SETTINGS->custom_import ? " checked='checked' " : ""; ?> /><label><?php echo JText::sprintf("Use custom import format"); ?></label>
	    <br/>
		<input type="checkbox" value="1" name="first_row_header" <?php echo $SETTINGS->first_row_header ? " checked='checked' " : ""; ?> /><label><?php echo JText::sprintf("First row is header row (skip it)"); ?></label>		
	    <br/>
		<label><?php echo JText::sprintf("Input columns one by one in order your CSV file will give"); ?>:</label>
		<br/>
		<label class="note" ><?php echo JText::sprintf("(You must include ID or SKU)"); ?>:</label>
		<br/>
	    <select id="custom_import_columns" multiple="multiple" name="custom_import_columns"> 
	    </select>

        <script type="text/javascript">
			jQuery(window).load(function(){
				 setTimeout(function(){
					 try{
						var select = jQuery('SELECT[name="custom_import_columns"], SELECT[name="hidden_columns"]');
						var n = 0;
						DG.getSettings().columns.map(function(c){
							select.append(jQuery('<option value="' + c.data + '">' + DG.getSettings().colHeaders[n] + '<option>'));
							n++;
							return c.data;
						});
						
						
						jQuery('SELECT[name="custom_import_columns"]').val(<?php echo json_encode($SETTINGS->custom_import_columns); ?>);
						jQuery('SELECT[name="hidden_columns"]').val(<?php echo json_encode($SETTINGS->hidden_columns); ?>);
						
						var hiddenc = <?php echo json_encode($SETTINGS->hidden_columns); ?>;
						if( hiddenc.length > 0){
							var settings = DG.getSettings(); 
							var v_cols = [];
							var v_head = [];
							for(var i = 0; i < settings.columns.length; i++){
								if(jQuery.inArray(	settings.columns[i].data, hiddenc) < 0){
									v_cols.push( settings.columns[i] );
									v_head.push( settings.colHeaders[i]);	
								}
							}
							DG.updateSettings({ columns: v_cols, colHeaders: v_head});
						}
						
						select.chosen(); 
					 }catch(e){
						alert(e.name + ":" + e.message);
					 }
				 },2000);
			});
        </script>		
		
		
	 </td>
	</tr>
	
  </table>
  <button id="cmdSettingsCancel" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMPORT_CANCEL"); ?></button>
  <button id="cmdSettingsSave" ><?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE"); ?></button>
</div>
</div>

<script type="text/javascript">
var product_images_dirty = false;
var product_content_dirty = false;

jQuery(document).ready(function(){

	var editor = new TINY.editor.edit('editor', {
		id: 'txtContent',
		width: '100%',
		height: "100%",
		cssclass: 'tinyeditor',
		controlclass: 'tinyeditor-control',
		rowclass: 'tinyeditor-header',
		dividerclass: 'tinyeditor-divider',
		controls: ['bold', 'italic', 'underline', 'strikethrough', '|', 'subscript', 'superscript', '|',
			'orderedlist', 'unorderedlist', '|', 'outdent', 'indent', '|', 'leftalign',
			'centeralign', 'rightalign', 'blockjustify', '|', 'unformat', '|', 'undo', 'redo', 'n',
			'font', 'size', 'style', '|', 'image', 'hr', 'link', 'unlink', '|', 'print'],
		footer: true,
		fonts: ['Verdana','Arial','Georgia','Trebuchet MS','Serif','Sans-serif'],
		xhtml: true,
		//cssfile: 'custom.css',
		bodyid: 'editor',
		footerclass: 'tinyeditor-footer',
		toggle: {text: 'source', activetext: 'wysiwyg', cssclass: 'toggle'},
		resize: false
	});

	jQuery("#txtContent").parent().addClass('edit-panel');
	
	

});


function saveProductContent(){
	$('#content_editor').addClass('waiting');
	
	var data = {}; 
	data.product_desc = jQuery('#txtContent').is(':visible') ? jQuery('#txtContent').val() : jQuery('.tinyeditor iframe').contents().find("#editor").html(); 
	
	var language = 'en_gb';
	try{
		language = jQuery('#edit_language').val().toLowerCase().replace("-","_");
	}catch(e){}
	
	jQuery.ajax({
		url: window.location.href + "&P_CONTENT=set&language=" + language + "&virtuemart_product_id=" + jQuery('#txtContent').attr('virtuemart_product_id'),
		type: "POST",
		dataType: "json",
		data: JSON.stringify( data ),
		success: function (data) {
			jQuery('#txtContent').val(data.product_desc);
			jQuery('.tinyeditor iframe').contents().find("#editor").html(data.product_desc);
			
			product_content_dirty = false;
			setTimeout(function(){
				product_content_dirty = false;
			},250);
		},
		error: function(a,b,c){
			alert( "ERROR!");
		}
	}).always(function(){
		$('#content_editor').removeClass('waiting');
	});
}

function editContent(id, product_info){

	if(product_content_dirty == true){
		if( confirm( "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE"); ?> " + $('#images_editor .product_info').text() +  " <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_DESCCONTENT"); ?>?")){
			saveProductContent();
			return;
		}
	}

	product_content_dirty = false;
	$('#content_editor .product_info').text(product_info);
	
	if(!$('div.right_panel').is(".content_edit")){
		$('div.right_panel').removeClass("filtering content_edit images_edit images_browse").addClass("content_edit");
		$('div.right_panel .toggler span').text("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCT_EDIT_CONTENT");?>");
	}	
	
	var language = 'en_gb';
	try{
		language = jQuery('#edit_language').val().toLowerCase().replace("-","_");
	}catch(e){}
	
	jQuery('.right_panel').addClass('waiting');
	jQuery.ajax({
	url: window.location.href + "&P_CONTENT=get&virtuemart_product_id=" + id + "&language=" + language + "&diff=" + Math.random(),
	type: "POST",
	dataType: "json",
	success: function (data) {
			jQuery('#txtContent').attr('virtuemart_product_id',id);
			jQuery('#txtContent').val(data.product_desc);
			jQuery('.tinyeditor iframe').contents().find("#editor").html(data.product_desc);
		},
	error: function(a,b,c){

		}
	}).always(function(){
		jQuery('.right_panel').removeClass('waiting');
	});
	
	if(jQuery('.right_panel').is('.closed'))
		jQuery('.right_panel_label').trigger('click');
	
	calculateSize();
}

function saveProductImages(){
	
	var data = $('#dg_images tbody TR').toArray().map(function(row){ 
		var R = jQuery(row); 
		
		var item = {}; 
		item.virtuemart_product_id = $('#dg_images').attr('virtuemart_product_id');
		item.virtuemart_media_id   = R.attr('mid');
		item.ordering              = jQuery.trim(R.find('TD.order').text());
		item.file_mimetype         = R.attr('mime');
		item.published             = R.find('TD.published INPUT:checked')[0] ? "1" : "0";
		item.file_is_product_image = R.find('TD.ismain INPUT:checked')[0] ? "1" : "0";
		
		item.file_url              = R.attr('src');
		if(R.attr('file_name')){
			item.file_url_thumb    = '';
			item.file_name         = R.attr('file_name');
		}else{
			item.file_url_thumb    = R.attr('thumb');  
		}
		
		item.file_description      = R.find('INPUT[name="description"]').val();
		item.file_meta             = R.find('INPUT[name="meta"]').val();
		item.file_title            = R.find('INPUT[name="title"]').val();
			
	    return item;
	});
	
	$('#images_editor').addClass('waiting');
	jQuery.ajax({
		url: window.location.href + "&P_IMAGES=set&virtuemart_product_id=" + $('#dg_images').attr('virtuemart_product_id'),
		type: "POST",
		dataType: "json",
		data: JSON.stringify( data ),
		success: function (data) {
			listProductImages(data);
			product_images_dirty = false;
			setTimeout(function(){
				product_images_dirty = false;
			},250);
		},
		error: function(a,b,c){
			alert( "ERROR!");
		}
	}).always(function(){
		$('#images_editor').removeClass('waiting');
	});
	
	
	
}


function listProductImages(data){
	jQuery('.main_image').css('background-image', 'none');
	jQuery('#dg_images TR:not(.header):not(.footer)').remove();
	for( var i = 0; i < data.length; i++ ){
		var sTR = "<tr mime='" + data[i].file_mimetype + "' mid='" + data[i].virtuemart_media_id + "' src='" + data[i].file_url + "' thumb='" + data[i].file_url_thumb + "' >"
				+ "<td class='move'> <span><a class='up'>&#8679;</a><a class='down'>&#8681;</a></span></td>" 
				+ "<td class='order'>" + data[i].ordering  + "</td>" 
				+ "<td class='published'><input type='checkbox' value='1' " + (data[i].published == "1" ? "checked='checked'" : "")  + " /></td>" 
				+ "<td class='ismain'><input type='radio' name='ismain' value='1' " + (data[i].file_is_product_image == "1" ? "checked='checked'" : "")  + " /></td>" 
				+ "<td class='alt_title'>"
				  + "<input type='text' name='title' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_TITLE");?>' value='" + data[i].file_title + "' />"
				  + "<input type='text' name='description' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_DESCRIPTION");?>' value='" + data[i].file_description+ "' />"
				  +	"<input type='text' name='meta' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_METAINFO");?>' value='" + data[i].file_title + "' />"
				+ "</td>"
				+ "<td class='thumb' ><img src='" + site_url + (data[i].file_url_thumb ?  data[i].file_url_thumb : data[i].file_url) + "' alt='" + (data[i].file_url_thumb ?  data[i].file_url_thumb : data[i].file_url)  + "' /> </td>" 
				+ "<td class='delete'><a>&times;</a></td>"
				+ "</tr>";	
				
		if(data[i].file_is_product_image == "1"){
			jQuery('.main_image').css('background-image', 'url(' + site_url + data[i].file_url  + ')');
		}
		
		jQuery(sTR).appendTo(jQuery('#dg_images tbody'));
	}
}

function editImages(id, product_info){
	if(product_images_dirty == true){
		if( confirm( "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE"); ?> " + $('#images_editor .product_info').text() +  " <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMAGES"); ?>?")){
			saveProductImages();
			return;
		}
	}

	product_images_dirty = false;
	$('#images_editor .product_info').text(product_info);

	if(!$('div.right_panel').is(".images_edit")){
		$('div.right_panel').removeClass("filtering content_edit images_edit images_browse").addClass("images_edit");
		$('div.right_panel .toggler span').text("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_PRODUCTIMAGES");?>");
	}
	
	jQuery('#dg_images TR:not(.header):not(.footer)').remove();
	jQuery('.right_panel').addClass('waiting');
	jQuery.ajax({
	url: window.location.href + "&P_IMAGES=get&virtuemart_product_id=" + id + "&diff=" + Math.random(),
	type: "POST",
	dataType: "json",
	success: function (data) {
			jQuery('#dg_images').attr('virtuemart_product_id',id);
			listProductImages(data);
			productImagesGridAfterChange();	
		},
	error: function(a,b,c){

		}
	}).always(function(){
		jQuery('.right_panel').removeClass('waiting');
	});
	
	if(jQuery('.right_panel').is('.closed'))
		jQuery('.right_panel_label').trigger('click');
	
	calculateSize();
}

$(document).on('click','a.edit-content',function(e){
	e.preventDefault();
    var h = jQuery(this).attr("href").split("?");
	editContent(  h[ h.length -1], this.rel );
});

$(document).on('click','a.edit-images',function(e){
	e.preventDefault();
    var h = jQuery(this).attr("href").split("?");
	editImages(  h[ h.length -1] , this.rel);
});

$(document).on('click','.back-to-filter',function(e){
	e.preventDefault();
	
	if(product_images_dirty){
		if( confirm( "<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_SAVE"); ?> " + $('#images_editor .product_info').text() +  " <?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_IMAGES"); ?>?")){
			saveProductImages();
			return;
		}
	}
	
	product_images_dirty = false;
	
	
	$('div.right_panel').removeClass("filtering content_edit images_edit images_browse").addClass("filtering");
	$('div.right_panel .toggler span').text("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_FILTERS");?>");
	calculateSize();
});


function productImagesGridAfterChange(){
	jQuery('#dg_images  tbody TR a.up, #dg_images  tbody TR a.down').show();
	jQuery('#dg_images  tbody TR:first a.up, #dg_images  tbody TR:last a.down').hide();
	jQuery('#dg_images tbody TR TD.order').each(function(i){
		jQuery(this).html(i);
	});	
};

$(document).on('click','#dg_images tbody TR TD a.up',function(e){
	var TR = jQuery(this).closest("TR");
	if(TR[0])
		if(TR.prev()[0]){
			TR.insertBefore(TR.prev());
			productImagesGridAfterChange();
			product_images_dirty = true;
		}
});

$(document).on('click','#dg_images tbody TR TD a.down',function(e){
	var TR = jQuery(this).closest("TR");
	if(TR[0])
		if(TR.next()[0]){
			TR.insertAfter(TR.next());
			productImagesGridAfterChange();	
			product_images_dirty = true;
		}	
});

$(document).on('change','#dg_images .ismain INPUT',function(){
	var selected = jQuery('#dg_images .ismain INPUT:checked');
	jQuery('.main_image').css('background-image', 'none');
	if(selected[0]){
		var row = selected.closest('TR');
		if(row.attr('file_name'))
		   jQuery('.main_image').css('background-image', 'url(' + row.attr('src') + ')');
		else	
		   jQuery('.main_image').css('background-image', 'url(' +  site_url + row.attr('src') + ')');
	}
	product_images_dirty = true;
});

$(document).on('keyup','#dg_images INPUT',function(){
	product_images_dirty = true;
});

$(document).on('click','#dg_images TD.delete a',function(e){
	e.preventDefault();
	var TR = jQuery(this).closest('TR');
	var file_name = "";
	
	if(TR.attr('file_name'))
		file_name = TR.attr('file_name');
    else{
		file_name = TR.attr('src').split('/');
		file_name = file_name[file_name.length - 1];
	}
	
	if( confirm("<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_DELETE");?> " + file_name + '?')){
		TR.remove();
		product_images_dirty = true;
	}
	
});

$(document).on('change','INPUT.product-image-upload',function(){
	var input = this;
	if (input.files && input.files[0]) {
		for(var i = 0; i < input.files.length; i++){
			var reader = new FileReader();
			
			reader.onload = function (e) {
				var sTR = "<tr mime='" + e.target.File___.type + "' mid='' src='' thumb='' >"
						+ "<td class='move'> <span><a class='up'>&#8679;</a><a class='down'>&#8681;</a></span></td>" 
						+ "<td class='order'>" + jQuery('#dg_images tbody TR').length  + "</td>" 
						+ "<td class='published'><input type='checkbox' value='1' checked='checked' /></td>" 
						+ "<td class='ismain'><input type='radio' name='ismain' value='1' /></td>" 
						+ "<td class='alt_title'>"
						  + "<input type='text' name='title' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_TITLE");?>' value='' />"
						  + "<input type='text' name='description' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_DESCRIPTION");?>' value='' />"
						  +	"<input type='text' name='meta' placeholder='<?php echo JText::sprintf("COM_VMEXCELLIKEINPUT_METAINFO");?>' value='' />"
						+ "</td>"
						+ "<td class='thumb' url='' ><img src='' alt='' /></td>" 
						+ "<td class='delete'><a>&times;</a></td>"
						+ "</tr>";	
					
				var tr = jQuery(sTR);
				tr.attr('src',e.target.result);
				tr.attr('file_name',e.target.File___.name);
				tr.find('.thumb IMG').attr('src',e.target.result);
				tr.appendTo(jQuery('#dg_images tbody'));
				productImagesGridAfterChange();				
				if(e.target.clearinput__)
					jQuery('#p_img_upload').val(null);
				product_images_dirty = true;	
			};
			
			
			reader.File___ = input.files[i];
			if(i == input.files.length -1){
				reader.clearinput__ = true;
			}
			reader.readAsDataURL(input.files[i]);
		}
    }
});

$(document).on('click','button.save_product_images',function(){
	saveProductImages();
});

$(document).on('click','button.save_product_content',function(){
	saveProductContent();
});




try{
	var fFR = FileReader;
}catch(e){
	jQuery('#dg_images tfoot td').html('To upload images plese update your browser!').css('color','red').css('font-weight','bold');
}


</script>

<div class="hidden-control-models" style="display:none;">
	<div class="shopper-groups" >
	<select>
		<option value="0"></option>
	<?php
		$db->setQuery("SELECT virtuemart_shoppergroup_id, shopper_group_name
					   FROM #__virtuemart_shoppergroups
					   ORDER BY shopper_group_name ASC");			
		$groups = $db->loadObjectList();
		foreach($groups as $group){?>
		<option value="<?php echo $group->virtuemart_shoppergroup_id; ?>" ><?php echo JText::sprintf($group->shopper_group_name); ?></option>
	<?php
		}
	?>
	</select>
	</div>
</div>

</body>
</html>
<?php
exit;
?>
