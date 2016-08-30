<?php
/**
 * Export view
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: view.raw.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.view' );

/**
 * Front-end export View
 */
class CsviViewExport extends JViewLegacy {

	/**
	 * Production view display method
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function display($tpl = null) {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);

		// Check if this template can be exported from frontend
		if (!$template->get('export_frontend','general')) {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_EXPORT_ALLOWED'));
			$jinput->set('logcount', 0);
			echo JText::_('COM_CSVI_NO_EXPORT_ALLOWED');
			// Store the log results
			$this->get('StoreLogResults', 'log');
		}
		else {
			$result = $this->get('ProcessData', 'exportfile');
			if (!$result) {
				echo JText::_('COM_CSVI_EXPORT_FAILED');
			}
		}
	}
}