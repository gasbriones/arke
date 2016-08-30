<?php
/**
 * List the product categories
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvivirtuemartproductcategories.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with product categories
 */
class JFormFieldCsviVirtuemartProductCategories extends JFormFieldCsviForm {

	protected $type = 'CsviVirtuemartProductCategories';

	/**
	 * Specify the options to load
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		Set to use chosen language
	 * @see
	 * @access 		protected
	 * @param
	 * @return 		array	an array of options
	 * @since 		4.0
	 */
	protected function getOptions() {
		$this->options = array();
		if (class_exists('com_virtuemart')) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$conf = JFactory::getConfig();
			$lang = strtolower($template->get('language', 'general', str_replace('-', '_', $conf->get('language'))));
			$helper = new Com_VirtueMart();
			$this->options = $helper->getCategoryTree($lang);
		}
		return $this->options;
	}
}