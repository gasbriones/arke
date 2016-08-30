<?php
/**
 * Install model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: install.php 2395 2013-03-24 11:43:12Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Install Model
 */
class CsviModelInstall extends JModelLegacy {

	private $_templates = array();
	private $_results = array();
	private $_tables = array();

	/**
	 * Find the version installed
	 *
	 * Version 4 is the first version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo 		Check version from database
	 * @todo		Convert settings from INI format to JSON format
	 * @see
	 * @access 		private
	 * @param
	 * @return 		string	the version determined by the database
	 * @since 		3.0
	 */
	public function getVersion() {
		// Determine the tables in the database
		$version = $this->_getVersion();
		if (empty($version)) $version = 'current';
		return $version;
	}

	/**
	 * Start performing the upgrade
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		string	the result of the upgrade
	 * @since 		3.0
	 */
	public function getUpgrade() {
		// Get the currently installed version
		$version = $this->_translateVersion();

		// Migrate the data in the tables
		if ($this->_migrateTables($version)) $this->_results['messages'][] = JText::_('COM_CSVI_UPGRADE_OK');

		// Update the version number in the database
		$this->_setVersion();

		// Load the components
		$this->_loadComponents();

		// Send the results back
		return $this->_results;
	}

	/**
	 * Migrate the tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version being migrated from
	 * @return 		bool	true if migration is OK | false if errors occured during migration
	 * @since 		3.0
	 */
	private function _migrateTables($version) {
		$this->_convertTemplateSettings($version);
		$this->_convertTemplateFields($version);
		$this->_convertTemplates($version);
	}

