<?php
/**
 * Install view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.json.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Install View
 */
class CsviViewInstall extends JViewLegacy {

	/**
	 * Handle the JSON requests
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		string JSON encoded text
	 * @since 		3.0
	 */
	public function display($tpl = null) {
		// Get the task to perform
		$tasks = explode('.', JRequest::getVar('tasks'));
		$task = $tasks[0];
		unset($tasks[0]);

		// Perform the task
		$result = array();
		$result['results'] = $this->get($task);
		if (JRequest::getBool('cancelinstall')) {
			$result['tasks'] = '';
		}
		else {
			$result['results']['messages'][] = JText::_('COM_CSVI_COMPLETED_'.strtoupper($task));

			// Add remaining tasks to the result for further processing
			$result['tasks'] = implode('.', $tasks);
		}

		// Send back the result
		echo json_encode($result);
	}
}