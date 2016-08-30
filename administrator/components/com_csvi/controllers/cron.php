<?php
/**
 * Cron controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: cron.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Cron Controller
 */
class CsviControllerCron extends JControllerLegacy {

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
	public function cron() {
		// Store the form fields
		$jinput = JFactory::getApplication()->input;
		$data	= $jinput->post->get('jform', array(), 'array');
		$jinput->set('com_csvi.data', $data);

		// Create the view object
		$view = $this->getView('cron', 'html');

		// Standard model
		$view->setModel( $this->getModel( 'cron', 'CsviModel' ), true );

		// Now display the view
		$view->display();
	}
}