<?php
/**
 * Analyzer controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Analyzer Controller
 */
class CsviControllerAnalyzer extends JControllerLegacy {

	/**
	 * Prepare for cron line generation
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
	public function analyzer() {
		// Create the view object
		$view = $this->getView('analyzer', 'html');

		// Standard model
		$view->setModel( $this->getModel( 'analyzer', 'CsviModel' ), true );

		// Now display the view
		$view->display();
	}
}