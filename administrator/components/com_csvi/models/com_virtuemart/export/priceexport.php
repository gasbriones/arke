<?php
/**
 * Multiple prices export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: mediaexport.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Processor for multiple prices exports
 */
class CsviModelPriceExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Multiple prices export
	 *
	 * @copyright
	 * @author		RolandD
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

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			if ($field->process) {
				switch ($field->field_name) {
					case 'product_sku':
						$userfields[] = $db->qn('#__virtuemart_product_prices.virtuemart_product_id');
						break;
					case 'product_name':
						$userfields[] = $db->qn('#__virtuemart_products.virtuemart_product_id');
						break;
					case 'product_currency':
						$userfields[] = $db->qn('#__virtuemart_currencies.currency_code_3');
						break;
					case 'custom':
						break;
					default:
						$userfields[] = $db->quoteName($field->field_name);
						break;
				}
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from($db->qn('#__virtuemart_product_prices'));
		$query->leftJoin($db->qn('#__virtuemart_products').' ON '.$db->qn('#__virtuemart_product_prices.virtuemart_product_id'). ' = '.$db->qn('#__virtuemart_products.virtuemart_product_id'));
		$query->leftJoin($db->qn('#__virtuemart_shoppergroups').' ON '.$db->qn('#__virtuemart_product_prices.virtuemart_shoppergroup_id'). ' = '.$db->qn('#__virtuemart_shoppergroups.virtuemart_shoppergroup_id'));
		$query->leftJoin($db->qn('#__virtuemart_currencies').' ON '.$db->qn('#__virtuemart_product_prices.product_currency'). ' = '.$db->qn('#__virtuemart_currencies.virtuemart_currency_id'));

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__virtuemart_products.published = '.$publish_state;
		}

		// Shopper group selector
		$shopper_group = $template->get('shopper_groups', 'multipleprices', array());
		if ($shopper_group && $shopper_group[0] != 'none') {
			$selectors[] = "#__virtuemart_shoppergroups.virtuemart_shoppergroup_id IN ('".implode("','", $shopper_group)."')";
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Fields to ignore
		$ignore = array('product_sku', 'product_name', 'shopper_group_name', 'product_currency');

		// Check if we need to group the orders together
		$groupby = $template->get('groupby', 'general', false, 'bool');
		if ($groupby) {
			$filter = $this->getFilterBy('groupby', $ignore);
			if (!empty($filter)) $query->group($filter);
		}

		// Order by set field
		$orderby = $this->getFilterBy('sort', $ignore);
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
				while ($record = $csvidb->getRow()) {
					$csvilog->setLinenumber($linenumber++);
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					foreach ($export_fields as $column_id => $field) {
						$fieldname = $field->field_name;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'product_sku':
								$query = $db->getQuery(true);
								$query->select('product_sku');
								$query->from('#__virtuemart_products');
								$query->where('virtuemart_product_id = '.$record->virtuemart_product_id);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'product_name':
								$query = $db->getQuery(true);
								$query->select($fieldname);
								$query->from('#__virtuemart_products_'.$template->get('language', 'general'));
								$query->where('virtuemart_product_id = '.$record->virtuemart_product_id);
								$db->setQuery($query);
								$fieldvalue = CsviHelper::replaceValue($field->replace, $db->loadResult());
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'product_price':
								$product_price =  number_format($record->product_price, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
								if (strlen(trim($product_price)) == 0) $product_price = $field->default_value;
								$product_price = CsviHelper::replaceValue($field->replace, $product_price);
								$record->output[$column_id] = $product_price;
								break;
							case 'product_price_publish_up':
							case 'product_price_publish_down':
								$date = JFactory::getDate($record->$fieldname);
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'product_currency':
								$fieldvalue = $record->currency_code_3;
								// Perform the replacement rules
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);

								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'shopper_group_name':
								// Check if the shopper group name is empty
								if (empty($field->default_value) && empty($fieldvalue)) $fieldvalue = '*';

								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							default:
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
						}
					}
					// Output the data
					$this->addExportFields($record);

					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
						$this->addExportContent($exportclass->NodeEnd());
					}

					// Output the contents
					$this->writeOutput();
				}
			}
			else {
				$this->addExportContent(JText::_('COM_CSVI_NO_DATA_FOUND'));
				// Output the contents
				$this->writeOutput();
			}
		}
	}
}