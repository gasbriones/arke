<?php
/**
 * CSVI Controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: controller.php 2368 2013-03-08 14:17:15Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load the controller framework
jimport('joomla.application.component.controller');

/**
 * Base controller
 */
class CsviController extends JControllerLegacy {

	/**
	* Method to display the view
	*
	* @access	public
	*/
	public function display($cachable = false, $urlparams = false) {

		parent::display($cachable, $urlparams);

		return $this;
	}
}