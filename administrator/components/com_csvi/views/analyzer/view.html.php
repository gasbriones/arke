<?php
/**
 * Analyzer view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Analyzer View
 */
class CsviViewAnalyzer extends JViewLegacy {

	/**
	* Analyzer view display method
	* @return void
	* */
	public function display($tpl = null) {

		$jinput = JFactory::getApplication()->input;
		$this->process = $jinput->get('process', false);

		// Check if we need to run the analyzer
		if ($this->process) {
			$this->items = $this->get('Analyze');
		}

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('analyzer');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_ANALYZER'), 'csvi_analyzer_48');
		JToolBarHelper::custom('analyzer.display', 'csvi_continue_32', 'csvi_continue_32', JText::_('COM_CSVI_ANALYZE'), false);
		//JToolBarHelper::help('about.html', true);

		// Display it all
		parent::display($tpl);
	}
}