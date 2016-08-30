<?php
/**
 * List the order user
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csviakeebasubsorderuser.php 1924 2012-03-02 11:32:38Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order users
 */
class JFormFieldCsviK2Categories extends JFormFieldCsviForm {

	protected $type = 'CsviK2Categories';

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
		$this->options = array();
		if (class_exists('com_k2')) {
			$jinput = JFactory::getApplication()->input;
			$template = $jinput->get('template', null, null);
			$helper = new Com_K2();
			$this->options = $helper->getCategoryTree($template->get('language', 'general', '*'));
		}
		return $this->options;
	}
}
