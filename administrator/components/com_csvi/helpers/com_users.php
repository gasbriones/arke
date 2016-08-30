<?php
/**
 * Users helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

class Com_Users {

	private $_csvidata = null;

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
	 * @since 		5.9.5
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvifields', null, null);
	}

	/**
	 * Get the user id, this is necessary for updating existing users
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		integer	id is returned when found else false
	 * @since 		5.9.5
	 */
	public function getUserId() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$id = $this->_csvidata->get('id');
		if ($id) {
			return $id;
		}
		else {
			$email = $this->_csvidata->get('email');
			if ($email) {
				$query = $db->getQuery(true);
				$query->select('id')->from($db->qn('#__users'))->where($db->qn('email').'='.$db->q($email));
				$db->setQuery($query);
				$csvilog->addDebug(JText::_('COM_CSVI_FIND_USER_ID'), true);
				return $db->loadResult();
			}
			else return false;
		}
	}
}