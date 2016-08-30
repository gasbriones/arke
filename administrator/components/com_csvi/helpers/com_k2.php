<?php
/**
 * K2 helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

class Com_K2 {

	private $_csvidata = null;
	private $_catsep = null;
	private $_categories = null;
	private $_category_cache = array();

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

		// Load the categories table
		require_once(JPATH_COMPONENT_ADMINISTRATOR.'/tables/com_k2/categories.php');
		$db = JFactory::getDbo();
		$this->_categories = new TableCategories($db);
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
	public function getItemId() {
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
					$catid = $this->getCategoryIdByPath($category_path);
					if (empty($catid)) return false;
				}
				else return false;
			}
			if ($alias && $catid) {
				$query = $db->getQuery(true);
				$query->select('id')->from($db->qn('#__k2_items'))->where($db->qn('alias').'='.$db->q($alias))->where($db->qn('catid').'='.$catid);
				$db->setQuery($query);
				$csvilog->addDebug(JText::_('COM_CSVI_FIND_CONTENT_ID'), true);
				return $db->loadResult();
			}
			else return false;
		}
	}

	/**
	 * Get the category ID for a product
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		int	$product_id	the product ID to get the category for
	 * @return 		int	the category ID the product is linked to limited to 1
	 * @since 		3.0
	 */
	public function getCategoryId($item_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('catid');
		$query->from('#__k2_items');
		$query->where('id = '.$item_id);
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	/**
	 * Get category list
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$language	the language code for the category names
	 * @return
	 * @since 		4.0
	 */
	public function getCategoryTree($language) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// 1. Get all categories
		$query->select('c.parent AS parent_id, c.id, c.name AS catname');
		$query->from('#__k2_categories c');
		$query->leftJoin('#__k2_categories x ON x.parent = c.id');
		$query->where('c.language = '.$db->q($language));
		$db->setQuery($query);
		$rawcats = $db->loadObjectList();
		if (!empty($rawcats)) {
			// 2. Group categories based on their parent_id
			$categories = array();
			foreach ($rawcats as $key => $rawcat) {
				$categories[$rawcat->parent_id][$rawcat->id]['pid'] = $rawcat->parent_id;
				$categories[$rawcat->parent_id][$rawcat->id]['cid'] = $rawcat->id;
				$categories[$rawcat->parent_id][$rawcat->id]['catname'] = $rawcat->catname;
			}
			if (count($rawcats) > 10) $categorysize = 10;
			else $categorysize = count($rawcats)+1;
		}
		$this->_options = array();
		// Add a don't use option
		$this->_options[] = JHtml::_('select.option', '', JText::_('COM_CSVI_EXPORT_DONT_USE'));

		if (isset($categories)) {
			if (count($categories) > 0) {
				// Take the toplevels first
				foreach ($categories[0] as $key => $category) {
					$this->_options[] = JHtml::_('select.option', $category['cid'], $category['catname']);

					// Write the subcategories
					$suboptions = $this->buildCategory($categories, $category['cid'], array());
				}
			}
		}
		return $this->_options;
	}

	/**
	 * Create the subcategory layout
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		array	select options for the category tree
	 * @since 		3.0
	 */
	private function buildCategory($cattree, $catfilter, $subcats, $loop=1) {
		if (isset($cattree[$catfilter])) {
			foreach ($cattree[$catfilter] as $subcatid => $category) {
				$this->_options[] = JHtml::_('select.option', $category['cid'], str_repeat('>', $loop).' '.$category['catname']);
				$subcats = $this->buildCategory($cattree, $subcatid, $subcats, $loop+1);
			}
		}
	}

	/**
	 * Construct the category path
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		array	$catids	The IDs to build a category for
	 * @param		string	$language	The name of the language selector
	 * @return
	 * @since 		4.0
	 */
	private function constructCategoryPath($catids) {
		$catpaths = array();
		if (is_array($catids)) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$csvilog = $jinput->get('csvilog', null, null);
			$db = JFactory::getDbo();

			// Load the category separator
			if (is_null($this->_catsep)) {
				$jinput = JFactory::getApplication()->input;
				$template = $jinput->get('template', null, null);
				$this->_catsep = $template->get('category_separator', 'general', '/');
			}

			// Get the paths
			foreach ($catids as $category_id) {
				// Create the path
				$paths = array();
				while ($category_id > 0) {
					$query = $db->getQuery(true);
					$query->select('c.parent, c.name');
					$query->from('#__k2_categories c');
					$query->leftJoin('#__k2_categories x ON x.parent = c.id');
					$query->where('c.id = '.$category_id);
					$query->where('c.language = '.$db->q($template->get('language', 'general', '*')));
					$db->setQuery($query);
					$path = $db->loadObject();
					$csvilog->addDebug('Get cat ID'.$category_id, true);
					if (is_object($path)) {
						$paths[] = $path->name;
						$category_id = $path->parent;
					}
					else {
						$csvilog->addDebug('COM_CSVI_CANNOT_GET_CATEGORY_ID');
						$csvilog->AddStats('incorrect', 'COM_CSVI_CANNOT_GET_CATEGORY_ID');
						return '';
					}
				}

				// Create the path
				$paths = array_reverse($paths);
				$catpaths[] = implode($this->_catsep, $paths);
			}
		}
		return $catpaths;
	}

	/**
	 * Creates the category path based on a category ID
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		int	$category_id the ID to create the category path from
	 * @return 		string	the category path
	 * @since 		3.0
	 */
	public function createCategoryPath($item_id, $id=false) {
		$db = JFactory::getDbo();

		// Get the category paths
		$query = $db->getQuery(true);
		$query->select($db->qn('id'));
		$query->from($db->qn('#__k2_categories'));
		$query->where($db->qn('id').' = '.$db->q($item_id));
		$db->setQuery($query);
		$catids = $db->loadColumn();

		if (!empty($catids)) {
			// Return the paths
			if ($id) {
				$result = $db->loadColumn();
				if (is_array($result)) return implode('|', $result);
				else return null;
			}
			else {
				$catpaths = $this->constructCategoryPath($catids);
				if (is_array($catpaths)) return implode('|', $catpaths);
				else return null;
			}
		}
		else return null;
	}

	/**
	 * Create a category path based on ID
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		$catids array	list of IDs to generate category path for
	 * @param		string	$language	The name of the language selector
	 * @return
	 * @since 		4.0
	 */
	public function createCategoryPathById($catids) {
		if (!is_array($catids)) $catids = (array)$catids;
		$paths = $this->constructCategoryPath($catids);
		if (is_array($paths)) return implode('|', $paths);
		else return '';
	}

	/**
	 * Gets the ID belonging to the category path
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		string	$category_path	the path to get the ID for
	 * @param		int		$vendor_id		the vendor ID the category belongs to
	 * @return		array	containing category_id
	 * @since 		3.0
	 */
	public function getCategoryIdByPath($category_path, $language='*') {
		// Check for any missing categories, otherwise create them
		$category = $this->_processCategory($category_path, $language);

		return end($category);
	}

	/**
	 * Creates categories from slash delimited line
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param 		array	$category_path	contains the category/categories for a product
	 * @return 		array containing category IDs
	 * @since
	 */
	private function _processCategory($category_path, $language='*') {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$lang = $template->get('language', 'general');

		// Load the category separator
		if (is_null($this->_catsep)) {
			$this->_catsep = $template->get('category_separator', 'general', '/');
		}

		$csvilog->addDebug('Checking category path: '.$category_path);

		// Explode slash delimited category tree into array
		$category_list = explode($this->_catsep, $category_path);
		$category_count = count($category_list);

		$category_parent_id = '0';

		// For each category in array
		for($i = 0; $i < $category_count; $i++) {
			// Check the cache first
			if (array_key_exists($category_parent_id.'.'.$category_list[$i], $this->_category_cache)) {
				$category_id = $this->_category_cache[$category_parent_id.'.'.$category_list[$i]];
			}
			else {
				// See if this category exists with it's parent in xref
				$query = $db->getQuery(true);
				$query->select('c.id');
				$query->from('#__k2_categories c');
				$query->leftJoin('#__k2_categories x ON x.parent = c.id');
				$query->where('c.name = '.$db->q($category_list[$i]));
				$query->where('c.parent = '.$category_parent_id);
				$db->setQuery($query);
				$category_id = $db->loadResult();
				$csvilog->addDebug(JText::_('COM_CSVI_CHECK_CATEGORY_EXISTS'), true);

				// Add result to cache
				$this->_category_cache[$category_parent_id.'.'.$category_list[$i]] = $category_id;
			}

			// Category does not exist - create it
			if (is_null($category_id)) {
				// Let's find out the last category in the level of the new category
				$query = $db->getQuery(true);
				$query->select('MAX(c.ordering) + 1 AS ordering');
				$query->from('#__k2_categories c');
				$query->leftJoin('#__k2_categories x ON x.parent = c.id');
				$query->where('x.parent = '.$category_parent_id);
				$db->setQuery($query);
				$list_order = $db->loadResult();
				if (is_null($list_order)) $list_order = 1;

				// Add category
				$this->_categories->set('name', $category_list[$i]);
				$this->_categories->set('parent', $category_parent_id);
				$this->_categories->set('ordering', $list_order);
				$this->_categories->set('published', 1);
				$this->_categories->store();
				$csvilog->addDebug('Add new category:', true);
				$category_id = $this->_categories->get('id');

				// Add result to cache
				$this->_category_cache[$category_parent_id.'.'.$category_list[$i]] = $category_id;

				// Clean for the next row
				$this->_categories->reset();

			}
			// Set this category as parent of next in line
			$category_parent_id = $category_id;
			$category[] = $category_id;
		}

		// Return an array with the last category_ids which is where the product goes
		return $category;
	}
}