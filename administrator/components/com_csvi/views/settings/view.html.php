<?php
/**
 * Settings view
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
 * Settings View
 */
class CsviViewSettings extends JViewLegacy {

	/**
	* About view display method
	* @return void
	* */
	function display($tpl = null) {
		// Load the form
		$this->form = $this->get('Form');

		// Load a list of tables
		$this->tablelist = $this->get('TableList');

		// Get ICEcat statistics
		$this->icecat_stats = $this->get('IcecatStats');

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('settings');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_SETTINGS_TITLE'), 'csvi_settings_48');
		JToolBarHelper::custom('settings.reset', 'csvi_reset_32', 'csvi_reset_32', JText::_('COM_CSVI_RESET_SETTINGS'), false);
		JToolBarHelper::custom('settings.save', 'csvi_save_32', 'csvi_save_32', JText::_('COM_CSVI_SAVE'), false);
		//JToolBarHelper::help('settings.html', true);

		// Display it all
		parent::display($tpl);
	}
}