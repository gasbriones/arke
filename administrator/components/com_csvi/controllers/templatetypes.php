<?php
/**
 * Template types controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetypes.php 2380 2013-03-15 14:34:04Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Template types Controller
 */
class CsviControllerTemplatetypes extends JControllerAdmin {

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
	public function getModel($name = 'Templatetype', $prefix = 'CsviModel') {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Reset the template types
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.1.1
	 */
	public function reset() {
		$model = $this->getModel('templatetypes');

		if ($model->reset()) {
			$msg = JText::_('COM_CSVI_TEMPLATETYPE_RESET_SUCCESSFULLY');
			$msgtype = '';
		}
		else {
			$msg = $this->getError();
			$msgtype = 'error';
		}
		$this->setRedirect('index.php?option=com_csvi&view=templatetypes', $msg, $msgtype);
	}
}