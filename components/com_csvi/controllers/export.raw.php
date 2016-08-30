<?php
/**
 * Export view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @version 	$Id: export.raw.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

class CsviControllerExport extends JControllerLegacy {

	/**
	* Overwrite the Joomla default getModel to make sure the ignore_request is not set to true
	*
	* @copyright
	* @author 		RolandD
	* @todo
	* @see
	* @access 		public
	* @param
	* @return
	* @since 		1.0
	*/
	public function getModel($name = '', $prefix = '', $config = array()) {
		if (empty($name)) {
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Export for front-end
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
	public function export() {
		// Create the view
		$view = $this->getView('export', 'raw');

		// Add the export model
		$view->setModel($this->getModel( 'export', 'CsviModel' ), true );

		// Add the export model path
		$this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

		// General export functions
		$view->setModel( $this->getModel( 'exportfile', 'CsviModel' ));
		// Log functions
		$view->setModel( $this->getModel( 'log', 'CsviModel' ));
		// Settings functions
		$view->setModel( $this->getModel( 'settings', 'CsviModel' ));
		// General category functions
		$view->setModel( $this->getModel( 'category', 'CsviModel' ));
		// Available fields
		$view->setModel( $this->getModel( 'availablefields', 'CsviModel' ));

		// Load the model
		$model = $this->getModel('exportfile');

		// Add extra helper paths
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers');
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/xml');
		$view->addHelperPath(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/html');

		// Load the helper classes
		$view->loadHelper('csvidb');
		$view->loadHelper('template');
		$view->loadHelper('csvisef');

		// Load the template details
		$jinput = JFactory::getApplication()->input;
		$template = new CsviTemplate();
		$template->load($jinput->get('template_id', 0, 'int'));
		$jinput->set('jform', $template->getSettings());

		// Set the output destination
		$template->set('exportto', 'general', $jinput->get('exportto', 'tofront', 'cmd'));

		$jinput->set('template', $template);

		if ($template->get('action', 'options', 'export') == 'export') {
			// Prepare for export
			if ($model->getPrepareExport()) {
				// Set the export override
				$app = JFactory::getApplication();
				$jinput = JFactory::getApplication()->input;
				$overridefile = JPATH_BASE.'/templates/'.$app->getTemplate().'/html/com_csvi/models/export/'.$template->get('operation', 'options').'.php';

				// Add the export model path if override exists
				if (file_exists($overridefile)) $this->addModelPath(JPATH_BASE.'/templates/'.$app->getTemplate().'/html/com_csvi/models/'.$template->get('component', 'options').'/export');
				else $this->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models/'.$template->get('component', 'options').'/export');

				// Load export specifc helper
				$view->loadHelper($template->get('component', 'options'));
				$view->loadHelper($template->get('component', 'options').'_config');

				// Display it all
				$view->display();
			}
			else {
				// Clean up first
				$model->getCleanSession();
				// Add appropriate message
			}
		}
		else {
			// Add appropriate message

		}
	}
}