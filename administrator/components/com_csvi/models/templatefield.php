<?php
/**
 * Template field model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: settings.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modeladmin');

/**
 * Settings Model
 */
class CsviModelTemplatefield extends JModelAdmin {

	private $context = 'com_csvi.templatefield';

	/**
	 * Method to get the record form located in models/forms
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		array $data Data for the form.
	 * @param 		boolean $loadData True if the form is to load its own data (default case), false if not.
	 * @return 		mixed
	 * @since 		1.0
	 */
	public function getForm($data = array(), $loadData = true) {
		$form = $this->loadForm($this->context, 'templatefield', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;

		return $form;
	}

	/**
	 * Get the form data
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		4.1
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.templatefield.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Get the combine
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('combine_id');
			$query->from('#__csvi_template_fields_combine');
			$query->where('field_id = '.$data->id);
			$db->setQuery($query);
			$data->combine = $db->loadColumn();

			// Get the replacements
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('replace_id');
			$query->from('#__csvi_template_fields_replacement');
			$query->where('field_id = '.$data->id);
			$db->setQuery($query);
			$data->replacement = $db->loadColumn();
		}

		return $data;
	}

	/**
	 * Store the settings
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		Replacement rules needs to be stored
	 * @see
	 * @access 		public
	 * @param
	 * @return 		bool	true on success | false on failure
	 * @since 		4.3
	 */
	public function save($data) {
		// Save the base data
		parent::save($data);

		$db = JFactory::getDbo();

		// Add the combine fields
		// Delete all existing references
		$query = $db->getQuery(true);
		$query->delete('#__csvi_template_fields_combine');
		$query->where('field_id = '.$data['id']);
		$db->setQuery($query);
		$db->execute();

		// Add the storing of the combine fields
		if (count($data['combine']) > 0) {
			$query = $db->getQuery(true);
			$query->insert('#__csvi_template_fields_combine');
			foreach ($data['combine'] as $combine) {
				$query->values('null, '.$data['id'].','.$combine);
			}
			$db->setQuery($query);
			$db->execute();
		}

		// Add the replacement fields
		// Delete all existing references
		$query = $db->getQuery(true);
		$query->delete('#__csvi_template_fields_replacement');
		$query->where('field_id = '.$data['id']);
		$db->setQuery($query);
		$db->execute();

		// Add the storing of the replacement rules
		if (count($data['replacement']) > 0) {
			$query = $db->getQuery(true);
			$query->insert('#__csvi_template_fields_replacement');
			foreach ($data['replacement'] as $replace) {
				$query->values('null, '.$data['id'].','.$replace);
			}
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/**
	 * Store the field order
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function saveOrder() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		// Get the names and values
		$names = explode(',', $jinput->get('names', '', 'string'));
		$values = explode(',', $jinput->get('values', '', 'string'));

		foreach ($names as $index => $name) {
			$filter = JFilterInput::getInstance();
			$id = $filter->clean($name, 'int');
			$table = $this->getTable();
			$table->load($id);
			$table->ordering = $values[$index];
			$table->store();
		}
	}

	/**
	 * Store a template field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see			views/process/tmpl/default.php
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function storeTemplateField() {
		$jinput = JFactory::getApplication()->input;

		// Collect the data
		$data = array();
		$fieldnames = explode(',', $jinput->get('field_name', '', 'string'));
		$template_id = $jinput->get('template_id', 0, 'int');
		// Get the highest field number
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('MAX(ordering)')->from('#__csvi_template_fields')->where('template_id = '.$template_id);
		$db->setQuery($query);
		$ordering = $db->loadResult();

		foreach ($fieldnames as $fieldname) {
			if (!empty($fieldname)) {
				$table = $this->getTable('templatefield');
				$data['template_id'] = $template_id;
				$data['ordering'] = ++$ordering;
				$data['field_name'] = $fieldname;
				$data['file_field_name'] = $jinput->get('file_field_name', '', 'string');
				$data['column_header'] = $jinput->get('column_header', '', 'string');
				$data['default_value'] = $jinput->get('default_value', '', 'string');
				$data['process'] = $jinput->get('process', 1, 'int');
				$data['sort'] = $jinput->get('sort', 0, 'int');
				$table->bind($data);
				if (!$table->store()) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Delete a template field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.3
	 */
	public function deleteTemplateField() {
		$jinput = JFactory::getApplication()->input;
		$cids = $jinput->get('cids', '', 'string');
		if ($cids) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__csvi_template_fields');
			$query->where('id IN ('.$cids.')');
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Renumber the fields
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		int $template_id	ID of the template to renumber the fields
	 * @return 		bool	true if no errors found | false if an error is found
	 * @since 		4.3
	 */
	public function renumberFields($template_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('f.id'));
		$query->from('#__csvi_template_fields f');
		$query->where($db->quoteName('f.template_id').' = '.$db->quote($template_id));
		$query->order('f.ordering, f.field_name');
		$db->setQuery($query);
		$fields = $db->loadObjectList();

		$process = true;
		foreach ($fields as $order => $field) {
			$query = $db->getQuery(true);
			$query->update('#__csvi_template_fields');
			$query->set('ordering = '.($order+1));
			$query->where('id = '.$field->id);
			$db->setQuery($query);
			if (!$db->query()) {
				$process = false;
				$app = JFactory::getApplication();
				$app->enqueueMessage($db->getErrorMsg());
			}
		}
		return $process;
	}

	/**
	 * Switch the state of a process or combine field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access		public
	 * @param		string	$action	the field to switch
	 * @return
	 * @since 		4.3
	 */
	public function switchState($action) {
		$jinput = JFactory::getApplication()->input;
		$table = $this->getTable('templatefield');
		$ids = $jinput->get('cid', array(), 'array');
		if (!empty($ids)) {
			foreach ($ids as $id) {
				$table->load($id);
				switch ($action) {
					case 'publish':
						$table->set('process', 1);
						break;
					case 'unpublish':
						$table->set('process', 0);
						break;
				}
				$table->store();
			}
		}
	}

	/**
	 * Clone a template field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.3
	 */
	public function copy() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$table = $this->getTable('templatefield');
		$ids = $jinput->get('cid', array(), 'array');
		if (!empty($ids)) {
			foreach ($ids as $id) {
				// Load the field
				$table->load($id);
				// Remove the id so we can save it as new
				$table->id = null;
				// Store the field
				$table->store();

				// Get all the replacements
				$query = $db->getQuery(true);
				$query->select('replace_id')->from('#__csvi_template_fields_replacement')->where('field_id = '.$id);
				$db->setQuery($query);
				$replaces = $db->loadObjectList();
				if (!empty($replaces)) {
					foreach ($replaces as $replace) {
						$query->clear();
						$query->insert('#__csvi_template_fields_replacement')->values('null,'.$table->id.','.$replace->replace_id);
						$db->setQuery($query);
						$db->query();
					}
				}
				// Get all the combines
				$query = $db->getQuery(true);
				$query->select('combine_id')->from('#__csvi_template_fields_combine')->where('field_id = '.$id);
				$db->setQuery($query);
				$combines = $db->loadObjectList();
				if (!empty($combines)) {
					foreach ($combines as $combine) {
						$query->clear();
						$query->insert('#__csvi_template_fields_combine')->values('null,'.$table->id.','.$combine->combine_id);
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}
	}
}