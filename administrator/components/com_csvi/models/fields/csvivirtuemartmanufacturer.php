<?php
/**
 * List the VirtueMart manufacturers
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvivirtuemartmanufacturer.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with manufacturers
 */
class JFormFieldCsviVirtuemartManufacturer extends JFormFieldCsviForm {

	protected $type = 'CsviVirtuemartManufacturer';

	/**
	 * Specify the options to load based on default site language
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
		$conf = JFactory::getConfig();
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$lang = strtolower($template->get('language', 'general', str_replace('-', '_', $conf->get('language'))));
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('virtuemart_manufacturer_id').' AS value,'.$db->qn('mf_name').' AS text');
		$query->from($db->qn('#__virtuemart_manufacturers_'.$lang));
		$db->setQuery($query);
		$options = $db->loadObjectList();
		if (empty($options)) $options = array();
		return array_merge(parent::getOptions(), $options);
	}
}