<?php
/**
 * List the order payments
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csviakeebasubsorderpayment.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order payments
 */
class JFormFieldCsviAkeebasubsOrderPayment extends JFormFieldCsviForm {

	protected $type = 'CsviAkeebasubsOrderPayment';

	/**
	 * Get the used payment processor names
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
		// Get the default administrator language
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('processor', 'value'));
		$query->select($db->qn('processor', 'text'));
		$query->from($db->qn('#__akeebasubs_subscriptions'));
		$query->group($db->qn('processor'));
		$db->setQuery($query);
		$methods = $db->loadObjectList();
		if (empty($methods)) $methods = array();
		return array_merge(parent::getOptions(), $methods);
	}
}