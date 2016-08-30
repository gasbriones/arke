<?php
/**
 * List the order user
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csviakeebasubsorderuser.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('CsviForm');

/**
 * Select list form field with order users
 */
class JFormFieldCsviAkeebasubsOrderUser extends JFormFieldCsviForm {

	protected $type = 'CsviAkeebasubsOrderUser';

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
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$orderuser = $template->get('orderuser', 'order', array(), 'array');
		if (!empty($orderuser)) {
			$query->select($db->qn('user_id', 'value'));
			$query->select($db->qn('name', 'text'));
			$query->from($db->qn('#__akeebasubs_subscriptions', 's'));
			$query->leftJoin($db->qn('#__users', 'u').' ON '.$db->qn('s.user_id').' = '.$db->qn('u.id'));
			$query->where($db->qn('s.user_id').' IN ('.implode(',', $orderuser).')');
			$query->order($db->qn('name'));
			$query->group($db->qn('user_id'));
			$db->setQuery($query);
			$customers = $db->loadObjectList();
			if (empty($customers)) $customers = array();
			return array_merge(parent::getOptions(), $customers);
		}
		else return parent::getOptions();
	}
}