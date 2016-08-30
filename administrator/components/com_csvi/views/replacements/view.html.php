<?php
/**
 * Template types view
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
 * Template types View
 */
class CsviViewReplacements extends JViewLegacy {

	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Template types display method
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
	public function display($tpl = null) {
		// Load the template types
		$this->items = $this->get('Items');

		// Get the pagination
		$this->pagination = $this->get('Pagination');

		// Load the user state
		$this->state = $this->get('State');

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('replacements');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_REPLACEMENTS'), 'csvi_replacement_48');
		JToolBarHelper::addNew('replacement.add');
		JToolBarHelper::editList('replacement.edit');
		JToolBarHelper::deleteList('', 'replacements.delete');
		//JToolBarHelper::help('about.html', true);

		// Display it all
		parent::display($tpl);
	}
}