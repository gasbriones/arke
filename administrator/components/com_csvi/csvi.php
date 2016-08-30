<?php
/**
 * Admin interface
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csvi.php 2391 2013-03-23 21:47:44Z RolandD $
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_csvi')) {
	$jinput = JFactory::getApplication()->input;
	$cron = $jinput->get('cron', false, 'bool');
	if ($cron) {
		echo JText::_('JERROR_ALERTNOAUTHOR')."\n";
		return false;
	}
	else return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT_ADMINISTRATOR.'/liveupdate/liveupdate.php';
if(JRequest::getCmd('view','') == 'liveupdate') {
	LiveUpdate::handleRequest();
	return;
}

// Define our version number
define('CSVI_VERSION', '5.9.5');

// Make sure the Joomla default language is always loaded
$jlang = JFactory::getLanguage();
$jlang->load('com_csvi', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_csvi', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_csvi', JPATH_ADMINISTRATOR, null, true);

// Get the input object
$jinput = JFactory::getApplication()->input;

// Load the logger
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/log.php');

// Load the general helper
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/csvi.php');

// Load a specific helper if available
$filename = JPATH_COMPONENT_ADMINISTRATOR.'/helpers/'.$jinput->get('component').'.php';
if (file_exists($filename))	require_once($filename);

// Get the database object
$db = JFactory::getDbo();

// Define the tmp folder
$config = JFactory::getConfig();
$tmp_path = $config->get('tmp_path');
if (!defined('CSVIPATH_TMP')) define('CSVIPATH_TMP', JPath::clean($tmp_path.'/com_csvi', '/'));
if (!defined('CSVIPATH_DEBUG')) define('CSVIPATH_DEBUG', JPath::clean($tmp_path.'/com_csvi/debug', '/'));

// Set the global settings
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/settings.php');
$settings = new CsviSettings();
$jinput->set('settings', $settings);

// Start preparing
if ($jinput->get('cron', false, 'bool')) {
	// Override preview in cron mode
	$jinput->set('was_preview', true);
}
else {
	// Not doing cron, so set it to false
	$jinput->set('cron', false);

	// Add stylesheets
	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/images.css');
	$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/display.css');
	$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/tables.css');
	$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/jquery-ui.css');
	$document->addStyleSheet(JURI::root().'administrator/components/com_csvi/assets/css/jquery-csvi.css');

	// Add javascript
	if (version_compare(JVERSION, '3.0', '<')) {
		$document->addScript(JURI::root().'administrator/components/com_csvi/assets/js/jquery.js');
		$document->addScriptDeclaration('jQuery.noConflict();');
	}
	else {
		JHtml::_('bootstrap.framework');
	}
	$document->addScript(JURI::root().'administrator/components/com_csvi/assets/js/jquery.timers.js');
	$document->addScript(JURI::root().'administrator/components/com_csvi/assets/js/jquery-ui.js');
	if ($settings->get('site.cookies', 1)) $document->addScript(JURI::root().'administrator/components/com_csvi/assets/js/jquery.ck.js');
	$document->addScript(JURI::root().'administrator/components/com_csvi/assets/js/csvi.js');
	JHtml::_('behavior.modal');

	// Add language strings to JavaScript
	// General
	JText::script('COM_CSVI_ERROR');
	JText::script('COM_CSVI_NOTICE');
	JText::script('COM_CSVI_OK');
	JText::script('COM_CSVI_INFORMATION');
	JText::script('COM_CSVI_CLOSE_DIALOG');
	JText::script('COM_CSVI_CANCEL_DIALOG');

	// About view
	JText::script('COM_CSVI_ERROR_CREATING_FOLDER');

	// Maintenance view
	JText::script('COM_CSVI_CONFIRM_DB_DELETE');
	JText::script('COM_CSVI_CONFIRM_CSVITABLES_DELETE');
	JText::script('COM_CSVI_CHOOSE_RESTORE_FILE_LABEL');
	JText::script('COM_CSVI_CHOOSE_PATCH_FILE_LABEL');
	JText::script('COM_CSVI_CHOOSE_BACKUP_LOCATION_LABEL');
	JText::script('COM_CSVI_EMPTYDATABASE_LABEL');
	JText::script('COM_CSVI_ERROR_PROCESSING_RECORDS');
	JText::script('COM_CSVI_CONFIRM_CATEGORY_DELETE');
	JText::script('COM_CSVI_REMOVEEMPTYCATEGORIES_LABEL');

	// Process
	JText::script('COM_CSVI_ERROR_DURING_PROCESS');
	JText::script('COM_CSVI_CHOOSE_TEMPLATE_FIELD');
	JText::script('COM_CSVI_ALERT');

	// Install
	JText::script('COM_CSVI_ERROR_DURING_INSTALL');
}

// Include dependancies
jimport('joomla.application.component.controller');

// Create the controller
$controller = JControllerLegacy::getInstance('csvi');
$controller->execute($jinput->get('task'));
$controller->redirect();