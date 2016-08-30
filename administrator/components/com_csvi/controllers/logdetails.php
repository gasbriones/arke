<?php
/**
 * Log details controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: logdetails.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Log details Controller
 */
class CsviControllerLogdetails extends JControllerLegacy {

	/**
	 * Show the log details
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function display() {
		$jinput = JFactory::getApplication()->input;

		// Check if the run ID is set
		$run_id = $jinput->get('run_id', $jinput->get('cid', array(), 'array'), 'array');

		// Get the first Run ID only
		$run_id = $run_id[0];

		if ($run_id > 0) {
			$jinput->set('run_id', $run_id);

			// Create the view object
			$view = $this->getView('logdetails', 'html');

			// Standard model
			$view->setModel( $this->getModel( 'logdetails', 'CsviModel' ), true );

			// Log functions
			$view->setModel( $this->getModel( 'log', 'CsviModel' ));

			// Now display the view
			$view->display();
		}
		else {
			$this->setRedirect('index.php?option=com_csvi&view=log', JText::_('COM_CSVI_NO_RUNID_FOUND'), 'error');
		}
	}

	/**
	 * Cancel the operation and return to the log view
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function cancel() {
		$this->setRedirect('index.php?option=com_csvi&view=log');
	}
}