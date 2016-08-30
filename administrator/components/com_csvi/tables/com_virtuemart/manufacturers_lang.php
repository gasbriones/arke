<?php
/**
 * Virtuemart Manufacturer table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: manufacturers_lang.php 2288 2013-01-14 17:39:16Z RolandD $
 */

// No direct access
defined('_JEXEC') or die;

class TableManufacturers_lang extends JTable {

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
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		if ($template->get('operation', 'options') == 'manufacturerimport') {
			if ($template->get('language', 'general') == $template->get('target_language', 'general')) $lang = $template->get('language', 'general');
			else $lang = $template->get('target_language', 'general');
		}
		else $lang = $template->get('language', 'general');
		parent::__construct('#__virtuemart_manufacturers_'.$lang, 'virtuemart_manufacturer_id', $db );
	}

	/**
	 * Check if the manufacturer exists
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function check($create = true) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$db = JFactory::getDbo();

		if (!empty($this->virtuemart_manufacturer_id)) {
			$query = $db->getQuery(true);
			$query->select($this->_tbl_key)
				->from($this->_tbl)
				->where($db->qn($this->_tbl_key).' = '.$db->q($this->virtuemart_manufacturer_id));
			$db->setQuery($query);
			$id = $db->loadResult();
			if ($id > 0) {
				// Load the existing data so we have a slug
				$this->load();
				$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_EXISTS', true);

				// Check if the main entry exists
				$query = $db->getQuery(true);
				$query->select('virtuemart_manufacturer_id')
					->from('#__virtuemart_manufacturers')
					->where('virtuemart_manufacturer_id = '.$this->virtuemart_manufacturer_id);
				$db->setQuery($query);
				$mf = $db->loadObject();
				if (empty($mf)) {
					$csvilog->addDebug('Found a manufacturer language but no manufacturer !!!!!');
					// Not good, no main entry found so let's create one
					$query = $db->getQuery(true);
					$query->insert('#__virtuemart_manufacturers')
						->columns(array('virtuemart_manufacturer_id'))
						->values($this->virtuemart_manufacturer_id);
					$db->setQuery($query);
					$db->query();
				}
				return true;
			}
			else {
				if ($create) {
					// Create a dummy entry for updating
					$query->insert($this->_tbl)
						->columns(array($this->_tbl_key))
						->values($this->virtuemart_manufacturer_id);
					$db->setQuery($query);
					if ($db->query()) return true;
					else {
						$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_NOT_EXISTS', true);
						return false;
					}
				}
				else {
					$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_NOT_EXISTS', true);
					return false;
				}
			}
		}
		// We have no manufacturer ID yet, try to find it
		else {
			$query = $db->getQuery(true);
			$query->select($this->_tbl_key)
				->from($this->_tbl)
				->where('mf_name = '.$db->q($this->mf_name));
			$db->setQuery($query);
			$id = $db->loadResult();
			if ($id > 0) {
				$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_EXISTS', true);
				$this->virtuemart_manufacturer_id = $id;
				$this->load();
				return true;
			}
			else {
				if (isset($this->mf_name_trans)) {
					// Check if we can find it by the original name
					$query = $db->getQuery(true);
					$query->select($this->_tbl_key)
						->from('#__virtuemart_manufacturers_'.$template->get('language', 'general'))
						->where('mf_name = '.$db->quote($this->mf_name_trans));
					$db->setQuery($query);
					$id = $db->loadResult();
					if ($id > 0) {
						$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_EXISTS', true);
						$this->virtuemart_manufacturer_id = $id;
						// Create a dummy entry for updating
						$query = $db->getQuery(true);
						$query->insert($this->_tbl)
							->columns(array($this->_tbl_key))
							->values($id);
						$db->setQuery($query);
						if ($db->query()) {
							$this->virtuemart_manufacturer_id = $id;
						}
						return true;
					}
				}

				if ($create) {
					// Find the highest ID
					$query = $db->getQuery(true);
					$query->select('MAX(virtuemart_manufacturer_id)');
					$query->from($this->_tbl);
					$db->setQuery($query);
					$maxid = $db->loadResult();
					$maxid++;
					// Create a dummy entry for updating
					$query = $db->getQuery(true);
					$query->insert($this->_tbl)
						->columns(array($this->_tbl_key))
						->values($maxid);
					$db->setQuery($query);
					if ($db->query()) {
						$this->virtuemart_manufacturer_id = $maxid;
						return true;
					}
					else {
						$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_NOT_EXISTS', true);
						return false;
					}
				}
				else {
					$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_NOT_EXISTS', true);
					return false;
				}
			}
		}
	}

	/**
	 * Create a slug if needed and store the product
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
	public function store() {
		if (empty($this->slug)) {
			// Create the slug
			$this->_validateSlug();
		}

		// Remove the translation name
		unset($this->mf_name_trans);

		return parent::store();
	}

	/**
	 * Validate a slug
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
	private function _validateSlug() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Create the slug
		$this->slug = Com_virtuemart::createSlug($this->mf_name);

		// Check if the slug exists
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT('.$db->qn($this->_tbl_key).')');
		$query->from($this->_tbl);
		$query->where($db->qn('slug').' = '.$db->q($this->slug));
		$db->setQuery($query);
		$slugs = $db->loadResult();
		$csvilog->addDebug(JText::_('COM_CSVI_CHECK_MANUFACTURER_SLUG'), true);
		if ($slugs > 0) {
			$jdate = JFactory::getDate();
			$this->slug .= $jdate->format("Y-m-d-h-i-s").mt_rand();
		}
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