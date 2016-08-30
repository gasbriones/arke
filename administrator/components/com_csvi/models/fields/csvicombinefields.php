<?php
/**
 * List the available fields
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvitemplates.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with templates
 */
class JFormFieldCsviCombineFields extends JFormFieldCsviForm {

	protected $type = 'CsviCombineFields';

	/**
	 * Get the available fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.3
	 */
	protected function getOptions() {
		// Get the template ID
		$jinput = JFactory::getApplication()->input;
		$session = JFactory::getSession();
		$sess_template_id = $session->get('com_csvi.select_template', 0);
		if ($sess_template_id !== 0) $sess_template_id = unserialize($sess_template_id);
		$template_id = $jinput->get('template_id', $sess_template_id, 'int');

		// Load the fields associated to the template
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value, CONCAT(field_name, " (", file_field_name, "|", id,")") AS text');
		$query->from($db->qn('#__csvi_template_fields'));
		$query->where($db->qn('template_id').' = '.$template_id);
		$query->order($db->qn('ordering'));
		$db->setQuery($query);
		$fields = $db->loadObjectList();
		if (!is_array($fields)) $fields = array();

		return array_merge(parent::getOptions(), $fields);
	}
}