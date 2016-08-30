<?php
defined("_JEXEC") or die("Direct Access to " . basename(__FILE__) . "is not allowed.");

/**
 *
 * @package    VirtueMart
 * @subpackage Plugins  _ Elements
 * @author Valérie Isaksen
 * @package VirtueMart
 * @copyright Copyright (c) 2004 - 2012 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: $
 */
/**
* Account set up >Set up > URL transaction accepted
* http://mywebsite.com/index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&po=
 *
 * Account set up >Set up > URL refused/cancelled transaction
* http://mywebsite.com/index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&po=
 *
 *  * Account set up > Dynamic Set up > Dynamic return URL
* http://mywebsite.com/index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&po=
*/


class JElementUrls extends JElement {

    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    var $_name = "urls";

    function fetchElement($name, $value, &$node, $control_name) {

	    $dynamic_url=JURI::root() .'index.php?option=com_virtuemart&view=pluginresponse&task=pluginnotification&tmpl=component&po=';
	    $accepted_url=JURI::root() .'index.php?option=com_virtuemart&view=pluginresponse&task=pluginresponsereceived&po=';
	    $refused_url=JURI::root() .'index.php?option=com_virtuemart&view=pluginresponse&task=pluginUserPaymentCancel&po=';
$msg="";
	    $msg.='<div style="margin-top: 10px ;">';
		    $msg.="<strong>".vmText::_('VMPAYMENT_KLIKANDPAY_CONF_DYNAMIC_RETURN_URL')."</strong>";
	    $msg.="<br />";
		    $msg.=vmText::_('VMPAYMENT_KLIKANDPAY_CONF_DYNAMIC_RETURN_URL_TIP');
	    $msg.="</div>";
	    $msg.='<input class="required" readonly size="180" value="'.$dynamic_url.'" />';
	    $msg.="</div>";

	    $msg.='<div style="margin-top: 10px ;">';
	    $msg.="<strong>".vmText::_('VMPAYMENT_KLIKANDPAY_CONF_URL_TRANSACTION_ACCEPTED')."</strong>";
	    $msg.="<br />";
	    $msg.=vmText::_('VMPAYMENT_KLIKANDPAY_CONF_URL_TRANSACTION_ACCEPTED_TIP');
	    $msg.="</div>";
	    $msg.='<input class="required" readonly size="180" value="'.$accepted_url.'" />';
	    $msg.="</div>";

	    $msg.='<div style="margin-top: 10px ;">';
	    $msg.="<strong>".vmText::_('VMPAYMENT_KLIKANDPAY_CONF_URL_TRANSACTION_REFUSED')."</strong>";
	    $msg.="<br />";
	    $msg.=vmText::_('VMPAYMENT_KLIKANDPAY_CONF_URL_TRANSACTION_REFUSED_TIP');
	    $msg.='<input class="required" readonly size="180" value="'.$refused_url.'" />';

	    $msg.="</div>";
	   return $msg;






    }

}