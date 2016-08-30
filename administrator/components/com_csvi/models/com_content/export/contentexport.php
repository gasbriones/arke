<?php
/**
 * Joomla Content export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsexport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for Joomla Content exports
 */
class CsviModelContentExport extends CsviModelExportfile {

	/**
	 * Content export
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
		$sef = new CsviSef();

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			switch ($field->field_name) {
				case 'category_path':
					$userfields[] = $db->qn('c.catid');
					break;
				case 'article_url':
					$userfields[] = $db->qn('c.id');
					$userfields[] = $db->qn('c.catid');
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
		$query->from($db->qn("#__content", "c"));

		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state != '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = $db->qn('c.published').' = '.$publish_state;
		}

		// Filter by language
		$language = $template->get('content_language', 'general');
		if ($language != '*') {
			$selectors[] = $db->qn('c.language').' = '.$db->q($language);
		}

		// Filter by category
		$categories = $template->get('content_categories', 'content');
		if ($categories && $categories[0] != '*') {
			$selectors[] = $db->qn('catid')." IN ('".implode("','", $categories)."')";
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Ingore fields
		$ignore = array('custom', 'category_path', 'article_url');

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
							case 'category_path':
								$query->clear();
								$query->select('path')->from('#__categories')->where('id = '.$record->catid);
								$db->setQuery($query);
								$fieldvalue = $db->loadResult();
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								$record->output[$column_id] = $fieldvalue;
								break;
							case 'article_url':
								// Let's create a SEF URL
								$_SERVER['QUERY_STRING'] = 'option=com_content&view=article&id='.$record->id.'&catid='.$record->catid.'&Itemid='.$template->get('j_itemid', 'product', 1, 'int');
								$article_url = $sef->getSiteRoute('index.php?'.$_SERVER['QUERY_STRING']);

								// Check for https, replace with http. https has unnecessary overhead
								if (substr($article_url, 0, 5) == 'https') $article_url = 'http'.substr($article_url, 5);
								$article_url = CsviHelper::replaceValue($field->replace, $article_url);
								$record->output[$column_id] = $article_url;
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