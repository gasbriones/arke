<?php
/**
 * Templates model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templates.php 2395 2013-03-24 11:43:12Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

/**
 * Templates Model
 */
class CsviModelTemplates extends JModelLegacy {

	/**
	 * Get the saved templates
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		array	list of template objects
	 * @since 		3.0
	 */
	public function getTemplates() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name AS text, id AS value, process')->from($db->qn('#__csvi_template_settings'))->order($db->qn('name'));
		$db->setQuery($query);
		$loadtemplates = $db->loadObjectList();
		if (!is_array($loadtemplates)) {
			$templates = array();
			$templates[] = JHtml::_('select.option', '', JText::_('COM_CSVI_SAVE_AS_NEW_FOR_NEW_TEMPLATE'));
		}

		$import = array();
		$export = array();
		// Group the templates by process
		if (!empty($loadtemplates)) {
			foreach ($loadtemplates as $tmpl) {
				if ($tmpl->process == 'import') $import[] = $tmpl;
				else if ($tmpl->process == 'export') $export[] = $tmpl;
			}
		}

		// Merge the whole thing together
		$templates[] = JHtml::_('select.option', '', JText::_('COM_CSVI_SELECT_TEMPLATE'));
		$templates[] = JHtml::_('select.option', '', JText::_('COM_CSVI_TEMPLATE_IMPORT'), 'value', 'text', true);
		$templates = array_merge($templates, $import);
		$templates[] = JHtml::_('select.option', '', JText::_('COM_CSVI_TEMPLATE_EXPORT'), 'value', 'text', true);
		$templates = array_merge($templates, $export);