	/**
	 * Convert the template settings table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateSettings($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.3':
			case '4.4':
				// Reset all process fields
					$db->setQuery('SELECT * FROM #__csvi_template_settings');
					$templates = $db->loadObjectList();
					foreach ($templates as $template) {
						$settings = json_decode($template->settings, true);
						$process = $settings['options']['action'];
						$query = $db->getQuery(true);
						$query->update($db->qn('#__csvi_template_settings'))->set($db->qn('process').' = '.$db->q($process))->where($db->qn('id').' = '.$db->q($template->id));
						$db->setQuery($query);
						if ($db->query()) {
							$this->_results['messages'][] = JText::_('COM_CSVI_TEMPLATE_SETTINGS_CONVERTED');
							$this->_results['messages'][] = $template->name;
						}
						else {
							$this->_results['messages'][] = $db->getErrorMsg();
						}
				}
				break;
		}
	}

	/**
	 * Convert the template fields tables table
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplateFields($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.3':
			case '4.4':
				// See if there is the free or pro version
				$fields = $db->getTableColumns('#__csvi_template_settings');
				if (!array_key_exists('process', $fields)) {
					// Free version
					$db->setQuery('SELECT * FROM #__csvi_template_settings');
					$templates = $db->loadObjectList();
					foreach ($templates as $template) {
						$settings = json_decode($template->settings, true);
						$process = $settings['options']['action'];
						$fields = $settings[$process.'_fields'];
						$previous_id = 0;
						foreach ($fields['_selected_name'] as $key => $field) {
							$column_header = (isset($fields['_column_header'])) ? $fields['_column_header'][$key] : null;
							$sort = (isset($fields['_sort_field'])) ? $fields['_sort_field'][$key] : null;
							$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields (template_id, ordering, field_name, column_header, default_value, process, sort) VALUES ('.$db->quote($template->id).', '.($key+1).', '.$db->quote($field).', '.$db->quote($column_header).', '.$db->quote($fields['_default_value'][$key]).', '.$db->quote($fields['_process_field'][$key]).', '.$db->quote($sort).')');
							if ($db->execute()) {
								$id = $db->insertid();
								if ($previous_id) {
									$this->_results['messages'][] = $template->name;
									// Add the combine if needed
									if ($fields['_combine_field'][$key] > 0) {
										$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields_combine (field_id, combine_id) VALUES ('.$previous_id.', '.$id.')');
										$db->execute();
									}
									// Add the replacement if needed
									if ($fields['_replace_field'][$key] > 0) {
										$db->setQuery('INSERT IGNORE INTO #__csvi_template_fields_replacement (field_id, replace_id) VALUES ('.$previous_id.', '.$id.')');
										$db->execute();
									}
								}
							$previous_id = $id;
						}
							else {
								$this->_results['messages'][] = $db->getErrorMsg();
							}
						}
					}
				}
				break;
		}
	}

	/**
	 * Convert the templates
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param 		string	$version	the version to convert from
	 * @return
	 * @since 		4.0
	 */
	private function _convertTemplates($version) {
		$db = JFactory::getDbo();

		switch ($version) {
			case '4.0':
				// Read the old templates
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__csvi_template_settings');
				$db->setQuery($query);
				$templates = $db->loadObjectList();
				foreach ($templates as $template) {
					$settings = json_decode($template->settings);
					if (is_object($settings)) {
						// Update the process field
						$query = $db->getQuery(true);
						$query->update('#__csvi_template_settings');
						$query->set($db->qn('process').' = '.$db->quote($settings->options->action));
						$query->where($db->qn('id').' = '.$template->id);
						$db->setQuery($query);
						$db->query();

						// Check if there is a fields section
						if (isset($settings->import_fields->_selected_name) || isset($settings->export_fields->_selected_name)) {
							// Delete any existing fields for this template ID
							$query = $db->getQuery(true);
							$query->delete('#__csvi_template_fields');
							$query->where($db->qn('template_id').' = '.$template->id);
							$db->setQuery($query);
							$db->query();

							// Get the fields in a single object
							if (isset($settings->import_fields->_selected_name)) $fields = $settings->import_fields;
							else if (isset($settings->export_fields->_selected_name)) $fields = $settings->export_fields;

							// Process all the fields
							foreach ($fields->_selected_name as $key => $fieldname) {
								$table = $this->getTable('templatefield');
								$data['template_id'] = $template->id;
								$data['field_name'] = $fieldname;
								if ($settings->options->action == 'import') $data['column_header'] = '';
								else if ($settings->options->action == 'export') $data['column_header'] = $fields->_column_header[$key];
								$data['default_value'] = $fields->_default_value[$key];
								$data['process'] = $fields->_process_field[$key];
								$data['combine'] = $fields->_combine_field[$key];
								if ($settings->options->action == 'import') $data['sort'] = '0';
								else if ($settings->options->action == 'export') $data['sort'] = $data['sort'] = $fields->_sort_field[$key];
								$table->bind($data);
								if (!$table->store()) {
									$this->_results['messages'][] = $table->getError();
								}
								else {
									$fieldid = $table->id;
									// Store the replacement rules
									if (isset($fields->_replace_field)) {
										if (is_array($fields->_replace_field)) $rules = $fields->_replace_field[$key];
										else if (is_object($fields->_replace_field)) $rules = $fields->_replace_field->$key;
										if (!empty($rules)) {
											if (is_array($rules)) {
												foreach ($rules as $rule) {
													if (!empty($rule)) {
														$query = $db->getQuery(true);
														$query->insert('#__csvi_template_fields_replacement');
														$query->values(array('null, '.$fieldid.', '.$rule));
														$db->setQuery($query);
														$db->query();
													}
												}
											}
											else {
												$query = $db->getQuery(true);
												$query->insert('#__csvi_template_fields_replacement');
												$query->values(array('null, '.$fieldid.', '.$rules));
												$db->setQuery($query);
												$db->query();
											}
										}
									}
								}
							}
						}

						// Delete any old replacement references
						$query = $db->getQuery(true);
						$query->select('r.id');
						$query->from('#__csvi_template_fields_replacement r');
						$query->innerJoin('#__csvi_template_fields f ON r.field_id = f.id');
						$db->setQuery($query);
						$rids = $db->loadColumn();

						if (!empty($rids)) {
							$query = $db->getQuery(true);
							$query->delete('#__csvi_template_fields_replacement');
							$query->where('id NOT IN ('.implode(',', $rids).')');
							$db->setQuery($query);
							$db->query();
						}
					}
				}
				break;
		}
		return true;
	}

