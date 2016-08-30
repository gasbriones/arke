<?php
/**
 * Control panel
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 2391 2013-03-23 21:47:44Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Default View
 *
* @package CSVI
 */
class CsviViewCsvi extends JViewLegacy {
	/**
	 * CSVI VirtueMart view display method
	 *
	 * @return void
	 */
	function display($tpl = null) {
		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_CONTROL_PANEL'), 'csvi_logo_48.png' );
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_csvi')) {
			JToolBarHelper::preferences('com_csvi');
		}
		//JToolBarHelper::help('control_panel.html', true);

	 	// Render the submenu
	    if (version_compare(JVERSION, '3.0', '>=')) {
	    	CsviHelper::addSubmenu('');
	    	$this->sidebar = JHtmlSidebar::render();
	    }
	    else {
	    	// Get the panel
	    	$helper = new CsviHelper();
	    	$this->cpanel_images = $helper->getButtons();
	    	$this->sidebar = '';
	    }

		// Display the page
		parent::display($tpl);
	}
}