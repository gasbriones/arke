<?php
/**
 * Maintenance view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.raw.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * Maintenance View
 */
class CsviViewMaintenance extends JViewLegacy {

	/**
	 * Handle the JSON calls for maintenance
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.3
	 */
	function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;
		$task = strtolower($jinput->get('task'));
		switch ($task) {
			case 'icecatsettings':
				echo $this->loadTemplate('icecat');
				break;
			case 'sortcategories':
				$this->languages = $this->get('Languages');
				echo $this->loadTemplate('sortcategories');
				break;
			case 'gettemplates':
				$template_model = $this->getModel('Templates');
				$this->templates = $template_model->getTemplates();
				echo $this->loadTemplate('templates');
				break;
		}
	}
}