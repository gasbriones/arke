<?php
/**
 * Template types controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetypes.json.php 2380 2013-03-15 14:34:04Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * About Controller
 *
 * @package    CSVIVirtueMart
 */
class CsviControllerTemplatetypes extends JControllerLegacy {

	public function loadTemplateTypes() {
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('templatetypes');
		$action = $jinput->get('action');
		$component = $jinput->get('component');
		echo json_encode($model->loadTemplateTypes($action, $component));
	}

	public function loadSettings() {
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('templatetypes');
		$action = $jinput->get('action');
		$component = $jinput->get('component');
		$operation = $jinput->get('operation');
		echo $model->loadSettings($action, $component, $operation);
	}
}