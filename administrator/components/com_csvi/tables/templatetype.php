<?php
/**
 * Template types table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetype.php 2380 2013-03-15 14:34:04Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableTemplatetype extends JTable {

	/**
	* @param database A database connector object
	*/
	public function __construct($db) {
		parent::__construct('#__csvi_template_types', 'id', $db );
	}
}