	/**
	 * Proxy function for calling the update the available fields
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
	public function getAvailableFields() {
		// Get the logger class
		$jinput = JFactory::getApplication()->input;
		$csvilog = new CsviLog();
		$jinput->set('csvilog', $csvilog);
		$model = $this->getModel('Availablefields');
		// Prepare to load the available fields
		$model->prepareAvailableFields();

		// Update the available fields
		$model->getFillAvailableFields();
	}

	/**
	 * Proxy function for installing sample templates
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
	public function getSampleTemplates() {
		// Get the logger class
		$jinput = JFactory::getApplication()->input;
		$csvilog = new CsviLog();
		$jinput->set('csvilog', $csvilog);

		jimport('joomla.filesystem.folder');
		$model = $this->getModel('Maintenance');
		$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/install/examples', '.csv');
		if (!empty($files)) {
			// Load the sample templates
			foreach ($files as $file) {
				$ext_id = $this->_isInstalled(basename($file, '.csv'));
				if ($ext_id > 0) {
					$model->getRestoreTemplates(true, JPATH_COMPONENT_ADMINISTRATOR.'/install/examples/'.$file);
				}
			}
		}
	}

	/**
	 * Create a proxy for including other models
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		3.0
	 */
	protected function getModel($model) {
		return $this->getInstance($model, 'CsviModel');
	}

	/**
	 * Set the current version in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.1
	 */
	private function _setVersion() {
		$db = JFactory::getDbo();
		$q = "INSERT IGNORE INTO #__csvi_settings (id, params) VALUES (2, '".JText::_('COM_CSVI_CSVI_VERSION')."')
			ON DUPLICATE KEY UPDATE params = '".JText::_('COM_CSVI_CSVI_VERSION')."'";
		$db->setQuery($q);
		$db->query();
	}

	/**
	 * Get the current version in the database
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.2
	 */
	private function _getVersion() {
		$db = JFactory::getDbo();

		// Check if the table exists
		$tables = $db->getTableList();

		// Load the settings
		if (in_array($db->getPrefix().'csvi_settings', $tables)) {
			$q = "SELECT params
				FROM #__csvi_settings
				WHERE id = 2";
			$db->setQuery($q);
			return $db->loadResult();
		}
		else return '';
	}

	/**
	 * Translate version
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		string with the working version
	 * @since 		3.5
	 */
	private function _translateVersion() {
		$jinput = JFactory::getApplication()->input;
		$version = $jinput->get('version', 'current', 'string');
		switch ($version) {
			case '4.0.1':
			case '4.1':
			case '4.2':
			case '4.2.1':
				return '4.0';
				break;
			default:
				return $version;
				break;
		}
	}

	/**
	 * Load supported components
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _loadComponents() {
		$db = JFactory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/install/tasks', '.sql');
		if (!empty($files)) {
			foreach ($files as $file) {
				$sqlname = JPATH_COMPONENT_ADMINISTRATOR.'/install/tasks/'.$file;
				$error = false;
				if (JFile::exists($sqlname)) {
					$ext_id = false;
					// Check if the component is installed
					$ext_id = $this->_isInstalled(basename($file, '.sql'));
					if ($ext_id > 0) {
						$q = JFile::read($sqlname);
						$queries = $db->splitSql(JFile::read($sqlname));
						foreach ($queries as $query) {
							$query = trim($query);
							if (!empty($query)) {
								$db->setQuery($query);
								if (!$db->query()) {
									$this->_results['messages'][] = $db->getErrorMsg();
									$error = true;
								}
							}
						}
						if ($error) $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_HAS_NOT_BEEN_ADDED', $file);
						else $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_HAS_BEEN_ADDED', $file);
					}
				}
				else $this->_results['messages'][] = JText::sprintf('COM_CSVI_COMPONENT_NOT_FOUND', $file);
			}
		}
	}

	/**
	 * Check if a component is installed
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		int
	 * @since 		5.9.5
	 */
	private function _isInstalled($component) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').'='.$db->q($component));
		$db->setQuery($query);
		$ext_id = $db->loadResult();
		return $ext_id;
	}
}