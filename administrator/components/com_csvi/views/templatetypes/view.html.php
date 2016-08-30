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

defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * Template types View
 */
class CsviViewTemplatetypes extends JViewLegacy {

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
		$this->templatetypes = $this->get('Items');

		// Get the pagination
		$this->pagination = $this->get('Pagination');

		// Load the user state
		$this->state = $this->get('State');

		// Render the submenu
		if (version_compare(JVERSION, '3.0', '>=')) {
			CsviHelper::addSubmenu('templatetypes');
			$this->sidebar = JHtmlSidebar::render();
		}
		else {
			// Get the panel
			$this->loadHelper('panel');
			$this->sidebar = '';
		}

		// Show the toolbar
		JToolBarHelper::title(JText::_('COM_CSVI_TEMPLATETYPES'), 'csvi_templates_48');
		JToolBarHelper::custom('templatetypes.reset', 'csvi_reset_32', 'csvi_reset_32', JText::_('COM_CSVI_RESET_SETTINGS'), false);
		JToolBarHelper::divider();
		JToolBarHelper::custom('templatetype.add', 'csvi_add_32', 'csvi_add_32','JTOOLBAR_NEW', false);
		JToolBarHelper::custom('templatetype.edit', 'csvi_edit_32', 'csvi_edit_32','JTOOLBAR_EDIT', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('templatetypes.publish', 'csvi_publish_32', 'csvi_publish_32', JText::_('JTOOLBAR_PUBLISH'), true);
		JToolBarHelper::custom('templatetypes.unpublish', 'csvi_unpublish_32', 'csvi_unpublish_32', JText::_('JTOOLBAR_UNPUBLISH'), true);
		//JToolBarHelper::help('about.html', true);

		// Display it all
		parent::display($tpl);
	}
}