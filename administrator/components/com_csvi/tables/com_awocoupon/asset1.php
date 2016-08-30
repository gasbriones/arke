<?php
/**
 * AwoCoupon Gift assets1 table
 *
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: coupons.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableAsset1 extends JTable {

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
		parent::__construct('#__awocoupon_asset1', 'id', $db );
	}

	/**
	 * Check if an asset already exists
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
	public function check() {
		if (isset($this->id)) return true;
		else {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn($this->_tbl_key));
			$query->from($db->qn($this->_tbl));
			$query->where($db->qn('coupon_id').' = '.$db->q($this->coupon_id));
			$query->where($db->qn('asset_type').' = '.$db->q($this->asset_type));
			$query->where($db->qn('asset_id').' = '.$db->q($this->asset_id));
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_CHECK_ASSET_EXISTS'), true);
			$db->query();
			if ($db->getAffectedRows() > 0) {
				$this->id = $db->loadResult();
			}
		}
	}

	/**
	 * Delete all entries for a given coupon
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		int	$coupon_id	the id to delete the entries for
	 * @return		bool	true on success | false on failure
	 * @since 		4.2
	 */
	public function clean($coupon_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete($this->_tbl);
		$query->where('coupon_id = '.$coupon_id);
		$db->setQuery($query);
		return $db->query();
	}

	/**
	 * Reset the table fields, need to do it ourselves as the fields default is not NULL
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