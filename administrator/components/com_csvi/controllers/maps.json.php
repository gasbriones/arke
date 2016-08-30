<?php
/**
 * Maps controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 2280 2013-01-04 10:49:02Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * Maps Controller
 */
class CsviControllerMaps extends JControllerLegacy {

	/**
	 * Create a template from mapped settings
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function createTemplate() {
		// Get the models
		$model = $this->getModel('Maps', 'CsviModel');
		$template = $this->getModel('Templates', 'CsviModel');
		$field = $this->getModel('Templatefield', 'CsviModel');
		$result = false;

		// Collect the data
		$data = $model->getTemplateData();
		if ($data) {
			if ($template_id = $template->save($data)) {
				// Set the template ID
				$jinput = JFactory::getApplication()->input;
				$jinput->set('template_id', $template_id);

				$fields = $model->getTemplateFields();
				$jinput->set('field_name', implode(',', $fields));

				// Store the template fields
				if ($field->storeTemplateField()) $result = true;
				else $result = false;
			}
			else $result = false;
		}
		else $result = false;

		echo json_encode($result);
	}
}