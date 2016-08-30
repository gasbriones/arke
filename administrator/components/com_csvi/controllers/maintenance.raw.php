<?php
/**
 * Matintenance controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: maintenance.raw.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Maintenance Controller
 */
class CsviControllerMaintenance extends JControllerForm {

	/**
	 * Construct the controller
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
	public function __construct() {
		parent::__construct();

		// Register some task
		$this->registerTask('sortcategories', 'options');
		$this->registerTask('icecatsettings', 'options');
		$this->registerTask('gettemplates', 'options');
	}

	/**
	 * Load the ICEcat settings
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
	public function options() {
		// Create the view object
		$view = $this->getView('maintenance', 'raw');

		// Load the model
		$view->setModel($this->getModel('maintenance', 'CsviModel'), true);
		$view->setModel($this->getModel('templates', 'CsviModel'));

		// Now display the view
		$view->display();
	}

	/**
	 * Load the operations
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
	public function operations() {
		$model = $this->getModel();
		$options = $model->getOperations();
		echo $options;
	}


}