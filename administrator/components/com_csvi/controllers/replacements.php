<?php
/**
 * Replacements controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: replacements.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Replacements Controller
 */
class CsviControllerReplacements extends JControllerAdmin {

	/**
	 * Proxy for getModel
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		object of a database model
	 * @since 		4.0
	 */
	public function getModel($name = 'Replacement', $prefix = 'CsviModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}