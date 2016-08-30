<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

include "awebcartadminhelpers.php";
include_once "awebcartcompressor.php";


/**
 * awebcartadmin 
 * version: 1.23
 */
class awebcartadminModelawebcartadmin extends JModelItem
{
        /**
         * @var string msg
         */
        protected $num;
 
        /**
         * Get the message
         * @return string The message to be displayed to the user
         */
       
		private function showcart($products)
		{
			$text="";
			if (count($products)==0) return "empty";
			foreach ($products as $row)
			{
				if (is_numeric($row->amount)) { $amount = $row->amount; }
				if (is_numeric($row->quantity)) {$amount = $row->quantity; }				
				$text.= "ID: ". $row->virtuemart_product_id. " Amount: $amount";
				$text.= " <br/>";			
			}
			return $text;
		}
		
		public function getCarts() 
        {
				// Get a db connection.
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
 				$query = "SELECT * from ".$db->getPrefix()."awebsavedcart where 1 order by date DESC";
				$db->setQuery($query);
				$results = array();
				foreach ($db->loadObjectList() as $row)
				{
					$results[$row->userid]['date'] = $row->date;
					if ($row->compr==1) {
						$mycompressor = new awebcartcompressor();
						$cart =  unserialize($mycompressor->decompress($row->data));		
					}
					else {
						$cart = unserialize($row->data);		
					}
					if (isset($cart->products)) $results[$row->userid]['prod'] = $this->showcart($cart->products);
					else $results[$row->userid]['prod'] = "empty";
					$results[$row->userid]['name'] = "";
				}
                return $results;
        }

		public function getTableSize()
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT count(*) as n,sum( length( data ) ) AS dsize FROM ".$db->getPrefix()."awebsavedcart where 1";
			$db->setQuery($query);
			$results = $db->loadObjectList();
			return $results;			
		}

		public function getCronMode()
		{
			include "cronmode.php";
			return $enable_cron_mode;			
		}
		public function deleteOlder($date)
		{
			$myhelper = new awebcartadminhelper();
			return $myhelper->deleteOlder($date);
		}

		public function deleteEmpty()
		{
			$myhelper = new awebcartadminhelper();
			return $myhelper->deleteEmpty();
		}
		
		public function deleteOrdered()
		{
			$myhelper = new awebcartadminhelper();
			return $myhelper->deleteOrdered();
		}

		public function deleteSelected()
		{
			$myhelper = new awebcartadminhelper();
			return $myhelper->deleteSelected();
		}

		public function setCronMode($mode)
		{
			if ($mode==0) $mode=1;
			else $mode=0;
			$path = dirname(__FILE__);
			$fname = $path."/cronmode.php";
			$fp = fopen($fname, 'w');
			$now = date("Y-m-d H:m:s");
			$content='<?php'."\n".'$enable_cron_mode = '.$mode.';'."\n //Last modify: $now \n".'?>';			
			fwrite($fp, $content);
			fclose($fp);
		}

}