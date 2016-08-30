<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controllerform');
 

class awebcartadminControllerDelete extends JControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function emptys() {
		
		$option = JRequest::getCmd('option');
		$model = $this->getModel('awebcartadmin');
		$msg = $model->deleteEmpty();
		$this->setRedirect('index.php?option='.$option,$msg);
   	}

	public function ordered() {
		
		$option = JRequest::getCmd('option');
		$model = $this->getModel('awebcartadmin');
		$msg = $model->deleteOrdered();
		$this->setRedirect('index.php?option='.$option,$msg);
   	}
	
	
	
	public function older() {
		
		$option = JRequest::getCmd('option');
		$model = $this->getModel('awebcartadmin');
		$date = JRequest::getVar('date');
		$msg = $model->deleteOlder($date);
		$this->setRedirect('index.php?option='.$option,$msg);
   	}
	
	public function selected() {
		
		$option = JRequest::getCmd('option');
		$model = $this->getModel('awebcartadmin');
		$msg = $model->deleteSelected();
		$this->setRedirect('index.php?option='.$option,$msg);
   	}

	public function cronenable() {
		
		$option = JRequest::getCmd('option');
		$model = $this->getModel('awebcartadmin');
		$mode = JRequest::getVar('mode');
		$model->setCronMode($mode);
		$msg = "Cron Mode changed";
		$this->setRedirect('index.php?option='.$option,$msg);
   	}

}





	
    