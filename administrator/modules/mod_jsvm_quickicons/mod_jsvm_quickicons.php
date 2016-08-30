<?php
/**
 * @package		JJ jDownloads Quick Icons
 * @author		JoomJunk
 * @copyright	Copyright (c) 2011 - 2012 - JoomJunk
 * @license		http://www.gnu.org/licenses/gpl-3.0.html
 */
 
  /**
 * @modified to adapt for Virtuemart Quickicons by JoomSpot.net
 */ 
 
// no direct access
defined('_JEXEC') or die('Restricted access');

// Make sure Virtuemart is installed, or quit.
$vm_installed = @file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_virtuemart'.DS.'version.php');
if(!$vm_installed) return;

// JoomJunk reference parameters
$mod_name = "mod_jsvm_quickicons";

// API
$mainframe	= JFactory::getApplication();
$document 	= JFactory::getDocument();

// QuickIcons include
$quickIconsFile = JPATH_ADMINISTRATOR.'/modules/mod_jsvm_quickicons/default_quickicons.php';

// Append CSS to the document's head
$document->addStyleSheet(JURI::root().'administrator/modules/'.$mod_name.'/tmpl/css/style.css');

// Output content with template
require(JModuleHelper::getLayoutPath($mod_name,'default'));