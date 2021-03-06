<?php
/**
 * SnelStart export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: orderexport.php 1994 2012-05-22 06:18:05Z RolandD $
 */

defined('_JEXEC') or die;

class CsviModelSnelStartExport extends CsviModelExportfile {

	/**
	 * SnelStart export
	 *
	 * Exports order details
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function getStart() {

		// Get some basic data
		$db = JFactory::getDbo();
		$csvidb = new CsviDb();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportclass =  $jinput->get('export.class', null, null);
		$export_fields = $jinput->get('export.fields', array(), 'array');
		$order_discount = 0;
		$coupon_code = '';
		$coupon_discount = '';

		$address = strtoupper($template->get('order_address', 'order', false));
		$user_info_fields = CsviModelAvailablefields::DbFields('virtuemart_order_userinfos');

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();

		// Order ID is needed as controller
		$userfields[] = $db->qn('#__virtuemart_orders.virtuemart_order_id');

		// Process the other export fields
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'created_by':
				case 'created_on':
				case 'locked_by':
				case 'locked_on':
				case 'modified_by':
				case 'modified_on':
				case 'order_status':
				case 'virtuemart_user_id':
				case 'virtuemart_vendor_id':
				case 'virtuemart_order_id':
				case 'virtuemart_paymentmethod_id':
				case 'virtuemart_shipmentmethod_id':
					$userfields[] = $db->qn('#__virtuemart_orders.'.$field->field_name);
					break;
				case 'email':
				case 'company':
				case 'title':
				case 'last_name':
				case 'first_name':
				case 'middle_name':
				case 'phone_1':
				case 'phone_2':
				case 'fax':
				case 'address_1':
				case 'address_2':
				case 'city':
				case 'zip':
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('BT').' THEN '.$db->qn('user_info.'.$field->field_name).' ELSE NULL END) AS '.$db->qn($field->field_name);
					break;
				case 'id':
					$userfields[] = $db->qn('#__users.'.$field->field_name);
					break;
				case 'payment_element':
					$userfields[] = $db->qn('#__virtuemart_orders.virtuemart_paymentmethod_id');
					break;
				case 'shipment_element':
					$userfields[] = $db->qn('#__virtuemart_orders.virtuemart_shipmentmethod_id');
					break;
				case 'state_2_code':
				case 'state_3_code':
				case 'state_name':
					$userfields[] = $db->qn('user_info.virtuemart_state_id', $field->field_name);
					break;
				case 'country_2_code':
				case 'country_3_code':
				case 'country_name':
				case 'virtuemart_country_id':
					$userfields[] = $db->qn('user_info.virtuemart_country_id', $field->field_name);
					break;
				case 'user_currency':
					$userfields[] = $db->qn('#__virtuemart_orders.user_currency_id');
					break;
				case 'username':
					$userfields[] = $db->qn('#__virtuemart_orders.virtuemart_user_id');
					break;
				case 'full_name':
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('BT').' THEN '.$db->qn('user_info.first_name').' ELSE NULL END) AS '.$db->qn('first_name');
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('BT').' THEN '.$db->qn('user_info.middle_name').' ELSE NULL END) AS '.$db->qn('middle_name');
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('BT').' THEN '.$db->qn('user_info.last_name').' ELSE NULL END) AS '.$db->qn('last_name');
					break;
				case 'product_price_total':
					$userfields[] = 'product_item_price*product_quantity AS product_price_total';
					break;
				case 'discount_percentage':
					$userfields[] = '(order_discount/order_total)*100 AS discount_percentage';

					break;
				case 'product_subtotal_discount_percentage':
					$userfields[] = $db->qn('#__virtuemart_order_items.product_basePriceWithTax');
					$userfields[] = $db->qn('#__virtuemart_order_items.product_final_price');
					$userfields[] = $db->qn('#__virtuemart_order_items.product_subtotal_discount');
					break;
				case 'shipping_company':
				case 'shipping_title':
				case 'shipping_last_name':
				case 'shipping_first_name':
				case 'shipping_middle_name':
				case 'shipping_phone_1':
				case 'shipping_phone_2':
				case 'shipping_fax':
				case 'shipping_address_1':
				case 'shipping_address_2':
				case 'shipping_city':
				case 'shipping_zip':
				case 'shipping_email':
					$name = str_ireplace('shipping_', '', $field->field_name);
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.'.$name).' ELSE NULL END) AS '.$db->qn($field->field_name);
					break;
				case 'shipping_state_name':
				case 'shipping_state_2_code':
				case 'shipping_state_3_code':
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.virtuemart_state_id').' ELSE NULL END) AS '.$db->qn($field->field_name);
					break;
				case 'shipping_country_name':
				case 'shipping_country_2_code':
				case 'shipping_country_3_code':
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.virtuemart_country_id').' ELSE NULL END) AS '.$db->qn($field->field_name);
					break;
				case 'shipping_full_name':
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.first_name').' ELSE NULL END) AS '.$db->qn('shipping_first_name');
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.middle_name').' ELSE NULL END) AS '.$db->qn('shipping_middle_name');
					$userfields[] = 'MAX(CASE WHEN '.$db->qn('user_info.address_type').' = '.$db->q('ST').' THEN '.$db->qn('user_info.last_name').' ELSE NULL END) AS '.$db->qn('shipping_last_name');
					break;
				case 'coupon_discount':
					// Coupon fields
					$userfields[] = $db->qn('#__virtuemart_orders.coupon_discount');
					$userfields[] = $db->qn('#__virtuemart_orders.coupon_code');
					break;
				case 'total_order_items':
				case 'custom':
					// These are man made fields, do not try to get them from the database
					break;
				default:
					$userfields[] = $db->qn($field->field_name);
					break;
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from('#__virtuemart_orders');
		$query->leftJoin('#__virtuemart_order_items ON #__virtuemart_orders.virtuemart_order_id = #__virtuemart_order_items.virtuemart_order_id');
		$query->leftJoin('#__virtuemart_order_userinfos AS user_info ON #__virtuemart_orders.virtuemart_order_id = user_info.virtuemart_order_id');
		$query->leftJoin('#__virtuemart_orderstates ON #__virtuemart_orders.order_status = #__virtuemart_orderstates.order_status_code');
		$query->leftJoin('#__virtuemart_product_manufacturers ON #__virtuemart_order_items.virtuemart_product_id = #__virtuemart_product_manufacturers.virtuemart_product_id');
		$query->leftJoin('#__virtuemart_manufacturers ON #__virtuemart_product_manufacturers.virtuemart_manufacturer_id = #__virtuemart_manufacturers.virtuemart_manufacturer_id');
		$query->leftJoin('#__users ON #__users.id = user_info.virtuemart_user_id');
		$query->leftJoin('#__virtuemart_countries ON #__virtuemart_countries.virtuemart_country_id = user_info.virtuemart_country_id');
		$query->leftJoin('#__virtuemart_invoices ON #__virtuemart_orders.virtuemart_order_id = #__virtuemart_invoices.virtuemart_order_id');
		$query->leftJoin('#__virtuemart_paymentmethods_'.$template->get('language', 'general').' ON #__virtuemart_orders.virtuemart_paymentmethod_id = #__virtuemart_paymentmethods_'.$template->get('language', 'general').'.virtuemart_paymentmethod_id');
		$query->leftJoin('#__virtuemart_shipmentmethods_'.$template->get('language', 'general').' ON #__virtuemart_orders.virtuemart_shipmentmethod_id = #__virtuemart_shipmentmethods_'.$template->get('language', 'general').'.virtuemart_shipmentmethod_id');

		// Check if there are any selectors
		$selectors = array();

		// Filter by manufacturer
		$manufacturer = $template->get('ordermanufacturer', 'order', false);
		if ($manufacturer && $manufacturer[0] != 'none') {
			$selectors[] = '#__virtuemart_manufacturers.virtuemart_manufacturer_id IN ('.implode(',', $manufacturer).')';
		}

		// Filter by order number start
		$ordernostart = $template->get('ordernostart', 'order', 0, 'int');
		if ($ordernostart > 0) {
			$selectors[] = '#__virtuemart_orders.virtuemart_order_id >= '.$ordernostart;
		}

		// Filter by order number end
		$ordernoend = $template->get('ordernoend', 'order', 0, 'int');
		if ($ordernoend > 0) {
			$selectors[] = '#__virtuemart_orders.virtuemart_order_id <= '.$ordernoend;
		}

		// Filter by list of order numbers
		$orderlist = $template->get('orderlist', 'order');
		if ($orderlist) {
			$selectors[] = '#__virtuemart_orders.virtuemart_order_id IN ('.$orderlist.')';
		}

		// Check for a pre-defined date
		$daterange = $template->get('orderdaterange', 'order', '');
		if ($daterange != '') {
			$jdate = JFactory::getDate();
			switch ($daterange) {
				case 'yesterday':
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)';
					break;
				case 'thisweek':
					// Get the current day of the week
					$dayofweek = $jdate->__get('dayofweek');
					$offset = $dayofweek - 1 ;
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= DATE_SUB(CURDATE(), INTERVAL '.$offset.' DAY)';
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) <= CURDATE()';
					break;
				case 'lastweek':
					// Get the current day of the week
					$dayofweek = $jdate->__get('dayofweek');
					$offset = $dayofweek + 6 ;
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= DATE_SUB(CURDATE(), INTERVAL '.$offset.' DAY)';
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) <= DATE_SUB(CURDATE(), INTERVAL '.$dayofweek.' DAY)';
					break;
				case 'thismonth':
					// Get the current day of the week
					$dayofmonth = $jdate->__get('day');
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= DATE_SUB(CURDATE(), INTERVAL '.$dayofmonth.' DAY)';
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) <= CURDATE()';
					break;
				case 'lastmonth':
					// Get the current day of the week
					$dayofmonth = $jdate->__get('day');
					$month = date('n');
					$year = date('y');
					if ($month > 1) $month--;
					else {
						$month = 12;
						$year--;
					}
					$daysinmonth = date('t', mktime(0,0,0,$month,25,$year));
					$offset = ($daysinmonth + $dayofmonth) - 1;

					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= DATE_SUB(CURDATE(), INTERVAL '.$offset.' DAY)';
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) <= DATE_SUB(CURDATE(), INTERVAL '.$dayofmonth.' DAY)';
					break;
				case 'thisquarter':
					// Find out which quarter we are in
					$month = $jdate->__get('month');
					$year = date('Y');
					$quarter = ceil($month/3);
					switch ($quarter) {
						case '1':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-01-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-04-01');
							break;
						case '2':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-04-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-07-01');
							break;
						case '3':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-07-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-10-01');
							break;
						case '4':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-10-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year++.'-01-01');
							break;
					}
					break;
				case 'lastquarter':
					// Find out which quarter we are in
					$month = $jdate->__get('month');
					$year = date('Y');
					$quarter = ceil($month/3);
					if ($quarter == 1) {
						$quarter = 4;
						$year--;
					}
					else {
						$quarter--;
					}
					switch ($quarter) {
						case '1':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-01-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-04-01');
							break;
						case '2':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-04-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-07-01');
							break;
						case '3':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-07-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-10-01');
							break;
						case '4':
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-10-01');
							$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year++.'-01-01');
							break;
					}
					break;
				case 'thisyear':
					$year = date('Y');
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-01-01');
					$year++;
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-01-01');
					break;
				case 'lastyear':
					$year = date('Y');
					$year--;
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) >= '.$db->quote($year.'-01-01');
					$year++;
					$selectors[] = 'DATE(#__virtuemart_orders.created_on) < '.$db->quote($year.'-01-01');
					break;
			}
		}
		else {
			// Filter by order date start
			$orderdatestart = $template->get('orderdatestart', 'order', false);
			if ($orderdatestart) {
				$orderdate = JFactory::getDate($orderdatestart);
				$selectors[] = $db->quoteName('#__virtuemart_orders').'.'.$db->quoteName('created_on').' >= '.$db->Quote($orderdate->toMySQL());
			}

			// Filter by order date end
			$orderdateend = $template->get('orderdateend', 'order', false);
			if ($orderdateend) {
				$orderdate = JFactory::getDate($orderdateend);
				$selectors[] = $db->quoteName('#__virtuemart_orders').'.'.$db->quoteName('created_on').' <= '.$db->Quote($orderdate->toMySQL());
			}

			// Filter by order modified date start
			$ordermdatestart = $template->get('ordermdatestart', 'order', false);
			if ($ordermdatestart) {
				$ordermdate = JFactory::getDate($ordermdatestart);
				$selectors[] = $db->quoteName('#__virtuemart_orders').'.'.$db->quoteName('modified_on').' >= '.$db->Quote($ordermdate->toMySQL());
			}

			// Filter by order modified date end
			$ordermdateend = $template->get('ordermdateend', 'order', false);
			if ($ordermdateend) {
				$ordermdate = JFactory::getDate($ordermdateend);
				$selectors[] = $db->quoteName('#__virtuemart_orders').'.'.$db->quoteName('modified_on').' <= '.$db->Quote($ordermdate->toMySQL());
			}
		}

		// Filter by order status
		$orderstatus = $template->get('orderstatus', 'order', false);
		if ($orderstatus && $orderstatus[0] != '') {
			$selectors[] = '#__virtuemart_orders.order_status IN (\''.implode("','", $orderstatus).'\')';
		}

		// Filter by order price start
		$pricestart = $template->get('orderpricestart', 'order', false, 'float');
		if ($pricestart) {
			$selectors[] = '#__virtuemart_orders.order_total >= '.$pricestart;
		}

		// Filter by order price end
		$priceend = $template->get('orderpriceend', 'order', false, 'float');
		if ($priceend) {
			$selectors[] = '#__virtuemart_orders.order_total <= '.$priceend;
		}

		// Filter by order user id
		$orderuser = $template->get('orderuser', 'order', false);
		if ($orderuser && $orderuser[0] != '') {
			$selectors[] = '#__virtuemart_orders.virtuemart_user_id IN (\''.implode("','", $orderuser).'\')';
		}

		// Filter by order product
		$orderproduct = $template->get('orderproduct', 'order', false);
		if ($orderproduct && $orderproduct[0] != '') {
			$selectors[] = '#__virtuemart_order_items.order_item_sku IN (\''.implode("','", $orderproduct).'\')';
		}

		// Filter by address type
		if ($address) {
			switch (strtoupper($address)) {
				case 'BTST':
					$selectors[] = "user_info.address_type = 'BT'";
					break;
				default:
					$selectors[] = 'user_info.address_type = '.$db->q(strtoupper($address));
					break;
			}
		}

		// Filter by order currency
		$ordercurrency = $template->get('ordercurrency', 'order', false);
		if ($ordercurrency && $ordercurrency[0] != '') {
			$selectors[] = '#__virtuemart_orders.order_currency IN (\''.implode("','", $ordercurrency).'\')';
		}

		// Filter by payment method
		$orderpayment = $template->get('orderpayment', 'order', false);
		if ($orderpayment && $orderpayment[0] != '') {
			$selectors[] = '#__virtuemart_orders.virtuemart_paymentmethod_id IN (\''.implode("','", $orderpayment).'\')';
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0) $query->where(implode("\n AND ", $selectors));

		// Check if we need to group the orders together
		$query->group($db->qn('#__virtuemart_orders.virtuemart_order_id'));

		// Order by set field
		$orderby = $this->getFilterBy('sort', $address, $user_info_fields);
		if (!empty($orderby)) $query->order($orderby);

		// Add a limit if user wants us to
		$limits = $this->getExportLimit();

		// Execute the query
		$csvidb->setQuery($query, $limits['offset'], $limits['limit']);
		$csvilog->addDebug(JText::_('COM_CSVI_EXPORT_QUERY'), true);
		// There are no records, write SQL query to log
		if (!is_null($csvidb->getErrorMsg())) {
			$this->addExportContent(JText::sprintf('COM_CSVI_ERROR_RETRIEVING_DATA', $csvidb->getErrorMsg()));
			$this->writeOutput();
			$csvilog->AddStats('incorrect', $csvidb->getErrorMsg());
		}
		else {
			$logcount = $csvidb->getNumRows();
			$jinput->set('logcount', $logcount);
			if ($logcount > 0) {
				$linenumber = 1;
				$orderid = null;
				while ($record = $csvidb->getRow()) {
					$csvilog->setLinenumber($linenumber++);

					// Add an order
					if (is_null($orderid) || $record->virtuemart_order_id != $orderid) {
						if (!is_null($orderid)) {
							// Output the contents
							$this->addExportContent($exportclass->NodeEnd());
							$this->writeOutput();
						}
						$orderid = $record->virtuemart_order_id;
						$this->addExportContent($exportclass->Order());
					}
					// Add an orderline
					$this->addExportContent($exportclass->Orderline());

					foreach ($export_fields as $column_id => $field) {
						if ($field->process) {
							$fieldname = $field->field_name;
							// Add the replacement
							if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
							else $fieldvalue = '';
							switch ($fieldname) {
								case 'payment_element':
									$query = $db->getQuery(true);
									$query->select($fieldname);
									$query->from('#__virtuemart_paymentmethods');
									$query->where('virtuemart_paymentmethod_id = '.$record->virtuemart_paymentmethod_id);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'shipment_element':
									$query = $db->getQuery(true);
									$query->select($fieldname);
									$query->from('#__virtuemart_shipmentmethods');
									$query->where('virtuemart_shipmentmethod_id = '.$record->virtuemart_shipmentmethod_id);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'state_2_code':
								case 'state_3_code':
								case 'state_name':
								case 'shipping_state_2_code':
								case 'shipping_state_3_code':
								case 'shipping_state_name':
									$query = $db->getQuery(true);
									$query->select(str_ireplace('shipping_', '', $fieldname));
									$query->from('#__virtuemart_states');
									$query->where('virtuemart_state_id = '.$record->$fieldname);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'country_2_code':
								case 'country_3_code':
								case 'country_name':
								case 'shipping_country_2_code':
								case 'shipping_country_3_code':
								case 'shipping_country_name':
									$query = $db->getQuery(true);
									$query->select(str_ireplace('shipping_', '', $fieldname));
									$query->from('#__virtuemart_countries');
									$query->where('virtuemart_country_id = '.$record->$fieldname);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'user_currency':
									$query = $db->getQuery(true);
									$query->select('currency_code_3');
									$query->from('#__virtuemart_currencies');
									$query->where('virtuemart_currency_id = '.$record->user_currency_id);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'user_email':
									$fieldvalue = CsviHelper::replaceValue($field->replace, $record->email);
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'user_id':
									$fieldvalue = CsviHelper::replaceValue($field->replace, $record->virtuemart_user_id);
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'created_on':
								case 'modified_on':
								case 'locked_on':
									$date = JFactory::getDate($record->$fieldname);
									$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'address_type':
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									if ($fieldvalue == 'BT') $fieldvalue = JText::_('COM_CSVI_BILLING_ADDRESS');
									else if ($fieldvalue == 'ST') $fieldvalue = JText::_('COM_CSVI_SHIPPING_ADDRESS');
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'full_name':
									$fieldvalue = str_replace('  ', ' ', $record->first_name.' '.$record->middle_name.' '.$record->last_name);
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'shipping_full_name':
									$fieldvalue = str_replace('  ', ' ', $record->shipping_first_name.' '.$record->shipping_middle_name.' '.$record->shipping_last_name);
									if (empty($fieldvalue)) $fieldvalue = str_replace('  ', ' ', $record->first_name.' '.$record->middle_name.' '.$record->last_name);
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'total_order_items':
									$query = $db->getQuery(true);
									$query->select('COUNT(virtuemart_order_id) AS totalitems');
									$query->from('#__virtuemart_order_items');
									$query->where('virtuemart_order_id = '.$record->virtuemart_order_id);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'custom':
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'username':
									$query = $db->getQuery(true);
									$query->select($fieldname);
									$query->from('#__users');
									$query->where('id = '.$record->virtuemart_user_id);
									$db->setQuery($query);
									$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'order_tax':
								case 'order_total':
								case 'order_subtotal':
								case 'order_shipment':
								case 'order_shipment_tax':
								case 'order_payment':
								case 'order_payment_tax':
								case 'order_discount':
								case 'user_currency_rate':
								case 'product_price_total':
								case 'discount_percentage':
									$fieldvalue =  number_format($fieldvalue, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
									$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'product_subtotal_discount_percentage':
									if ($record->product_basePriceWithTax > 0) $fieldvalue = number_format(($record->product_subtotal_discount/$record->product_basePriceWithTax*100)*-1, 3);
									else number_format(($record->product_subtotal_discount/$record->product_final_price*100)*-1, 3);
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								case 'order_discountAmount':
									// Set some discounts
									$order_discount = number_format($fieldvalue*-1, 3);
									$record->output[$column_id] = null;
									break;
								case 'coupon_discount':
									// For coupon field usage
									if ($record->coupon_discount > 0) {
										$coupon_code = $record->coupon_code;
										$coupon_discount = number_format($record->coupon_discount*-1, 3);
									}

									$fieldvalue =  number_format($fieldvalue, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
									$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
								default:
									//if strpos($fieldname, 'shipping_')
									// Check if we have any content otherwise use the default value
									if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
									$record->output[$column_id] = $fieldvalue;
									break;
							}
						}
					}
					// Output the data
					$this->addExportFields($record);

					// Add the order discount row
					if (!empty($order_discount)) {
						$record = new stdClass();
						foreach ($export_fields as $column_id => $field) {
							$value = null;
							switch ($field->field_name) {
								case 'order_item_sku':
									$value = 'Korting';
									break;
								case 'order_item_name':
									$value = 'Korting';
									break;
								case 'product_quantity':
									$value = '1';
									break;
								case 'product_item_price':
									$value = ($order_discount) ? $order_discount : 0;
									$order_discount = 0;
									break;
							}
							$record->output[$column_id] = $value;
						}
						$this->addExportContent($exportclass->Orderline());
						$this->addExportFields($record);
					}

					// Add the coupon discount
					if (!empty($coupon_code) && !empty($coupon_discount)) {
						$record = new stdClass();
						foreach ($export_fields as $column_id => $field) {
							$value = null;
							switch ($field->field_name) {
								case 'order_item_sku':
									$value = $coupon_code;
									break;
								case 'order_item_name':
									$value = $coupon_code;
									break;
								case 'product_quantity':
									$value = '1';
									break;
								case 'product_item_price':
									$value = ($coupon_discount) ? $coupon_discount : 0;
									break;
							}
							$record->output[$column_id] = $value;
						}
						$this->addExportContent($exportclass->Orderline());
						$this->addExportFields($record);
					}

					// Clean the totalitems
					JRequest::setVar('total_order_items', 0);
				}

				// Close the XML structure
				$this->addExportContent($exportclass->NodeEnd());
				$this->writeOutput();
			}
			else {
				$this->addExportContent(JText::_('COM_CSVI_NO_DATA_FOUND'));
				// Output the contents
				$this->writeOutput();
			}
		}
	}

	/**
	 * Create an SQL filter
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param 		string	$filter	what kind of SQL type should be created
	 * @return 		string	the SQL part to add to the query
	 * @since 		3.0
	 */
	protected function getFilterBy($filter, $address, $user_info_fields) {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$export_fields = $jinput->get('export.fields', array(), 'array');
		$fields = array();

		foreach ($export_fields as $column_id => $field) {
			switch ($filter) {
				case 'groupby':
					$process = true;
					break;
				case 'sort':
					$process = $field->sort;
					break;
				default:
					$process = false;
			}
			if ($process) {
				switch ($field->field_name) {
					case 'custom':
					case 'total_order_items':
					case 'discount_percentage':
					case 'product_price_total':
					case 'full_name':
					case 'payment_element':
					case 'shipment_element':
					case 'state_2_code':
					case 'state_3_code':
					case 'state_name':
					case 'country_2_code':
					case 'country_3_code':
					case 'country_name':
					case 'user_currency':
					case 'user_email':
					case 'user_id':
					case 'virtuemart_country_id':
					case 'shipping_company':
					case 'shipping_title':
					case 'shipping_last_name':
					case 'shipping_first_name':
					case 'shipping_middle_name':
					case 'shipping_phone_1':
					case 'shipping_phone_2':
					case 'shipping_fax':
					case 'shipping_address_1':
					case 'shipping_address_2':
					case 'shipping_city':
					case 'shipping_zip':
					case 'shipping_email':
					case 'shipping_state_name':
					case 'shipping_state_2_code':
					case 'shipping_state_3_code':
					case 'shipping_country_name':
					case 'shipping_country_2_code':
					case 'shipping_country_3_code':
					case 'shipping_full_name':
						break;
					case 'user_id':
						$fields[] = $db->qn('#__virtuemart_orders').'.'.$db->qn('virtuemart_user_id');
						break;
					case 'product_price':
						$fields[] = $db->qn('product_item_price');
						break;
					case 'ordering':
						$fields[] = $db->qn('#__virtuemart_orderstates').'.'.$db->qn('ordering');
						break;
					default:
						if (preg_match("/".$field->field_name."/i", join(",", array_keys($user_info_fields)))) {
							$fields[] = $db->qn('user_info.'.$field->field_name);
						}
						else $fields[] = $db->qn($field->field_name);
						break;
				}
			}
		}

		// Construct the SQL part
		if (!empty($fields)) {
			switch ($filter) {
				case 'groupby':
					$groupby_fields = array_unique($fields);
					$q = implode(',', $groupby_fields);
					break;
				case 'sort':
					$sort_fields = array_unique($fields);
					$q = implode(', ', $sort_fields);
					break;
				default:
					$q = '';
					break;
			}
		}
		else $q = '';

		return $q;
	}
}