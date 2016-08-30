<?php
/**
 * Settings table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: settings.php 2354 2013-03-01 15:29:01Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableSettings extends JTable {

	/**
	 * Method Description
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		database A database connector object
	 * @return
	 * @since 		4.0
	 */
	public function __construct($db) {
		parent::__construct('#__csvi_settings', 'id', $db );
	}
}