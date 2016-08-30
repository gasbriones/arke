<?php
/**
 * Process controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: process.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Import Controller
 *
* @package CSVI
 */
class CsviControllerProcess extends JControllerLegacy {


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
	 * @since 		3.0
	 */
	public function __construct() {
		parent::__construct();

		$this->registerTask('saveasnew','save');
		$this->registerTask('unpublish','publish');
	}

	/**
	 * Save a template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function save() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$jinput = JFactory::getApplication()->input;
		$model = $this->getModel('templates');

		// Store the form fields
		$app	= JFactory::getApplication();
		$data	= $jinput->get('jform', array(), 'array');

		// Save the data
		$id = $model->save($data);

		// Redirect back to the export page
		$this->setRedirect(JRoute::_('index.php?option=com_csvi&view=process&template_id='.$id, false));
	}

	/**
	 * Remove a template
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function remove() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel('templates');

		// Save the data
		$model->remove();

		// Redirect back to the export page
		$this->setRedirect(JRoute::_('index.php?option=com_csvi&view=process', false));
	}

	/**
	 * Publish/unpublish a template field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		5.3
	 */
	public function publish() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel('templatefield');

		// Save the data
		$model->switchState($this->getTask());

		// Redirect back to the page
		$jinput = JFactory::getApplication()->input;
		$this->setRedirect(base64_decode($jinput->get('return')));
	}

	/**
	 * Publish/unpublish a template field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		5.3
	 */
	public function copy() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel('templatefield');

		// Save the data
		$model->copy();

		// Redirect back to the page
		$jinput = JFactory::getApplication()->input;
		$this->setRedirect(base64_decode($jinput->get('return')));
	}

	/**
	 * Import is all finished, show the results page
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
	public function finished() {
		// Create the view object
		$view = $this->getView('process', 'result');

		// Standard model
		$view->setModel( $this->getModel( 'process', 'CsviModel' ), true );

		// Log functions
		$view->setModel( $this->getModel( 'log', 'CsviModel' ));

		// Set the layout file
		$view->setLayout('import_result');

		// Now display the view
		$view->display();
	}

	/**
	 * Cancel a running import
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo		Figure out the session vars
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function cancelImport() {
		$jinput = JFactory::getApplication()->input;
		if ($jinput->get('was_preview', false, 'bool')) {
			$this->setRedirect('index.php?option=com_csvi&view=import', JText::_('COM_CSVI_IMPORT_CANCELLED'), 'notice');
		}
		else {
			// Load the data from the session
			$session = JFactory::getSession();
			$option = $jinput->get('option');



			// The template
			require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/template.php');
			$template = unserialize($session->get($option.'.global.template'));
			$csvilog = unserialize($session->get($option.'.csvilog'));

			if (is_object($template)) {
				// Enable the session
				$jinput->set('importsession', true);

				// Set the template
				$jinput->set('template', $template);
				// The logger
				$jinput->set('csvilog', $csvilog);
				// Set the file handler
				$jinput->set('csvifile', unserialize($session->get($option.'.csvifile')));
				// Load the total line counter
				$jinput->set('totalline', unserialize($session->get($option.'.totalline')));
				// Load the total of records processed
				$jinput->set('recordsprocessed', unserialize($session->get($option.'.recordsprocessed')));
				// Load the field settings
				$jinput->set('csvifields', unserialize($session->get($option.'.csvifields')));
				// Load the column headers
				$jinput->set('columnheaders', unserialize($session->get($option.'.csvicolumnheaders')));
				// Load the preview handler
				$jinput->set('csvipreview', unserialize($session->get($option.'.csvipreview')));

				// Finish the process
				$model = $this->getModel('importfile');
				$model->finishProcess(true);

				// Store the import as cancelled
				$db = JFactory::getDbo();

				// Get the records processed
				$query = $db->getQuery(true);
				$query->select('COUNT(id) AS records');
				$query->from('#__csvi_log_details');
				$query->where('log_id = '.$csvilog->getLogId());
				$db->setQuery($query);
				$records = $db->loadResult();

				// Store the data
				$query = $db->getQuery(true);
				$query->update('#__csvi_logs');
				$query->set('records = '.$records);
				$query->set('run_cancelled = 1');
				$query->where('run_id = '.$csvilog->getId());
				$db->setQuery($query);
				$db->query();

				// Return to the import result screen
				$this->setRedirect('index.php?option=com_csvi&task=process.finished&run_id='.$csvilog->getId(), JText::_('COM_CSVI_IMPORT_CANCELLED'), 'notice');
			}
			else {
				// Return to the import result screen
				$this->setRedirect('index.php?option=com_csvi&view=process', JText::_('COM_CSVI_IMPORT_CANCELLED'), 'notice');
			}
		}
	}
}