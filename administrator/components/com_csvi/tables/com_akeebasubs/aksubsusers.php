<?php
/**
 * Akeeba Subscription users table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: vmusers.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableAksubsusers extends JTable {

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
		parent::__construct('#__akeebasubs_users', 'akeebasubs_user_id', $db );
	}

	/**
	 * Check if an entry exists or a placeholder is needed
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
	public function check() {
		if (!empty($this->akeebasubs_user_id)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT('.$db->qn($this->_tbl_key).') AS total');
			$query->from($db->qn($this->_tbl));
			$query->where($db->qn($this->_tbl_key).' = '.$db->q($this->virtuemart_user_id));
			$db->setQuery($query);
			if ($db->loadResult() == 1) return true;
			else {
				$query = "INSERT IGNORE INTO ".$db->qn($this->_tbl)." (".$db->qn($this->_tbl_key).") VALUES (".$db->q($this->akeebasubs_user_id).")";
				$db->setQuery($query);
				$db->query();
				return false;
			}
		}
	}

	/**
	 * Reset the keys including primary key
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
	public function reset() {
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v) {
			// If the property is not private, reset it.
			if (strpos($k, '_') !== 0) {
				$this->$k = NULL;
			}
		}
	}
}