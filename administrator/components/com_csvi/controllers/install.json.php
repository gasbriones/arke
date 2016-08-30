<?php
/**
 * Install controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: install.json.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Install Controller
 *
 * @package    CSVIVirtueMart
 */
class CsviControllerInstall extends JControllerLegacy {

	/**
	 * Upgrade CSVI VirtueMart
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
	public function upgrade() {
		// Create the view object
		$view = $this->getView('install', 'json');

		// Standard model
		$view->setModel( $this->getModel( 'install', 'CsviModel' ), true );
		$view->setModel( $this->getModel( 'availablefields', 'CsviModel' ));
		$view->setModel( $this->getModel( 'maintenance', 'CsviModel' ));

		$view->display();
	}
}