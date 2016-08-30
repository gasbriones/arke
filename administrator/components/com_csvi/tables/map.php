<?php
/**
 * Template types table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetype.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableMap extends JTable {

	/**
	* @param database A database connector object
	*/
	public function __construct($db) {
		parent::__construct('#__csvi_maps', 'id', $db );
	}

	/**
	 * Override the delete function as we need to delete the fields also
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
	public function delete($pk = null) {
		if (parent::delete($pk)) {
			$query = $this->_db->getQuery(true)
			->delete($this->_db->qn('#__csvi_mapheaders'))
			->where($this->_db->qn('map_id').'='.$this->_db->q($pk));
			$this->_db->setQuery($query);
			$this->_db->execute();

			return true;
		}
	}
}