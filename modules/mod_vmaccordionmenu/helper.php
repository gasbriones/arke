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

defined('_JEXEC') or die('Restricted access');

if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
$config= VmConfig::loadConfig();
if (!class_exists( 'VirtueMartModelVendor' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'vendor.php');
//if (!class_exists( 'VmImage' )) require(JPATH_VM_ADMINISTRATOR.DS.'helpers'.DS.'image.php');
//if (!class_exists( 'shopFunctionsF' )) require(JPATH_SITE.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'shopfunctionsf.php');
if(!class_exists('TableMedias')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'medias.php');
if(!class_exists('TableCategories')) require(JPATH_VM_ADMINISTRATOR.DS.'tables'.DS.'categories.php');
if (!class_exists( 'VirtueMartModelCategory' )) require(JPATH_VM_ADMINISTRATOR.DS.'models'.DS.'category.php');
JHTML::_('behavior.mootools');

class modVmAccordionMenuHelper {

	function VmCategoryTree ( $active_category_id, $category_id, $vendorId, $cache, $categoryModel, $endlevel, $o = null) {

		static $level 		= 1;
		static $path 		= array();

		$categories		= $cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ),$vendorId, $category_id );
		//$categories 	= $categoryModel->getChildCategoryList($vendorId, $category_id);

		foreach ($categories as $c) {
			$childCats 	= $cache->call( array( 'VirtueMartModelCategory', 'getChildCategoryList' ),$vendorId, $c->virtuemart_category_id );

			$url 		= JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$c->virtuemart_category_id);

			//$parentCat 	= $cache->call( array( 'VirtueMartModelCategory', 'getParentCategory' ), $c->virtuemart_category_id );
			$parentCat	= $categoryModel->getParentCategory($c->virtuemart_category_id);

			$o[$c->virtuemart_category_id]['parent_id'] = $parentCat->virtuemart_category_id;
			$o[$c->virtuemart_category_id]['category_name'] = $c->category_name;
			$o[$c->virtuemart_category_id]['category_url'] = $url;
			$o[$c->virtuemart_category_id]['has_children'] = false;
			if (isset($childCats) && !empty($childCats)) {
				$o[$c->virtuemart_category_id]['has_children'] = true;
			}
			$o[$c->virtuemart_category_id]['level'] = $level;
			$o[$c->virtuemart_category_id]['path'] = $path;


			if (isset($childCats) && !empty($childCats)) {
				$path[] = $c->virtuemart_category_id;

				$level++;

				if ((int)$endlevel == (int)$level) {
					$level--;
				} else {
					$o = $this->VmCategoryTree( $active_category_id, $c->virtuemart_category_id, $vendorId, $cache, $categoryModel, $endlevel, $o);
					$level--;
				}
				array_pop($path);
			}

		}
		return ($o);
	}

}
