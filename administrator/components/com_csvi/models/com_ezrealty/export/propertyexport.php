<?php
/**
 * Property export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsexport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for property exports
 */
class CsviModelPropertyExport extends CsviModelExportfile {

	/**
	 * Property tables export
	 *
	 * Exports category details data to either csv, xml or HTML format
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.4
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
		$this->_domainname = CsviHelper::getDomainName();
		$classname = 'CsviCom_Ezrealty_Config';
		if (class_exists($classname)) $config = new $classname;

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'id':
				case 'alias':
				case 'checked_out':
				case 'checked_out_time':
				case 'editor':
				case 'ordering':
				case 'published':
					$userfields[] = $db->qn('e.'.$field->field_name);
					break;
				case 'category':
					$userfields[] = $db->qn('c.name');
					break;
				case 'country':
					$userfields[] = $db->qn('cn.name', 'country');
					break;
				case 'state':
					$userfields[] = $db->qn('st.name', 'state');
					break;
				case 'city':
					$userfields[] = $db->qn('loc.ezcity', 'city');
					break;
				case 'fname':
				case 'file_title':
				case 'file_description':
				case 'file_ordering':
				case 'picture_url':
				case 'picture_url_thumb':
					$userfields[] = $db->qn('e.id');
					break;
				case 'custom':
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
		$query->from($db->qn('#__ezrealty', 'e'));
		$query->leftJoin($db->qn('#__ezrealty_catg', 'c').' ON '.$db->qn('e.cid').' = '.$db->qn('c.id'));
		$query->leftJoin($db->qn('#__ezrealty_country', 'cn').' ON '.$db->qn('e.cnid').' = '.$db->qn('cn.id'));
		$query->leftJoin($db->qn('#__ezrealty_state', 'st').' ON '.$db->qn('e.stid').' = '.$db->qn('st.id'));
		$query->leftJoin($db->qn('#__ezrealty_locality', 'loc').' ON '.$db->qn('e.locid').' = '.$db->qn('loc.id'));

		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = $db->qn('e.published').' = '.$publish_state;
		}

		// Filter by transaction type
		$transaction_type = $template->get('transaction_type', 'property');
		if ($transaction_type[0] != '') {
			$selectors[] = $db->qn('e.type').' IN ('.implode(',', $transaction_type).')';
		}

		// Filter by property type
		$property_type = $template->get('property_type', 'property');
		if ($property_type[0] != '') {
			$selectors[] = $db->qn('e.cid').' IN ('.implode(',', $property_type).')';
		}

		// Filter by street
		$street = $template->get('street', 'property');
		if ($street[0] != '') {
			$selectors[] = $db->qn('e.address2')." IN ('".implode("','", $street)."')";
		}

		// Filter by locality
		$locality = $template->get('locality', 'property');
		if ($locality[0] != '') {
			$selectors[] = $db->qn('e.locality')." IN ('".implode("','", $locality)."')";
		}

		// Filter by states
		$state = $template->get('state', 'property');
		if ($state[0] != '') {
			$selectors[] = $db->qn('e.state')." IN ('".implode("','", $state)."')";
		}

		// Filter by countries
		$country = $template->get('country', 'property');
		if ($country[0] != '') {
			$selectors[] = $db->qn('e.country')." IN ('".implode("','", $country)."')";
		}

		// Filter by owner
		$owner = $template->get('owner', 'property');
		if ($owner[0] != '') {
			$selectors[] = $db->qn('e.owner').' IN ('.implode(',', $owner).')';
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Ingore fields
		$ignore = array('custom', 'category', 'country', 'state', 'city', 'fname', 'file_title', 'file_description', 'file_ordering',
					'picture_url', 'picture_url_thumb');

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
				while ($record = $csvidb->getRow()) {
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					foreach ($export_fields as $column_id => $field) {
						$fieldname = $field->field_name;
						$fieldreplace = $field->field_name.$field->column_header;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'aucdate':
							case 'availdate':
							case 'checked_out_time':
							case 'listdate':
							case 'ohdate':
							case 'ohdate2':
								$date = JFactory::getDate($record->$fieldname);
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $date->toUnix()));
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'expdate':
							case 'lastupdate':
								$fieldvalue = CsviHelper::replaceValue($field->replace, date($template->get('export_date_format', 'general'), $record->$fieldname));
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'bond':
							case 'closeprice':
							case 'offpeak':
							case 'price':
								$fieldvalue =  number_format($fieldvalue, $template->get('export_price_format_decimal', 'general', 2, 'int'), $template->get('export_price_format_decsep', 'general'), $template->get('export_price_format_thousep', 'general'));
								if ($template->get('add_currency_to_price', 'general')) {
									$fieldvalue = $config->get('er_currencycode').' '.$fieldvalue;
								}
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'custom_title':
								// Get the custom title
								$query = $db->getQuery(true);
								$query->select($db->qn('custom_title'));
								$query->from($db->qn('#__virtuemart_customs'));
								$query->where($db->qn('virtuemart_custom_id').' = '.$db->q($record->vm_custom_id));
								$db->setQuery($query);
								$title = $db->loadResult();
								$fieldvalue = CsviHelper::replaceValue($field->replace, $title);
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'category':
								$fieldvalue = $record->name;
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'fname':
							case 'file_title':
							case 'file_description':
							case 'file_ordering':
								$query = $db->getQuery(true);
								$query->select($db->qn('i.'.str_ireplace('file_', '', $fieldname)));
								$query->from($db->qn('#__ezrealty_images', 'i'));
								$query->leftJoin($db->qn('#__ezrealty', 'e').' ON '.$db->qn('i.propid').' = '.$db->qn('e.id'));
								$query->where($db->qn('e.id').' = '.$db->q($record->id));
								$query->order('i.ordering');
								$db->setQuery($query);
								$titles = $db->loadColumn();
								if (is_array($titles)) {
									$fieldvalue = CsviHelper::replaceValue($field->replace, implode('|', $titles));
									// Check if we have any content otherwise use the default value
								}
								else $fieldvalue = '';
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'picture_url':
							case 'picture_url_thumb':
								$query = $db->getQuery(true);
								$query->select($db->qn('fname'));
								$query->from($db->qn('#__ezrealty_images'));
								$query->where($db->qn('propid').' = '.$record->id);
								$query->order($db->qn('ordering'));
								$db->setQuery($query, 0, 1);
								$fieldvalue = $db->loadResult();
								// Check if there is already a product full image
								if (strlen(trim($fieldvalue)) > 0) {
									if ($fieldname == 'picture_url') $picture_url = $this->_domainname.'/components/com_ezrealty/ezrealty/'.$fieldvalue;
									else $picture_url = $this->_domainname.'/components/com_ezrealty/ezrealty/th/'.$fieldvalue;
								}
								else $picture_url = $field->default_value;
								$picture_url = CsviHelper::replaceValue($field->replace, $picture_url);
								$record->output[$column_id] = $picture_url;
								break;
							case 'custom':
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
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