<?php
/**
 * Virtuemart Product Price table
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: product_prices.php 2383 2013-03-17 09:08:01Z RolandD $
 */

/* No direct access */
defined('_JEXEC') or die;

class TableProduct_prices extends JTable {


	/**
	 * Table constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function __construct($db) {
		parent::__construct('#__virtuemart_product_prices', 'virtuemart_product_price_id', $db );
	}

	/**
	 * Check if a price already exists
	 *
	 * Criteria for an existing price are:
	 * - product id
	 * - shopper group id
	 * - product currency
	 * - price quantity start
	 * - price quantity end
	 * - publish up
	 * - publish down
	 * If all exists, price will be updated
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return		true if price exists | false if price does not exist
	 * @since 		4.0
	 */
	public function check() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn($this->_tbl_key))
			->from($db->qn($this->_tbl))
			->where($db->qn('virtuemart_product_id').' = '.$db->q($this->virtuemart_product_id))
			->where($db->qn('virtuemart_shoppergroup_id').' = '.$db->q($this->virtuemart_shoppergroup_id))
			->where($db->qn('product_currency').' = '.$db->q($this->product_currency))
			->where($db->qn('price_quantity_start').' = '.$db->q($this->price_quantity_start))
			->where($db->qn('price_quantity_end').' = '.$db->q($this->price_quantity_end))
			->where('('.$db->qn('product_price_publish_up').' = '.(($this->product_price_publish_up) ? $db->q($this->product_price_publish_up) : $db->q('0000-00-00 00:00:00')).' OR '.$db->qn('product_price_publish_up').' IS NULL)')
			->where('('.$db->qn('product_price_publish_down').' = '.(($this->product_price_publish_down) ? $db->q($this->product_price_publish_down) : $db->q('0000-00-00 00:00:00')).' OR '.$db->qn('product_price_publish_down').' IS NULL)');
		$db->setQuery($query);
		$csvilog->addDebug('COM_CSVI_VM_PRODUCT_PRICE', true);
		$id = $db->loadResult();
		if ($id) {
			$this->virtuemart_product_price_id = $id;
			$this->load($id);
			return true;
		}
		else return false;
	}

	/**
	 * See if we can find a shopper group ID
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return		array of shopper group IDs
	 * @since 		4.0
	 * @deprecated	5.4.2
	 */
	public function getShopperGroupID() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('virtuemart_shoppergroup_id'));
		$query->from($db->qn($this->_tbl));
		$query->where($db->qn('virtuemart_product_id').' = '.$db->q($this->virtuemart_product_id));
		$db->setQuery($query);
		$shopper_groups = $db->loadColumn();
		$csvilog->addDebug('COM_CSVI_VM_PRODUCT_PRICE_SHOPPER_GROUP', true);
		return $shopper_groups;
	}
	/**
	* This function calculates the new price by adding the uploaded value
	* to the current price
	*
	* Prices can be calculated with:
	* - Add (+)
	* - Subtract (-)
	* - Divide (/)
	* - Multiply (*)
	*
	* Add and subtract support percentages
	*
	* @todo logging
	 */
	public function CalculatePrice() {
		// Get the operation
		$operation = substr($this->product_price, 0, 1);

		if (strstr('+-/*', $operation)) {
			// Get the price value
			$modify = $this->product_price;

			// Clone the current instance as we don't want the DB values overwrite the uploaded values */
			$newprice = clone $this;

			// Get the current price in the database
			$newprice->check();
			$newprice->load($this->virtuemart_product_price_id);
			$this->virtuemart_product_price_id = $newprice->virtuemart_product_price_id;

			// Set the price to calculate with
			$price = $newprice->product_price;

			// Check if we have a percentage value
			if (substr($modify, -1) == '%') {
				$modify = substr($modify, 0, -1);
				$percent = true;
			}
			else $percent = false;

			// Get the price value
			$value = substr($modify, 1);

			// Check what modification we need to do and apply it
			switch ($operation) {
				case '+':
					if ($percent) $price += $price* ($value/100);
					else $price += $value;
					break;
				case '-':
					if ($percent) $price -= $price* ($value/100);
					else $price -= $value;
					break;
				case '/':
					$price /= $value;
					break;
				case '*':
					$price*= $value;
					break;
				default:
					// Assign the current price to prevent it being overwritten
					$price = $this->product_price;
					break;
			}

			// Set the new price
			$this->product_price = $price;
		}
	}

	/**
	 * Reset the keys including primary key
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function reset() {
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v) {
			// If the property is not private, reset it.
			if (strpos($k, '_') !== 0) {
				$this->$k = NULL;
			}
		}
	}
}