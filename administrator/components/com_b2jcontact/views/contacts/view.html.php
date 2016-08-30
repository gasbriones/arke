<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * View class for a list of contacts.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_contact
 * @since       1.6
 */
class b2jcontactViewContacts extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		require_once JPATH_COMPONENT.'/helpers/contact.php';
		require_once JPATH_COMPONENT.'/helpers/b2jlanghandler.php';

		$lang_handler = new B2JLangHandler();
		if ($lang_handler->HasMessages())
		{
			$langErrors = $lang_handler->GetMessages();
			$message = 'Thank you for using B2J Contact!<br/>';
			foreach ($langErrors as $langErorr) {
				$message .= $langErorr;
			}
			$message .= "Join B2J Contact Translation group on <a target='_blank' href='http://www.transifex.com/projects/p/b2j-contact/'>Transifex</a>.";
			JFactory::getApplication()->enqueueMessage($message, "warning");
		}

		ContactHelper::addSubmenu('contacts');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item)
		{
			$item->order_up = true;
			$item->order_dn = true;
		}

		$this->addToolbar();
		//$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT.'/helpers/contact.php';
		$canDo	= ContactHelper::getActions();
		$user	= JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_B2JCONTACT_MANAGER_CONTACTS'));
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'/administrator/components/com_b2jcontact/css/component.css');		


		if ($canDo->get('core.create'))
		{
			JToolbarHelper::addNew('contact.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own')))
		{
			JToolbarHelper::editList('contact.edit');
			JToolbarHelper::custom('contacts.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
		}
		JToolBarHelper::divider();
		if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::publish('contacts.publish', 'JTOOLBAR_PUBLISH', true);
			JToolbarHelper::unpublish('contacts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		}
		JToolBarHelper::divider();
		JToolbarHelper::checkin('contacts.checkin');
		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
		{
			JToolbarHelper::deleteList('', 'contacts.delete', 'JTOOLBAR_EMPTY_TRASH');
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::trash('contacts.trash');
		}
		JToolBarHelper::divider();
		if ($canDo->get('core.admin'))
		{
			JToolbarHelper::preferences('com_b2jcontact');
		}
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.published' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
