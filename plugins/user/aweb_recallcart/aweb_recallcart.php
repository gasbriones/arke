<?php
/**
 * @package Plugin aWeb_Recall_Cart for Joomla! 2.5
 * @version 1.76
 * @author aWebSupport Team
 * @copyright (C) 2013- aWebSupport.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die( 'Restricted access' );
$path = dirname(__FILE__);
$path = substr($path,0,strpos($path,"plugins/user/aweb_recallcart")-1);
$comprpath = JPATH_BASE ."/administrator/components/com_awebcartadmin/models/awebcartcompressor.php";
if(file_exists($comprpath))
    require_once($comprpath);

class plgUseraWeb_RecallCart extends JPlugin {

	public function onUserLogin ($user, $options = array())	
	{
		$this->recallCart();		
		return true;
	}

	protected function debug($message)
	{
		$debugmode = $this->params->get('aweb_debugmode');
		if ($debugmode==1)
		{
			$fname = "aweblog.htm";
			$fp = fopen($fname, 'a');
			$now = date("Y-m-d H:i:s");
			fwrite($fp, $now."\n\r");
			fwrite($fp, $message."\n\r");
			fclose($fp);
		}		
	}

	protected function is_published($pruductid) 
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
 		$query = "SELECT published from ".$db->getPrefix()."virtuemart_products where virtuemart_product_id=".$pruductid;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			if ($row->published == 0) return false;
		}
		return true;
	}
	protected function get_stock_info($pruductid,$incart)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
 		$query = "SELECT product_in_stock, 	published from ".$db->getPrefix()."virtuemart_products where virtuemart_product_id=".$pruductid;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			$instock = $row->product_in_stock;
			if ($row->published == 0) $instock = 0;
		}		
		$stoccount = 0;
		if ($instock < $incart) $stoccount = $instock;
		else $stoccount=$incart;
		if ($stoccount<0) $stoccount=0;
		return $stoccount;
	}
	
	private function getOrdStatus()
	{
		$completed="";
		$completed = $this->params->get('aweb_completed_status_flag');
		if ($completed=="") $completed="C";		
		return $completed;
	}

	protected function is_ordered_cart($uid,$cartdate)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT order_status as ords,o.created_on as date ";
		$query.= "FROM ".$db->getPrefix()."virtuemart_orders o ";
		$query.= "WHERE o.created_by=$uid and o.modified_on>='$cartdate'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			// if ($row->ords==$this->getOrdStatus()) return true;
			if (strpbrk($this->getOrdStatus(), $row->ords)) return true;
		}
		return false;
		
	}
	protected function stock_check(&$products)
	{
		if (count($products)==0) return "empty";		
		foreach ($products as $k => $row)
		{
			$amount = 0;
			$aok = 0;
			$qok = 0;

			if (is_numeric($row->quantity)) { $qok=1; $amount = $row->quantity; }
			if (is_numeric($row->amount)) { $aok=1; $amount = $row->amount; }
			
			if ($this->params->get('aweb_manage_stock')==1) {
				$available = $this->get_stock_info($row->virtuemart_product_id,$amount);
			}
			else
			{
				$available = $this->is_published($row->virtuemart_product_id);
				if ($available!=0) $available = $amount;	
			}
			if ($available==0) {
				unset($products[$k]);
			}
			else {
				if ($aok==1) $products[$k]->amount = intval($available);	
				if ($qok==1) $products[$k]->quantity= intval($available);		
			}
		}
	}

	protected function check_cart($cart)
	{
		$mycart = unserialize($cart);	
		if (!isset($mycart->vendorId)) $mycart->vendorId = 1;		
		$this->stock_check($mycart->products);
		$cart = serialize($mycart);
		return $cart;		
	}

	protected function recallCart()
	{	
			if (!isset($_SESSION['__vm'])) $_SESSION['__vm']["vmcart"]="";
			if (!isset($_SESSION['__vm']["vmcart"])) $_SESSION['__vm']["vmcart"]="";
			$db = JFactory::getDBO();
			$user =& JFactory::getUser();
			$userid = $user->get('id');
//			$cart = VirtueMartCart::getCart();
			$this->debug("user: ".$userid."\n\rcart: ".$_SESSION['__vm']["vmcart"]);

			$query = $db->getQuery(true);
			$query->select('data,compr,date');
			$query->from($db->getPrefix()."awebsavedcart");
			$query->where('userid ='.$userid);
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach ( $rows as $row ) {
				if ($_SESSION['__vm']["vmcart"]=="" || strpos($_SESSION['__vm']["vmcart"],'"products";a:0')!==false){
						
						$data = $row->data;
						if ($row->compr==1 && defined('_AWEB_CART_ADMIN')){
							$mycompressor = new awebcartcompressor();
							$data = $mycompressor->decompress($row->data);							
						}						
						
						if (!$this->is_ordered_cart($userid,$row->date))
						{
							$newcart = $this->check_cart($data);							
							$_SESSION['__vm']["vmcart"] =  $newcart;
						}
				}			
				$this->debug("dbdata ".$row->data);
			}				
	}


}
