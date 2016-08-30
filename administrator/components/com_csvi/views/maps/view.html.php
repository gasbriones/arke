<?php
/**
 * Maps view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.html.php 2281 2013-01-04 13:34:58Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Maps View
 */
class CsviViewMaps extends JViewLegacy {

	/**
	 * Items to be displayed
	 */
	protected $items;

	/**
	 * Pagination for the items
	 */
	protected $pagination;

	/**
	 * User state
	 */
	protected $state;

	/**
	* Maps view display method
	* @return void
	* */
	function display($tpl = null) {

		// Load the logs
		$this->items = $this->get('Items');

		// Get the pagination
		$this->pagination = $this->get('Pagination');

		// Load the user state
		$this->state = $this->get('State');

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('maps');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_MAPS'), 'csvi_map_48');
		JToolBarHelper::custom('map.add', 'csvi_add_32', 'csvi_add_32','JTOOLBAR_NEW', false);
		JToolBarHelper::custom('map.edit', 'csvi_edit_32', 'csvi_edit_32','JTOOLBAR_EDIT', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('maps.delete', 'csvi_delete_32', 'csvi_delete_32', JText::_('JTOOLBAR_DELETE'), true);

		// Display it all
		parent::display($tpl);
	}
}