		return $templates;
	}

	/**
	 * Save export settings
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		array	$data	the data to be stored
	 * @return 		bool	true on success | false on failure
	 * @since 		3.0
	 */
	public function save($data) {
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$table = $this->getTable('csvi_template_settings');
		$bind = array();
		$id = $jinput->get('template_id', 0, 'int');
		if ($id > 0) $table->load($id);
		else $bind['name'] = $jinput->get('template_name', 'Template '.time(), 'string');
		$bind['settings'] = json_encode($data);

		// Set the process type
		$bind['process'] = $data['options']['action'];

		// Store the template
		$table->bind($bind);
		if ($table->store()) {
			$app->enqueueMessage(JText::sprintf('COM_CSVI_PROCESS_SETTINGS_SAVED', $table->name));
			// Copy over the fields also if it is save as new
			$old_id = $jinput->get('old_template_id');
			if ($old_id) {
				$db = JFactory::getDbo();
				// Load the old fields
				$query = $db->getQuery(true);
				$query->select($db->qn('id'))
					->select($db->q($table->id).' AS '.$db->qn('template_id'))
					->select($db->qn('ordering'))
					->select($db->qn('field_name'))
					->select($db->qn('file_field_name'))
					->select($db->qn('template_field_name'))
					->select($db->qn('column_header'))
					->select($db->qn('default_value'))
					->select($db->qn('process'))
					->select($db->qn('combine_char'))
					->select($db->qn('sort'))
					->select($db->qn('cdata'))
					->from($db->qn('#__csvi_template_fields'))
					->where($db->qn('template_id').' = '.$old_id);
				$db->setQuery($query);
				$fields = $db->loadObjectList('id');

				// Store the fields in the new template
				$templatefield = $this->getTable('templatefield');
				foreach ($fields as $fid => $field) {
					// Unset the ID since we are storing a new one
					unset($field->id);
					if ($templatefield->save($field)) {
						$fields[$fid]->new_id = $templatefield->id;
					}

					// Clean the template field table
					$templatefield->reset();
					unset($templatefield->id);
				}

				// Add the combine and replace fields
				foreach ($fields as $fid => $field) {
					// Combine
					$query->clear()
						->select($db->qn('combine_id'))
						->from($db->qn('#__csvi_template_fields_combine'))
						->where($db->qn('field_id').' = '.$db->q($fid));
					$db->setQuery($query);
					$combines = $db->loadObjectList();
					if (!empty($combines)) {
						foreach ($combines as $combine) {
							$cid = $combine->combine_id;
							$query->clear()
								->insert('#__csvi_template_fields_combine')
								->values('null,'.$db->q($fields[$fid]->new_id).','.$db->q($fields[$cid]->new_id));
							$db->setQuery($query);
							$db->query();
						}
					}

					// Replace
					$query->clear()
						->select($db->qn('replace_id'))
						->from($db->qn('#__csvi_template_fields_replacement'))
						->where($db->qn('field_id').' = '.$db->q($fid));
					$db->setQuery($query);
					$replacements = $db->loadObjectList();
					if (!empty($replacements)) {
						foreach ($replacements as $replacement) {
							$rid = $replacement->replace_id;
							$query->clear()
								->insert('#__csvi_template_fields_replacement')
								->values('null,'.$db->q($fields[$fid]->new_id).','.$db->q($rid));
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}
		else {
			$app->enqueueMessage(JText::sprintf('COM_CSVI_PROCESS_SETTINGS_NOT_SAVED', $table->getError()), 'error');
		}
		return $table->id;
	}

	/**
	 * Remove a settings template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function remove() {
		$app = JFactory::getApplication();
		$jinput = JFactory::getApplication()->input;
		$table = $this->getTable('csvi_template_settings');
		$table->load($jinput->get('template_id', null, 'int'));
		if ($table->delete()) {
			$app->enqueueMessage(JText::sprintf('COM_CSVI_PROCESS_SETTINGS_DELETED', $table->name));

			// Remove the fields associated with the template
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__csvi_template_fields')->where('template_id = '.$jinput->get('template_id', null, 'int'));
			$db->setQuery($query);
			if ($db->query()) {
				$app->enqueueMessage(JText::_('COM_CSVI_PROCESS_FIELDS_DELETED'));
			}
			else {
				$app->enqueueMessage(JText::sprintf('COM_CSVI_PROCESS_FIELDS_NOT_DELETED', $table->getError()), 'error');
			}
		}
		else {
			$app->enqueueMessage(JText::sprintf('COM_CSVI_PROCESS_SETTINGS_NOT_DELETED', $table->getError()), 'error');
		}
	}

	/**
	 * Get the template details
	 *
	 * Retrieves the template details from the csvi_templates table. If the
	 * template id is 0, it will automatically retrieve the template details
	 * for the template with the lowest ID in the database
	 *
	 * @see self::GetFirstTemplateId();
	 * @param $templateid integer Template ID to retrieve
	 */
	public function _getTemplate() {
		$row = $this->getTable($this->_tablename);
		if ($this->_id == 0) {
			$this_id = $this->GetFirstTemplateId();
		}
		$row->load($this->_id);

		// Fix the price format
		$row->export_price_formats = self::getNumberFormat($row->export_price_format);
		return $row;
	}

	/**
	 * Load the template types based on type
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$type	The type of template to filter on
	 * @return 		array	list of template types
	 * @since 		3.0
	 */
	function getTemplateTypes($type=false, $component=false) {
		$db = JFactory::getDBO();
		$q = "SELECT CONCAT('COM_CSVI_', UPPER(template_type_name)) AS name, template_type_name AS value
			FROM #__csvi_template_types ";
		// Check any selectors
		$selectors = array();
		if ($type) $selectors[] = "template_type = ".$db->Quote($type);
		if ($component) $selectors[] = "component = ".$db->Quote($component);
		if (!empty($selectors)) $q .= "WHERE ".implode(' AND ', $selectors);
		// Order by name
		$q .= " ORDER BY template_type_name";
		$db->setQuery($q);
		$types = $db->loadObjectList();

		// Translate the strings
		foreach ($types as $key => $type) {
			$type->value = JText::_($type->value);
			$types[$key] = $type;
		}
		return $types;
	}
}