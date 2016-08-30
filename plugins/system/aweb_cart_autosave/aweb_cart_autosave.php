<?php
/**
 * @package Plugin aWeb_Cart_AutoSave for Joomla! 2.5
 * @version 1.77
 * @author aWebSupport Team
 * @copyright (C) 2013-2014 aWebSupport.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

$path = dirname(__FILE__);
$path = substr($path,0,strpos($path,"plugins/system/aweb_cart_autosave")-1);
$comprpath = JPATH_BASE ."/administrator/components/com_awebcartadmin/models/awebcartcompressor.php";
if(file_exists($comprpath))
    require_once($comprpath);


class plgSystemaWeb_Cart_AutoSave extends JPlugin
{
	
	public function onAfterRoute()
	{
		$this->saveCart();	
	}

	protected function get_date()
	{
		$config =& JFactory::getConfig();
		$dtz = new DateTimeZone('GMT');
		$date = new DateTime(NULL, $dtz);
		return $date->format('Y-m-d H:i:s');		
	}

	function saveCart()
	{
		$user =& JFactory::getUser();
		$userid = $user->get('id');
		if ($userid!=0)
		{
			if (isset($_SESSION['__vm']))
			{
				$db = JFactory::getDBO();
				if (isset($_SESSION['__vm']["vmcart"]))
				{
					$rawdata =  $_SESSION['__vm']["vmcart"];
					$cart = unserialize($rawdata);	
					$cartsize = count($cart->products);					
					if ($cartsize>0)
					{		
						$db = JFactory::getDBO();
						if ($db->name == "mysql") $data = mysql_real_escape_string( $rawdata);
						else $data = mysqli_real_escape_string($db->getConnection(), $rawdata); 
						$now = $this->get_date();		
						$compr = 0;
						if (defined('_AWEB_CART_ADMIN')) {
							$mycompressor = new awebcartcompressor();
							$compr = $mycompressor->hasbz(); 
							if ($compr == 1) $data = $mycompressor->compress($rawdata);			
						}

						$q="INSERT INTO ".$db->getPrefix()."awebsavedcart (userid,data,date,compr) VALUES ('".$userid."','".$data."','".$now."','".$compr."')";
						$q.=" ON DUPLICATE KEY UPDATE data='".$data."',date='".$now."',compr='".$compr."'";    
						$db->setQuery($q);
						$db->query();	
					}
					else {
						$q="DELETE FROM ".$db->getPrefix()."awebsavedcart where userid='".$userid."'";
						$db->setQuery($q);
						$db->query();																		
					}
				}
			}
		}
	}
	
}