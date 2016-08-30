<?php
/**
 * List the templates
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvitemplates.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with templates
 */
class JFormFieldCsviTemplates extends JFormFieldCsviForm {

	protected $type = 'CsviTemplates';

	/**
	 * Get the export templates set for front-end export
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.0
	 */
	protected function getOptions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('id').' AS value ,'.$db->qn('name').' AS text');
		$query->from($db->qn('#__csvi_template_settings'));
		$query->where($db->qn('settings').' LIKE '.$db->q('%"action":"export"%'));
		$query->where($db->qn('settings').' LIKE '.$db->q('%"export_frontend":"1"%'));
		$query->order($db->qn('name'));
		$db->setQuery($query);
		$templates = $db->loadObjectList();
		return $templates;
	}
}