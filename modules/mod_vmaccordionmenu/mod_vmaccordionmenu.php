<?php
/**
 * VirtueMart2 Categories Accordion menu
 *
 * @package		mod_vmaccordionmenu
 * @author James Frank
 * @author Jan Pavelca - Phoca
 * @author Gruz <arygroup@gmail.com>
 * @copyright	Copyleft - All rights reversed
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');


//$tagId = $params->get('tag_id',"vmAccordionMenu" );
$tagId = 'vmAccordionMenu'.$module->id;
$menuClassSfx = $params->get('menuclass_sfx',"" );

$document	= JFactory::getDocument();
$document->addScript('modules/mod_vmaccordionmenu/assets/vmaccordionmenu.js');


// Params
$vendorId 				= '1';
$categoryModel			= new VirtueMartModelCategory();
$cache 					=  JFactory::getCache('com_virtuemart','callback');
$category_id 			= $params->get('parent_category_id', 0);

//$endlevel 		= ($params->get('endLevel', 10) + 1);// zero for unlimited
$endlevel = -1;

$helper = new modVmAccordionMenuHelper();

$jinput = JFactory::getApplication()->input;
$active_category_id = $jinput->get('virtuemart_category_id', '0', 'int');

$tree = $helper->VmCategoryTree ( $active_category_id, $category_id, $vendorId = 1, $cache, $categoryModel, $endlevel, $o = null);


$expand_img = '';
$contract_img = '';

if ($params->get('highlite_line',false)) {
	$document->addStyleDeclaration( 'ul#'.$tagId.' ul {clear:both;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li span {padding:0 0 0 3px;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li a {display:block;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li img {float:left;padding:6px 3px 0 0;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li a:hover {background:'.$params->get('hover_bg_color','#aaaaaa').';text-decoration:none;}');

	$document->addStyleDeclaration( 'ul#'.$tagId.' li.active, ul#'.$tagId.' li#current ul  {background:'.$params->get('active_bg_color','#f8f8f8').';}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li#current {background:'.$params->get('current_bg_color','#ddd').';}');

	$params->set('show_images',true);
	$params->set('hide_list_style',true);
	$expand_img = $params->get('plus_image',"modules/mod_vmaccordionmenu/assets/plus.gif");
	$contract_img = $params->get('minus_image',"modules/mod_vmaccordionmenu/assets/minus.gif");
	if (empty($expand_img)) {
		$params->set('plus_image',"modules/mod_vmaccordionmenu/assets/plus.gif");
	}
	if (empty($contract_img)) {
		$params->set('minus_image',"modules/mod_vmaccordionmenu/assets/minus.gif");
	}
}



if ($params->get('show_images') == '1') {
	$expand_img = JURI::base()."/".$params->get('plus_image',"modules/mod_vmaccordionmenu/assets/plus.gif");
	$contract_img = JURI::base()."/".$params->get('minus_image',"modules/mod_vmaccordionmenu/assets/minus.gif");
}

if ($params->get('hide_list_style',false)) {
	$document->addStyleDeclaration( 'ul#'.$tagId.' li {list-style:none inside;}');
}

$document->addStyleDeclaration( $params->get('custom_css',''));

if ($params->get('show_path_bold',false)) {
	//make active path bold
	$document->addStyleDeclaration( 'ul#'.$tagId.' li.active a {font-weight:bold;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li.closed a {font-weight:normal;}');
	$document->addStyleDeclaration( 'ul#'.$tagId.' li#current a{color:'.$params->get('current_text_color','red').';}');
}


//$document->addStyleDeclaration( 'li#current a {font-weight:bold;}');
//$document->addStyleDeclaration( 'li#current ul a {font-weight:normal;}');

require(JModuleHelper::getLayoutPath('mod_vmaccordionmenu'));

