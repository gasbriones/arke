<?php
/**
 * About view
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
 * About View
 */
class CsviViewAbout extends JViewLegacy {

	/**
	* About view display method
	* @return void
	* */
	function display($tpl = null) {

		// Assign the values
		$this->folders = $this->get('FolderCheck');

		// Get the schema version
		$this->schemaVersion = $this->get('SchemaVersion');

		// Check for database errors
		$changeSet = $this->get('ChangeSet');
		$this->errors = $changeSet->check();

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('about');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_ABOUT'), 'csvi_about_48');
		JToolBarHelper::custom('about.fix', 'refresh', 'refresh', 'COM_CSVI_TOOLBAR_DATABASE_FIX', false, false);
		//JToolBarHelper::help('about.html', true);

		// Display it all
		parent::display($tpl);
	}
}