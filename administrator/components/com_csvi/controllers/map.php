<?php
/**
 *
 * Controller for the map editing
 *
 * @author 		RolandD
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetype.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load the controller framework
jimport('joomla.application.component.controllerform');

/**
 * Controller for the map editing
 *
 * @author 	RolandD
 * @since 	4.0
 */
class CsviControllerMap extends JControllerForm {

	/**
	 * Proxy for getModel
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		object of a database model
	 * @since 		4.0
	 */
	public function getModel($name = 'Map', $prefix = 'CsviModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => false));
		return $model;
	}

	/**
	 * Save the uploaded file data
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function postSaveHook($model, $validData) {
		// Get the ID
		$pk = $model->getState($this->context . '.id');

		if ($pk) {
			// Store the options
			$model->postSaveHook($pk, $validData);

			// Let's see if the user uploaded a file to get new columns
			if (!empty($_FILES['jform']['name']['mapfile'])) {
				$jinput = JFactory::getApplication()->input;

				// Initiate the log
				require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/log.php';
				$csvilog = new CsviLog();
				$jinput->set('csvilog', $csvilog);

				// Save the file
				$model->processFile($pk, $validData);
			}
			// Store any mapped fields
			else {
				$templateheader = JRequest::getVar('templateheader');
				$model->processHeader($validData, $templateheader);
			}
		}
	}
}