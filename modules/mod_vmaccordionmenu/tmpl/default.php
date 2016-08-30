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

$src = "
	window.addEvent('domready', function(){
			if (window.vmAccordionMenuLoaded".$tagId.") { return };
			window.window.vmAccordionMenuLoaded".$tagId." = true;
			vmAccordionMenu(
				\"". $tagId ."\", // Menu Tag ID (this id is assigned to menu parent ul tag)
				\"". $expand_img ."\", //path to image used to expand menu item
				\"". $contract_img ."\", //path to image used to collapse menu item
				{duration:".$params->get('duration_time').", transition:Fx.Transitions.Quart.easeOut}, // (optional argument) custom accordion options to override defaults, use null if you want to set next argument without changing this
				".$params->get('hover_time',200).", // (optional argument) hover delay in milliseconds over \"parent menu item\" to open its sublevels, default is 200
				".$params->get('activate_hovering',400)." // (optional argument) enable/disable opening of submenus on hovering
			);
	});
";
$document->addScriptDeclaration( $src );

//$document->addStyleDeclaration( '#a10{position:absolute;right:0px;background:#FFFF88}');
//$document->addStyleDeclaration( '#a20{position:absolute;right:20px;background:#FFFF88;z-index:100;}');
//echo '<pre id="a20">';
//print_r($tree);
//echo '</pre>';

$prev_parent = 0;
$prev_level = 1;
$html = '<ul class="vmaccordionmenu menu'.$menuClassSfx.'" id="'.$tagId.'">';

//flag to minimize checks
$active_marked = true;
$active_path = array();
//first get all the hierarchy of categories to be marked as active
if (!empty($active_category_id)) {
	$active_marked = false;
	foreach ($tree as $k=>$v) {
		if ($k == $active_category_id ) {
			$active_path = $v['path'];
			$active_path[] = $k;
			break;
		}
	}
}

foreach ($tree as $k=>$v) {
	if ($v['level'] < $prev_level) {
		for ($i = 0; $i <($prev_level-$v['level']); $i++) {
			$html .= '</ul>';
		}
	}
	$parent_class = '';
	$children_ul_start = '';
	if ($v['has_children']) {
		$parent_class = 'parent ';
		$children_ul_start = '<ul>';
		$prev_parent = $k;
	}
	$current = '';
	$aclass= 'class="closed"';
	$activeorno = ' closed ';
	if (!$active_marked) {
		if (in_array($k,$active_path)) {
			$activeorno = ' active ';
		}
		if ($k == $active_category_id ) {
			$current = ' id="current" ';
			$parent_class .= ' current ';
			$aclass = 'class="active"';
			$active_marked = true;
		}
	}
	$html .= '<li '.$current.' class="'.$parent_class.$activeorno.'item'.$k.'"><a href="'.$v['category_url'].'" '.$aclass.' ><span>'.$v['category_name'].'</span></a>'.$children_ul_start;
	$prev_level = $v['level'];
}
if (1 < $prev_level) {
	for ($i = 0; $i <($prev_level-1); $i++) {
		$html .= '</ul>';
	}
}
$html .= '</ul>';
echo $html;
?>


