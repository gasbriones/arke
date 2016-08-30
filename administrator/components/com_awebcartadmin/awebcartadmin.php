<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import joomla controller library
jimport('joomla.application.component.controller');

$document = JFactory::getDocument();
$path = substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],"/administrator"));
$document->addStyleSheet($path."/administrator/components/com_awebcartadmin/aweb.css");

// Get an instance of the controller prefixed by awebcartadmin
$controller = JController::getInstance('awebcartadmin');

 // Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';

	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}


// Create the controller
$classname	= 'awebcartadminController'.$controller;
$controller	= new $classname( );


		

// Get the task
$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', "", 'STR' );
 
// Perform the Request task
$controller->execute($task);
 
// Redirect if set by the controller
$controller->redirect();



