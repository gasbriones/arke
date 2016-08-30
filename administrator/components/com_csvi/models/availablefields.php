<?php
/**
 * Available fields model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: availablefields.php 2383 2013-03-17 09:08:01Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.modellist' );

/**
 * Available fields Model
 */
class CsviModelAvailablefields extends JModelList {

	var $_context = 'com_csvi.availablefields';

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param		array	An optional associative array of configuration settings.
	 * @return
	 * @since 		1.0
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('csvi_name', 'component_table');
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		protected
	 * @param
	 * @return		void
	 * @since		4.0
	 */
	protected function populateState() {
		// Initialise variables.
		$jinput = JFactory::getApplication()->input;
		$app = JFactory::getApplication('administrator');

		// Load the filter state
		$this->setState('filter.action', $app->getUserStateFromRequest($this->_context.'.filter.action', 'jform_options_action', 'import', 'word'));
		$this->setState('filter.component', $app->getUserStateFromRequest($this->_context.'.filter.component', 'jform_options_component', 'com_csvi', 'cmd'));
		$this->setState('filter.operation', $app->getUserStateFromRequest($this->_context.'.filter.operation', 'jform_options_operation', 'customimport', 'word'));
		$this->setState('filter.avfields', $app->getUserStateFromRequest($this->_context.'.filter.avfields', 'filter_avfields', false, 'word'));
		$this->setState('filter.idfields', $jinput->get('filter_idfields'));

		// List state information.
		// Controls the query ORDER BY
		parent::populateState('csvi_name', 'asc');
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return		object the query to execute
	 * @since 		4.0
	 */
	protected function getListQuery() {
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select('csvi_name, component_name, component_table, isprimary');
		$query->from('#__csvi_available_fields AS a');

		// Join the template types
		$query->leftJoin('#__csvi_template_tables AS t ON t.template_table = a.component_table');

		// Add all the filters
		$filters = array();
		if ($this->getState('filter.action') != '') $filters[] = "SUBSTRING(t.template_type_name, -6) = ".$db->Quote($this->getState('filter.action'));
		if ($this->getState('filter.component') != '') {
			$filters[] = "a.component = ".$db->q($this->getState('filter.component'));
			$filters[] = "t.component = ".$db->q($this->getState('filter.component'));
		}
		if ($this->getState('filter.operation') != '') $filters[] = "t.template_type_name = ".$db->Quote($this->getState('filter.operation'));
		if ($this->getState('filter.avfields') != '') $filters[] = "(csvi_name LIKE ".$db->Quote('%'.$this->getState('filter.avfields').'%')." OR component_name LIKE ".$db->Quote('%'.$this->getState('filter.avfields').'%')." OR component_table LIKE ".$db->Quote('%'.$this->getState('filter.avfields').'%').")";
		if (!$this->getState('filter.idfields')) $filters[] = "(csvi_name NOT LIKE '%\_id' AND csvi_name NOT LIKE 'id')";

		// Add the filters to the query
		if (!empty($filters)) {
			$query->where('('.implode(' AND ', $filters).')');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	/**
	 * Fill the available fields table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getFillAvailableFields() {
		// Load the session data
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$option = $jinput->get('option');
		$continue = true;
		while ($continue) {
			$continue = $this->getAvailableFieldsSingle();
		}
		$csvilog = unserialize($session->get($option.'.csvilog'));
		$jinput->set('csvilog', $csvilog);
		return;
	}

	/**
	 * Prepare for available fields importing.
	 *
	 * 1. Set all tables to be indexed
	 * 2. Empty the available fields table
	 * 3. Import the extra availablefields sql file
	 * 4. Find what tables need to be imported and store them in the session
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see			CsviModelSettings::save
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.5
	 */
	public function prepareAvailableFields() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		// Load the session data
		$session = JFactory::getSession();
		$option = $jinput->get('option');
		$csvilog = $jinput->get('csvilog', null, null);

		// Clean the session
		$session->set($option.'.csvilog', serialize('0'));

		// Set all tables to be indexed
		$query = $db->getQuery(true);
		$query->update('#__csvi_template_tables');
		$query->set('indexed = 0');
		$db->setQuery($query);
		$db->query();

		// Empty the available fields first
		$q = "TRUNCATE TABLE ".$db->qn('#__csvi_available_fields');
		$db->setQuery($q);
		if ($db->query()) $csvilog->AddStats('empty', JText::_('COM_CSVI_AVAILABLE_FIELDS_TABLE_EMPTIED'));
		else $csvilog->AddStats('error', JText::_('COM_CSVI_AVAILABLE_FIELDS_TABLE_COULD_NOT_BE_EMPTIED'));

		// Do component specific updates
		$override = new stdClass();
		$override->value = 'override';
		$components = CsviHelper::getComponents();
		$components[] = $override;
		jimport('joomla.filesystem.file');
		foreach ($components as $component) {
			switch ($component->value) {
				case 'mod_vm_cherry_picker':
					// Delete any existing entries
					$query = $db->getQuery(true);
					$query->delete('#__csvi_template_tables')
						->where($db->qn('component').' = '.$db->q('mod_vm_cherry_picker'))
						->where($db->qn('template_table').' REGEXP '.$db->q('vm_product_type_[0-9]'));
					$db->setQuery($query);
					$db->query();

					// Add new entries
					$name_tables = $db->getTableList();
					$query = $db->getQuery(true);
					$validq = false;
					$query->insert('#__csvi_template_tables')->columns(array('template_type_name', 'template_table', 'component', 'indexed'));
					foreach ($name_tables as $nkey => $name_table) {
						if (strpos($name_table, $db->getPrefix().'vm_product_type') !== false) {
							if (stristr('0123456789', substr($name_table, -1))) {
								$validq = true;
								$name = str_ireplace($db->getPrefix(), '', $name_table);
								$query->values($db->q('producttypenamesexport').','.$db->q($name).','.$db->q('mod_vm_cherry_picker').',0');
								$query->values($db->q('producttypenamesimport').','.$db->q($name).','.$db->q('mod_vm_cherry_picker').',0');
							}
						}
					}
					if ($validq) {
						$db->setQuery($query);
						$db->query();
					}
					break;
			}

			// Process all extra available fields
			$filename = JPATH_COMPONENT_ADMINISTRATOR.'/install/availablefields/'.$component->value.'.sql';
			if (JFile::exists($filename)) {
				// Check if the component is installed
				$ext_id = false;
				$query = $db->getQuery(true)
				->select($db->qn('extension_id'))
				->from($db->qn('#__extensions'))
				->where($db->qn('element').'='.$db->q($component->value));
				$db->setQuery($query);
				$ext_id = $db->loadResult();
				if ($ext_id) {
					$queries = JInstallerHelper::splitSql(JFile::read($filename));
					foreach ($queries as $q) {
						$db->setQuery($q);
						if ($db->query()) $result = true;
						else $result = false;
					}
					if ($result) $csvilog->AddStats('added', JText::sprintf('COM_CSVI_CUSTOM_AVAILABLE_FIELDS_HAVE_BEEN_ADDED', JText::_('COM_CSVI_'.$component->value)));
					else $csvilog->AddStats('error', JText::sprintf('COM_CSVI_CUSTOM_AVAILABLE_FIELDS_HAVE_NOT_BEEN_ADDED', JText::_('COM_CSVI_'.$component->value)));
				}
			}
			else $csvilog->AddStats('error', JText::sprintf('AVAILABLEFIELDS_EXTRA_NOT_FOUND', $filename));
		}
		// Add the log the session
		$session->set($option.'.csvilog', serialize($csvilog));
	}

	/**
	 * Import the available fields in steps
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.5
	 */
	public function getAvailableFieldsSingle() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$queries = array();

		// Load the session data
		$session = JFactory::getSession();
		$option = $jinput->get('option');
		$csvilog = unserialize($session->get($option.'.csvilog'));
		$lines = unserialize($session->get($option.'.linesprocessed'));
		if (empty($lines)) $lines = 0;
		$lines++;
		// Set the line number
		$csvilog->setLinenumber($lines);
		$errors = false;
		$process = false;

		// Load a table to index
		while (!$process) {
			$query = $db->getQuery(true);
			$query->select('template_table, component')
				->from('#__csvi_template_tables')
				->where('indexed = 0')
				->where($db->qn('template_table').' != '.$db->qn('template_type_name'))
				->group($db->qn('template_table'));
			$db->setQuery($query, 0, 1);
			$table = $db->loadObject();
			if (is_object($table)) {
				// Set the table name
				$showtable = $table->template_table;

				// Check if the table exists
				$tables = $db->getTableList();
				if (in_array($db->getPrefix().$showtable, $tables)) {
					// Get the primary key for the table
					$primarykey = CsviHelper::getPrimaryKey($showtable);

					$fields = $this->DbFields($showtable, true);
					if (is_array($fields)) {
						$process = true;
						// Process all fields
						foreach ($fields as $name => $value) {
							// Check if the field is a primary field
							if ($primarykey == $name) $primary = 1;
							else $primary = 0;

							if ($name) {
								$q = "INSERT IGNORE INTO ".$db->qn('#__csvi_available_fields')." VALUES ("
									."0,"
									.$db->q($name).","
									.$db->q($name).","
									.$db->q($value).","
									.$db->q($table->component).","
									.$db->q($primary).")";
								$db->setQuery($q);
								if (!$db->query()) $errors = true;

							}
						} // foreach
						// Check for any errors
						if (!$errors) {
							$jinput->set('updatetable', $showtable);
							$csvilog->AddStats('added', JText::sprintf('COM_CSVI_AVAILABLE_FIELDS_HAVE_BEEN_ADDED', $showtable));
						}
						else {
							$csvilog->AddStats('error', JText::_('COM_CSVI_AVAILABLE_FIELDS_HAVE_NOT_BEEN_ADDED'));
						}
					} // is_array
				}
				// Set the table to indexed
				$query = $db->getQuery(true);
				$query->update('#__csvi_template_tables')
					->set('indexed = 1')
					->where('template_table = '.$db->quote($showtable))
					->where('component = '.$db->quote($table->component));
				$db->setQuery($query);
				$db->query();

				// Assign the tables to the session
				$session->set($option.'.linesprocessed', serialize($lines));

				$continue = true;


			} // empty
			else {
				$jinput->set('csvilog', $csvilog);

				// Clear the session
				$session->set($option.'.csvilog', serialize('0'));
				$session->set($option.'.linesprocessed', serialize('0'));

				// Set the run ID
				$jinput->set('run_id', $csvilog->getId());

				$continue = false;
				$process = true;
			}
			// Assign the log to the session
			$session->set($option.'.csvilog', serialize($csvilog));
		}

		return $continue;
	}

	/**
	 * Creates an array of custom database fields the user can use for import/export
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return		array of custom database fields
	 * @since 		3.0
	 */
	public function DbFields($table, $addname=false) {
		$db = JFactory::getDbo();
		$customfields = array();
		$q = "SHOW COLUMNS FROM ".$db->quoteName('#__'.$table);
		$db->setQuery($q);
		$fields = $db->loadObjectList();
		if (count($fields) > 0) {
			foreach ($fields as $key => $field) {
				if ($addname) $customfields[$field->Field] = $table;
				else $customfields[$field->Field] = null;
			}
		}
		return $customfields;
	}

	/**
	 * Get the fields belonging to a certain operation type
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$type	the template type name
	 * @return 		array	list of tables or fields
	 * @since		3.0
	 */
	public function getAvailableFields($type, $component, $filter='array', $table_name=null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('csvi_name AS value, csvi_name AS text');
		$query->from('#__csvi_available_fields AS a');
		$query->leftJoin('#__csvi_template_tables AS t ON t.template_table = a.component_table');
		$query->where($db->qn('t.template_type_name').' = '.$db->q($type));
		$query->where($db->qn('t.component').' = '.$db->q($component));
		$query->where($db->qn('a.component').' = '.$db->q($component));
		if ($table_name) $query->where($db->qn('t.template_table').' = '.$db->q($table_name));
		$query->group('csvi_name');
		$db->setQuery($query);

		// Get the results
		$fields = array();
		if ($filter == 'array') $fields = $db->loadColumn();
		else if ($filter == 'object') $fields = $db->loadObjectList();

		// Return the array of fields
		return $fields;
	}

	/**
	* Check if there are enough fields in the database
	*
	* @author RolandD
	* @access public
	* @return bool true|false
	*/
	public function getFieldCheck() {
		$db = JFactory::getDbo();
		$q = 'SELECT COUNT(id) FROM #__csvi_available_fields';
		$db->setQuery($q);
		if ($db->loadResult() > 0) return true;
		else return false;
	}

	/**
	 * Proxy for getModel
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		object of a database model
	 * @since 		4.0
	 */
	public function getModel($name = 'AvailableFields', $prefix = 'CsviModel') {
		$model = JModelList::getInstance($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}