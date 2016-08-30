<?php
/**
 * Virtuemart Waitinglist table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customs.php 2320 2013-02-09 09:59:04Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableCustoms extends JTable {

	/**
	 * Table constructor
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
	public function __construct($db) {
		parent::__construct('#__virtuemart_customs', 'virtuemart_custom_id', $db );
	}

	/**
	 * Resets the default properties
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.1
	 */
	public function reset() {
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v) {
			// If the property is not private, reset it.
			if (strpos($k, '_') !== 0) {
				$this->$k = NULL;
			}
		}
	}

	/**
	 * Check if there is already a waiting list entry
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.1
	 */
	public function check() {
		if (empty($this->virtuemart_custom_id)) {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn($this->_tbl_key));
			$query->from($db->qn($this->_tbl));
			$query->where($db->qn('custom_element').' = '.$db->q($this->custom_element));
			$query->where($db->qn('custom_title').' = '.$db->q($this->custom_title));
			$db->setQuery($query);
			$this->virtuemart_custom_id = $db->loadResult();
			$csvilog->addDebug(JText::_('COM_CSVI_CHECKING_CUSTOMFIELD_EXISTS'), true);
		}
	}
}