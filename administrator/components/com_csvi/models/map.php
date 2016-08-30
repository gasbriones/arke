<?php
/**
 *
 * Model class for map editing
 *
 * @author 		RolandD
 * @link		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetype.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load the model framework
jimport('joomla.application.component.modeladmin');

/**
 * Map editing
 *
 * @author 		RolandD
 * @since 		1.0
 */
class CsviModelMap extends JModelAdmin {

	/**
	 * @var string Model context string
	 */
	private $context = 'com_csvi.map';

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
		// Get the form.
		$form = $this->loadForm($this->context, 'map', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData() {
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_csvi.edit.map.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Load the data for an item
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function getItem($pk = null) {
		$item = parent::getItem($pk);
		if ($item->id) {
			$jinput = JFactory::getApplication()->input;
			$db = JFactory::getDbo();

			// Load the options
			$query = $db->getQuery(true)
				->select($db->qn('action').','.$db->qn('component').','.$db->qn('operation'))
				->from($db->qn('#__csvi_maps'))
				->where($db->qn('id').'='.$item->id);
			$db->setQuery($query);
			$item->options = $db->loadObject();

			// Store the value so the operation dropdown on the edit form can be filled
			$jinput->set('jform', array('options' => $db->loadAssoc()));

			// Load the header fields to match
			$query = $db->getQuery(true)
				->select($db->qn('csvheader').','.$db->qn('templateheader'))
				->from($db->qn('#__csvi_mapheaders'))
				->where($db->qn('map_id').'='.$item->id);
			$db->setQuery($query);
			$item->headers = $db->loadObjectList();
		}

		return $item;
	}

	/**
	 * Some post processing after saving
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function postSaveHook($pk, $validData) {
		$table = $this->getTable('Map');
		$data = array();
		$data['id'] = $pk;
		foreach ($validData['options'] as $option => $value) {
			$data[$option] = $value;
		}
		return $table->save($data);
	}

	/**
	 * Process an uploaded file with headers
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function processFile($pk, $validData) {
		$jinput = JFactory::getApplication()->input;

		// Get the file details
		$upload = array();
		$upload['name'] = $_FILES['jform']['name']['mapfile'];
		$upload['type'] = $_FILES['jform']['type']['mapfile'];
		$upload['tmp_name'] = $_FILES['jform']['tmp_name']['mapfile'];
		$upload['error'] = $_FILES['jform']['error']['mapfile'];

		if (!$upload['error']) {
			// Move the temporary file
			if (is_uploaded_file($upload['tmp_name'])) {
				// Get some basic info
				jimport('joomla.filesystem.file');
				jimport('joomla.filesystem.folder');
				$folder = CSVIPATH_TMP.'/'.time();
				$upload_parts = pathinfo($upload['name']);

				// Create the temp folder
				if (JFolder::create($folder)) {
					// Move the uploaded file to its temp location
					if (JFile::upload($upload['tmp_name'], $folder.'/'.$upload['name'])) {
						if (array_key_exists('extension', $upload_parts)) {
							// Load the base class
							require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file.php';

							// Load the extension specific class
							switch ($upload_parts['extension']) {
								case 'xml':
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/xml.php';
									$fileclass = 'Xml';
									break;
								case 'xls':
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/xls.php';
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/excel_reader2.php';
									$fileclass = 'Xls';
									break;
								case 'ods':
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/ods.php';
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/ods_reader.php';
									$fileclass = 'Ods';
									break;
								default:
									// Treat any unknown type as CSV
									require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/file/import/csv.php';
									$fileclass = 'Csv';
									break;
							}
							// Set the file class name
							$fileclass .= 'File';

							// Get a template object
							if (!class_exists('CsviTemplate')) require JPATH_COMPONENT_ADMINISTRATOR.'/helpers/template.php';
							$template = new CsviTemplate();
							$template->set('source', 'general', 'fromserver');
							$template->set('local_csv_file', 'general', $folder.'/'.$upload['name']);
							$jinput->set('template', $template);

							// Get the file handler
							$file = new $fileclass;

							// Validate and process the file
							$file->validateFile();
							$file->processFile();

							// Get the header
							if ($file->loadColumnHeaders()) {
								$header = $jinput->get('columnheaders', array(), 'array');

								if (is_array($header)) {
									// Load the table
									$table = $this->getTable('mapheaders');

									// Remove existing entries
									$db = JFactory::getDbo();
									$query = $db->getQuery(true)
										->delete($db->qn('#__csvi_mapheaders'))
										->where($db->qn('map_id').'='.$pk);
									$db->setQuery($query);
									$db->query();

									// Store the headers
									$map = array();
									$map['map_id'] = $pk;
									foreach ($header as $name) {
										$map['csvheader'] = $name;

										// Store the data
										$table->save($map);
										$table->reset();
									}
								}
								else return false;
							}
							else return false;
						}
						else return false;
					}
					else return false;
				}
				else return false;
			}
			else return false;
		}
	}

	/**
	 * Process the header mappings
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function processHeader($validData, $header) {
		$db = JFactory::getDbo();
		foreach ($header as $csvheader => $templateheader) {
			$query = $db->getQuery(true)
				->update($db->qn('#__csvi_mapheaders'))
				->set($db->qn('templateheader').'='.$db->q($templateheader))
				->where($db->qn('map_id').'='.$validData['id'])
				->where($db->qn('csvheader').'='.$db->q($csvheader));
			$db->setQuery($query);
			echo $query->dump();
			$db->query();
		}
	}
}