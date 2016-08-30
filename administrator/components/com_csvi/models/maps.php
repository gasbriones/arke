<?php
/**
 * Maps model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 2280 2013-01-04 10:49:02Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.modellist' );

/**
 * Maps Model
 */
class CsviModelMaps extends JModelList {

	var $_context = 'com_csvi.maps';

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
			$config['filter_fields'] = array('name');
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
		//$this->setState('filter.action', $app->getUserStateFromRequest($this->_context.'.filter.action', 'jform_options_action', 'import', 'word'));
		//$this->setState('filter.component', $app->getUserStateFromRequest($this->_context.'.filter.component', 'jform_options_component', 'com_csvi', 'cmd'));
		//$this->setState('filter.operation', $app->getUserStateFromRequest($this->_context.'.filter.operation', 'jform_options_operation', 'customimport', 'word'));
		//$this->setState('filter.avfields', $app->getUserStateFromRequest($this->_context.'.filter.avfields', 'filter_avfields', false, 'word'));
		//$this->setState('filter.idfields', $jinput->get('filter_idfields'));

		// List state information.
		// Controls the query ORDER BY
		parent::populateState('name', 'asc');
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
		$query->select($db->qn('id').','.$db->qn('name').','.$db->qn('checked_out'));
		$query->from($db->qn('#__csvi_maps', 'm'));

		// Add all the filters
		$filters = array();

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
	 * Get the data to create a new template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		array	the data
	 * @since 		5.8
	 */
	public function getTemplateData() {
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->getInt('id');
		$data = array();

		// Get the map details
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('m.action').','.$db->qn('m.component').','.$db->qn('m.operation'))
			->from($db->qn('#__csvi_maps', 'm'))
			->where($db->qn('m.id').' = '.$id);
		$db->setQuery($query);
		$map = $db->loadObjectList();

		// Get the options if we have a result
		if ($map) {
			$data['options']['action'] = $map[0]->action;
			$data['options']['component'] = $map[0]->component;
			$data['options']['operation'] = $map[0]->operation;
		}

		// Return the data
		return $data;
	}

	/**
	 * Get the fields to create template fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		data	the fields
	 * @since 		5.8
	 */
	public function getTemplateFields() {
		$jinput = JFactory::getApplication()->input;
		$id = $jinput->getInt('id', 0);
		$data = array();

		if ($id) {
			// Get the map details
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->qn('templateheader'))
				->from($db->qn('#__csvi_mapheaders'))
				->where($db->qn('map_id').' = '.$id);
			$db->setQuery($query);
			$data = $db->loadColumn();
		}

		// Return the data
		return $data;
	}
}