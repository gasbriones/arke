<?php
/**
 * List the operations
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvioperations.php 2380 2013-03-15 14:34:04Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with operations
 */
class JFormFieldCsviOperations extends JFormFieldCsviForm {

	protected $type = 'CsviOperations';

	/**
	 * Specify the options to load
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
		$jinput = JFactory::getApplication()->input;
		$jform = $jinput->get('jform', array(), 'array');
		$trans = array();

		if (!empty($jform) && isset($jform['options'])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('template_type_name'))
				->from($db->qn('#__csvi_template_types'))
				->where($db->qn('template_type').' = '.$db->q($jform['options']['action']))
				->where($db->qn('component').' = '.$db->q($jform['options']['component']))
				->where($db->qn('published').'= 1')
				->order($db->qn('ordering'));
			$db->setQuery($query);
			$types = $db->loadColumn();

			// Get translations
			foreach ($types as $type) {
				$trans[$type] = JText::_('COM_CSVI_'.strtoupper($type));
			}
		}
		else {
			$trans = parent::getOptions();
		}
		return $trans;
	}
}