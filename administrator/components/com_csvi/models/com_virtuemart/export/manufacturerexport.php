<?php
/**
 * Manufacturer details export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: manufacturerexport.php 2349 2013-02-28 11:39:26Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for manufacturer details exports
 *
 */
class CsviModelManufacturerExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Manufacturer details export
	 *
	 * Exports manufacturer details data to either csv, xml or HTML format
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
		$db = JFactory::getDBO();
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
					case 'virtuemart_manufacturer_id':
					case 'mf_name_trans':
						$userfields[] = $db->qn('#__virtuemart_manufacturers.virtuemart_manufacturer_id');
						break;
					case 'file_url':
					case 'file_url_thumb':
						$userfields[] = '#__virtuemart_manufacturer_medias.virtuemart_media_id';
						break;
					case 'mf_category_name':
						break;
					default:
						$userfields[] = $db->qn($field->field_name);
						break;
				}
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from('#__virtuemart_manufacturers');
		$query->leftJoin('#__virtuemart_manufacturers_'.$template->get('language', 'general').' ON #__virtuemart_manufacturers_'.$template->get('language', 'general').'.virtuemart_manufacturer_id = #__virtuemart_manufacturers.virtuemart_manufacturer_id');
		$query->leftJoin('#__virtuemart_manufacturer_medias ON #__virtuemart_manufacturer_medias.virtuemart_manufacturer_id = #__virtuemart_manufacturers.virtuemart_manufacturer_id');

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = '#__virtuemart_manufacturers.published = '.$db->Quote($publish_state);
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Fields to ignore
		$ignore = array('mf_category_name', 'mf_name_trans', 'file_url','file_url_thumb');

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
							case 'mf_category_name':
								$query = $db->getQuery(true);
								$query->select('mf_category_name');
								$query->from('#__virtuemart_manufacturercategories_'.$template->get('language', 'general'));
								$query->where('virtuemart_manufacturercategories_id = '.$record->virtuemart_manufacturercategories_id);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'mf_name_trans':
								$query = $db->getQuery(true);
								$query->select('mf_name');
								$query->from('#__virtuemart_manufacturers_'.$template->get('target_language', 'general'));
								$query->where('virtuemart_manufacturer_id = '.$record->virtuemart_manufacturer_id);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'file_url':
							case 'file_url_thumb':
								$query = $db->getQuery(true);
								$query->select($fieldname);
								$query->from('#__virtuemart_medias');
								$query->where('virtuemart_media_id = '.$record->virtuemart_media_id);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
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