<?php
/**
 * Maintenance controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: maintenance.json.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controllerform');

/**
 * Maintenance Controller
 */
class CsviControllerMaintenance extends JControllerForm {

	/**
	 * Update available fields in steps
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
	public function updateAvailableFieldsSingle() {
		// Create the view object
		$view = $this->getView('maintenance', 'json');

		// View
		$view->setLayout('availablefields');

		// Load the model
		$view->setModel($this->getModel('maintenance', 'CsviModel'), true);
		$view->setModel($this->getModel( 'availablefields', 'CsviModel' ));

		// Now display the view
		$view->display();
	}
}