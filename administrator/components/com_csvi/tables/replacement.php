<?php
/**
 * Replacements table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: replacement.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableReplacement extends JTable {

	/**
	* @param database A database connector object
	*/
	public function __construct($db) {
		parent::__construct('#__csvi_replacements', 'id', $db );
	}
}