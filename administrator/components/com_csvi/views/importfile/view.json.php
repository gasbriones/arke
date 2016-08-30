<?php
/**
 * Import file cron view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.json.php 2388 2013-03-17 16:55:09Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Import file View
 */
class CsviViewImportFile extends JViewLegacy {

	/**
	 * Import the files
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		string	JSON encoded text
	 * @since 		3.0
	 */
	public function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;
		if ($jinput->get('importsession', true, 'bool')) {
			// Process the data
			$this->get('ProcessData');

			// Empty the message stack
			$session = JFactory::getSession();
			$session->set('application.queue', null);

			// Collect the results
			$result = array();

			// Set the view mode
			if ($jinput->get('csvipreview', false, 'bool')) {
				$result['view'] = 'preview';
				$result['headers'] = $jinput->get('headers_preview', null, null);
				$result['output'] = $jinput->get('data_preview', null, null);

				if (empty($results['headers']) && empty($result['output'])) {
					$result['process'] = false;
					$csvilog = $jinput->get('csvilog', null, null);
					$result['url'] = JURI::root().'administrator/index.php?option=com_csvi&task=process.finished&run_id='.$csvilog->getId();

					// Clean the session, nothing to import
					$this->get('CleanSession');
				}
				else $result['process'] = true;
			}
			else {
				$result['view'] = '';
				// Get the number of records processed
				$result['records'] = $jinput->get('recordsprocessed', 0, 'int');

				if ($result['records'] == 0 || $jinput->get('finished', false)) {
					$result['process'] = false;
					$result['url'] = JURI::root().'administrator/index.php?option=com_csvi&task=process.finished&run_id='.$jinput->get('run_id', 0, 'int');
				}
				else {
					// Check if we are finished
					$result['process'] = true;
				}
			}
		}
		else {
			// Collect the results
			$result = array();
			$result['process'] = false;
			$result['url'] = JURI::root().'administrator/index.php?option=com_csvi&task=process.finished&run_id='.$jinput->get('run_id', 0, 'int');

			// Clean the session, nothing to import
			$this->get('CleanSession');
		}

		// If the import is finished, call the plugins
		if (!$result['process']) {
			// Load the template
			$session = JFactory::getSession();
			$template = new CsviTemplate();
			$template->load(unserialize($session->get('com_csvi.select_template')));
			$options = array();
			$options[] = $template->get('options');
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('csvi');
			$dispatcher->trigger('onImportComplete', $options);
		}
		else {
			// Import is not finished, lets sleep
			if ($jinput->get('currentline', 0, 'int') > 0 && !$jinput->get('finished', false)) {
				$settings = new CsviSettings();
				sleep($settings->get('import.import_wait', 0));
			}
		}

		// Output the results
		echo json_encode($result);
	}
}