<?php
/**
 * Content helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

class Com_Content {

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
	 * Get the content id, this is necessary for updating existing content
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo 		Reduce number of calls to this function
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		integer	product_id is returned
	 * @since 		5.3
	 */
	public function getContentId() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$id = $this->_csvidata->get('id');
		if ($id) {
			return $id;
		}
		else {
			$alias = $this->_csvidata->get('alias');
			$catid = $this->_csvidata->get('catid');
			if (empty($catid)) {
				$category_path = $this->_csvidata->get('category_path');
				if ($category_path) {
					// We have a category path, let's get the ID
					$catid = $this->getCategoryId($category_path);
					if (empty($catid)) return false;
				}
				else return false;
			}
			if ($alias && $catid) {
				$query = $db->getQuery(true);
				$query->select('id')->from($db->qn('#__content'))->where($db->qn('alias').'='.$db->q($alias))->where($db->qn('catid').'='.$catid);
				$db->setQuery($query);
				$csvilog->addDebug(JText::_('COM_CSVI_FIND_CONTENT_ID'), true);
				return $db->loadResult();
			}
			else return false;
		}
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
	 * @return 		int	the ID of the category
	 * @since 		5.3
	 */
	public function getCategoryId($category_path) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')->from($db->qn('#__categories'))->where($db->qn('extension').' = '.$db->q('com_content'))->where($db->qn('path').' = '.$db->q($category_path));
		$db->setQuery($query);
		$catid = $db->loadResult();
		if (empty($catid)) $catid = 2;
		$this->_csvidata->set('catid', $catid);
		return $catid;
	}
}