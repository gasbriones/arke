<?php
/**
 *
 * Paypal payment plugin
 *
 * @author Jeremy Magne
 * @version $Id: customjs.php 7868 2014-04-29 10:39:03Z alatak $
 * @package VirtueMart
 * @subpackage payment
 * Copyright (C) 2004-2015 Virtuemart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.net
 */


defined ('_JEXEC') or die();

class JElementCustomjs extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'Customjs';

	function fetchElement ($name, $value, &$node, $control_name) {
		

		
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root(true).'/plugins/vmpayment/klikandpay/klikandpay/assets/js/admin.js');
		$doc->addStyleSheet(JURI::root(true).'/plugins/vmpayment/klikandpay/klikandpay/assets/css/klikandpay.css');

		
		return '';		
	}

}