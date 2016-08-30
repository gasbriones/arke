<?php
/**
 * About controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.raw.php 2280 2013-01-04 10:49:02Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controllerform');

/**
 * About Controller
 */
class CsviControllerAbout extends JControllerForm {

	/**
	 * Version check
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
	public function createFolder() {
		$model = $this->getModel();
		echo json_encode($model->createFolder());
	}
}