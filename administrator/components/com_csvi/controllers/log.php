<?php
/**
 * Log controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: log.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Log Controller
 *
 * @package    CSVIVirtueMart
 */
class CsviControllerLog extends JControllerLegacy {

	/**
	 * Constructor
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
	public function __construct() {
		parent::__construct();

		// Redirects
		$this->registerTask('remove_all','remove');
	}

	/**
	 * Cancel the operation
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.5
	 */
	public function cancel() {
		$this->setRedirect('index.php?option=com_csvi&view=log');
	}

	/**
	 * Download a debug log file
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
	public function downloadDebug() {
		$log = $this->getModel('log', 'CsviModel');
		$log->downloadDebug();
	}

	/**
	 * Read a logfile from disk and show it in a popup
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function LogReader() {
		$jinput = JFactory::getApplication()->input;
		$jinput->set('view', 'log');
		$jinput->set('layout', 'logreader');
		$jinput->set('hidemainmenu', 1);

		parent::display();
	}

	/**
	 * Delete log files
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
	public function remove() {
		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('log');
		switch ($this->getTask()) {
			case 'remove':
				$results = $model->getDelete();
				break;
			case 'remove_all':
				$results = $model->getDeleteAll();
				break;
		}

		foreach ($results as $type => $messages) {
			foreach ($messages as $msg) {
				if ($type == 'ok') $this->setMessage($msg);
				else if ($type == 'nok') $this->setMessage($msg, 'error');
			}
		}
		$this->setRedirect('index.php?option=com_csvi&view=log');
	}
}