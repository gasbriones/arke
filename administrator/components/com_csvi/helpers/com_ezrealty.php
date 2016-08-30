<?php
/**
 * EZ Realty helper file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * The EZ Realty Config Class
 */
class Com_EzRealty {

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
	 * @since 		4.0
	 */
	public function __construct() {
		$jinput = JFactory::getApplication()->input;
		$this->_csvidata = $jinput->get('csvifields', null, null);
	}

	/**
	 * Get the property id, this is necessary for updating existing properties
	 *
	 * @copyright
	 * @author		RolandD
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		integer	id is returned
	 * @since 		5.1
	 */
	public function getPropertyId() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$update_based_on = $template->get('update_based_on', 'property', 'id');
		if ($update_based_on == 'id') {
			return $this->_csvidata->get('id');
		}
		else {
			$property_key = $this->_csvidata->get($update_based_on);
			if ($property_key) {
				$query = $db->getQuery(true);
				$query->select('id')->from('#__ezrealty')->where($db->qn($update_based_on)." = ".$db->q($property_key));
				$db->setQuery($query);
				$csvilog->addDebug('COM_CSVI_FIND_PROPERTY_ID', true);
				return $db->loadResult();
			}
			else return false;
		}
	}

	/**
	 * Unpublish products before import
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see 		prepareImport()
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function unpublishBeforeImport() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		if ($template->get('unpublish_before_import', 'property', 0)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__ezrealty')->set('published = 0');
			// Check if we have a filter
			$filter = $template->get('unpublish_based_on', 'property', '');
			$filter_value = $template->get('unpublish_value', 'property', '');
			if ($filter && $filter_value) $query->where($db->qn($filter).'='.$db->q($filter_value));
			$db->setQuery($query);
			if ($db->query()) $csvilog->addDebug('COM_CSVI_PROPERTYUNPUBLISH_BEFORE_IMPORT', true);
			else $csvilog->addDebug('COM_CSVI_COULD_NOT_UNPUBLISH_BEFORE_IMPORT', true);
		}
	}
}