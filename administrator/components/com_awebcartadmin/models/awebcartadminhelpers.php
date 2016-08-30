<?php

include "cronmode.php";
include_once "common.php";

if (!defined('_JEXEC') && $enable_cron_mode)
{
	$path = dirname(__FILE__);
	define('_JEXEC','ok');
	$path = substr($path,0,strpos($path,"administrator/components/com_awebcartadmin/models")-1);
	define('JPATH_BASE', $path );
	define( 'DS', DIRECTORY_SEPARATOR );
	require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
	require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
	// import Joomla modelitem library
	jimport('joomla.application.component.modelitem');
}

require_once('awebcartcompressor.php');
class awebcartadminhelper
{
	public function deleteRow($userid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "delete FROM ".$db->getPrefix()."awebsavedcart where userid='$userid'";
		$db->setQuery($query);
		$db->query();
		//return $query;
	}

	private function getOrdStatus()
	{
		$completed="C";
		$params = getAwebParams('aweb_recallcart');
		$completed = $params->aweb_completed_status_flag;
		if ($completed=="") $completed="C";
		return $completed;
	}

	protected function is_ordered_cart($uid,$cartdate)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT order_status as ords,o.created_on as date ";
		$query.= "FROM ".$db->getPrefix()."virtuemart_orders o ";
		$query.= "WHERE o.created_by=$uid and o.modified_on>'$cartdate'";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			if ($row->ords==$this->getOrdStatus()) return true;


		}
		return false;

	}



	public function deleteEmpty()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$msg = "Delete Empty Ready!";
		$query = "select * FROM ".$db->getPrefix()."awebsavedcart where 1";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		foreach ($results as $row)
		{
			if ($row->compr==1) {
				$mycompressor = new awebcartcompressor();
				$cart =  unserialize($mycompressor->decompress($row->data));		
			}
			else {
				$cart = unserialize($row->data);
			}			
			if (!isset($cart->products) || count($cart->products)==0) $this->deleteRow($row->userid);				
		}
		return $msg;
	}

	public function deleteOrdered()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$msg = "Delete Ordered Ready!";
		$query = "select * FROM ".$db->getPrefix()."awebsavedcart where 1";
		$db->setQuery($query);
		$results = $db->loadObjectList();
		foreach ($results as $row)
		{
			if ($row->compr==1) {
				$mycompressor = new awebcartcompressor();
				$cart =  unserialize($mycompressor->decompress($row->data));		
			}
			else {
				$cart = unserialize($row->data);
			}			
			if ($this->is_ordered_cart($row->userid,$row->date)) $this->deleteRow($row->userid);				
		}
		return $msg;
	}
	
	public function deleteSelected()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$n=0;
		$msg="";
		foreach ($_POST as $k => $v)
		{ 
			if (strpos($k,"del_")!==false) 
			{
				$this->deleteRow($v);				
				$n++;
			}
		}
		$msg .= "$n cart deleted!";
		return $msg;
	}

	public function deleteOlder($date)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			if ($date=='1week') $date = date('Y-m-d',strtotime("-1 week"));
			if ($date=='1month')$date = date('Y-m-d',strtotime("-1 month"));

			$msg = "Delete Older than $date Ready!";
			$query = "select * FROM ".$db->getPrefix()."awebsavedcart where date<'$date'";
			$db->setQuery($query);
			$results = $db->loadObjectList();
			foreach ($results as $row)
			{
				$this->deleteRow($row->userid);	
				
			}
			
			return $msg;
		}
}
?>