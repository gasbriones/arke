<?php
/**
 * Categories helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

class Com_Categories {

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
	 * @since 		5.3
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvifields', null, null);
	}

	/**
	 * Get the category ID based on it's path
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$category_path	The path of the category
	 * @param		string	$extension		The extension the category belongs to
	 * @return 		int	the ID of the category
	 * @since 		5.3
	 */
	public function getCategoryId($category_path, $extension) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->qn('#__categories'))->where($db->qn('extension').' = '.$db->q($extension))->where($db->qn('path').' = '.$db->q($category_path));
		$db->setQuery($query);
		$catid = $db->loadResult();
		$this->_csvidata->set('id', $catid);
		return $catid;
	}
}