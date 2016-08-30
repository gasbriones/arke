<?php
/**
 * Template types model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: templatetypes.php 2383 2013-03-17 09:08:01Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.modellist' );

/**
 * Template types Model
 */
class CsviModelTemplatetypes extends JModelList {

	/** @var Set the context */
	var $_context = 'com_csvi.templatetypes';

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	public function __construct() {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('template_type_name', 'template_type', 'component', 'ordering');
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
		$app = JFactory::getApplication('administrator');

		$name = $app->getUserStateFromRequest($this->context.'.filter.name', 'filter_name', '');
		$this->setState('filter.name', $name);

		$component = $app->getUserStateFromRequest($this->context.'.filter.component', 'filter_component', '');
		$this->setState('filter.component', $component);

		$process = $app->getUserStateFromRequest($this->context.'.filter.process', 'filter_process', '*');
		$this->setState('filter.process', $process);

		$published = $app->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '*');
		$this->setState('filter.published', $published);

		// List state information.
		// Controls the query ORDER BY
		parent::populateState('template_type_name', 'asc');
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
		$query->select('*');
		$query->from($db->qn('#__csvi_template_types'));

		// Check for filters used
		$name = $this->getState('filter.name');
		if ($name) {
			$query->where($db->qn('template_type_name').' LIKE '.$db->q('%'.$name.'%'));
		}

		$component = $this->getState('filter.component');
		if ($component) {
			$query->where($db->qn('component').' = '.$db->q($component));
		}

		$process = $this->getState('filter.process');
		if ($process != '*') {
			$query->where($db->qn('template_type').' = '.$db->q($process));
		}

		$published = $this->getState('filter.published');
		if ($published != '*') {
			$query->where('published = '.$published);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}

	/**
	 * Load the template types for a given selection
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		$action	the import or export option
	 * @param		$component the component
	 * @return 		array of available template types
	 * @since 		3.5
	 */
	public function loadTemplateTypes($action, $component) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('template_type_name'))
			->from($db->qn('#__csvi_template_types'))
			->where($db->qn('template_type').'='.$db->q($action))
			->where($db->qn('component').'='.$db->q($component))
			->where($db->qn('published').'= 1')
			->order($db->qn('ordering'));
		$db->setQuery($query);
		$types = $db->loadColumn();

		// Get translations
		$trans = array();
		foreach ($types as $type) {
			$trans[$type] = JText::_('COM_CSVI_'.strtoupper($type));
		}
		return $trans;
	}

	/**
	 * Reset the template types
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return
	 * @since 		5.4
	 */
	public function reset() {
		$db = JFactory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$files = JFolder::files(JPATH_COMPONENT_ADMINISTRATOR.'/install/tasks', '.sql');
		if (!empty($files)) {
			foreach ($files as $file) {
				$sqlname = JPATH_COMPONENT_ADMINISTRATOR.'/install/tasks/'.$file;
				$error = false;
				if (JFile::exists($sqlname)) {
					$q = JFile::read($sqlname);
					$queries = $db->splitSql(JFile::read($sqlname));
					foreach ($queries as $query) {
						$query = trim($query);
						if (!empty($query)) {
							$db->setQuery($query);
							if (!$db->query()) {
								$this->setError($db->getErrorMsg());
								return false;
							}
						}
					}
				}
			}
		}
		return true;
	}
}