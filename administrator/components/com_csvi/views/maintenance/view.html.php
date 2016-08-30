<?php
/**
 * Maintenance view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 2389 2013-03-21 09:03:25Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Maintenance View
 */
class CsviViewMaintenance extends JViewLegacy {

	/**
	 * Display the maintenance screen
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param 		string	$tpl	the template to use
	 * @return
	 * @since 		3.0
	 */
	function display($tpl = null) {
		// Get the component list
		$this->components = $this->get('Components');
		// Get the maintenance options
		$this->options = $this->get('MaintenanceOptions');

		$app = JFactory::getApplication();
		$app->setUserState('com_csvi.global.form', false);

		// Load the results
		$jinput = JFactory::getApplication()->input;
		$settings = $jinput->get('settings', null, null);
		if ($settings->get('log.log_store', 1)) {
			$this->logresult = $this->get('Stats', 'log');
			$this->logmessage = $this->get('StatsMessage', 'log');
		}
		else $this->logresult = false;

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('maintenance');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_MAINTENANCE'), 'csvi_maintenance_48');
		if ($this->getLayout() != 'log') {
			JToolBarHelper::custom('cron.cron', 'csvi_cron_32', 'csvi_cron_32', JText::_('COM_CSVI_CRONLINE'), false);
			JToolBarHelper::custom('', 'csvi_continue_32.png', 'csvi_continue_32.png', JText::_('COM_CSVI_CONTINUE'), false);
			//JToolBarHelper::help('maintenance.html', true);
		}
		else if ($settings->get('log.log_store', 1)) {
			JToolBarHelper::custom('logdetails.logdetails', 'csvi_logdetails_32', 'csvi_logdetails_32', JText::_('COM_CSVI_LOG_DETAILS'), false);
		}

		// Display it all
		parent::display($tpl);
	}
}