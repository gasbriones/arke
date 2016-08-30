<?php
/**
 * Joomla User export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: customfieldsexport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for Joomla User exports
 */
class CsviModelUserExport extends CsviModelExportfile {

	/**
	 * User export
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
				case 'fullname':
					$userfields[] = $db->qn('u.name', 'fullname');
					break;
				case 'usergroup_name':
					$userfields[] = $db->qn('id');
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
		$query->from($db->qn("#__users", "u"));

		$selectors = array();

		// Filter by published state
		$user_state = $template->get('user_state', 'user');
		if ($user_state != '*') {
			$selectors[] = $db->qn('u.block').' = '.$user_state;
		}

		// Filter by active state
		$user_active = $template->get('user_active', 'user');
		if ($user_active == '0') {
			$selectors[] = $db->qn('u.activation').' = '.$db->q('');
		}
		elseif ($user_active == '1') {
			$selectors[] = $db->qn('u.activation').' = '.$db->q('32');
		}

		// Filter by user group
		$user_groups = $template->get('user_group', 'user');
		if ($user_groups && $user_groups[0] != '*') {
			$query->join('LEFT', $db->qn('#__user_usergroup_map', 'map2').' ON '.$db->qn('map2.user_id').' = '.$db->qn('u.id'));

			if (isset($user_groups)) {
				$selectors[] = $db->qn('map2.group_id').' IN ('.implode(',', $user_groups).')';
			}
		}

		// Filter on range
		$user_range = $template->get('user_range', 'user');
		if ($user_range != '*') {

			jimport('joomla.utilities.date');

			// Get UTC for now.
			$dNow = new JDate;
			$dStart = clone $dNow;

			switch ($user_range) {
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$app	= JFactory::getApplication();
					$offset	= $app->getCfg('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart	= new JDate('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$tz = new DateTimeZone('GMT');
					$dStart->setTimezone($tz);
					break;
			}

			if ($user_range == 'post_year') {
				$selectors[] = $db->qn('u.registerDate').' < '.$db->q($dStart->format('Y-m-d H:i:s'));
			}
			else {
				$selectors[] = $db->qn('u.registerDate').' >= '.$db->q($dStart->format('Y-m-d H:i:s')).
								' AND u.registerDate <='.$db->q($dNow->format('Y-m-d H:i:s'));
			}
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Ingore fields
		$ignore = array('custom', 'fullname', 'usergroup_name');

		// Check if we need to group the users together
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
							case 'usergroup_name':
								$query = $db->getQuery(true);
								$query->select($db->qn('title'));
								$query->from($db->qn('#__usergroups'));
								$query->leftJoin($db->qn('#__user_usergroup_map').' ON '.$db->qn('#__user_usergroup_map.group_id').' = '.$db->qn('#__usergroups.id'));
								$query->where($db->qn('user_id').' = '.$record->id);
								$db->setQuery($query);
								$groups = $db->loadColumn();
								if (is_array($groups)) $fieldvalue = implode('|', $groups);
								else $fieldvalue = '';
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
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