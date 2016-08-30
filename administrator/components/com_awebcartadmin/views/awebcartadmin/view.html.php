<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * awebcartadmin View
 */
class awebcartadminViewawebcartadmin extends JView
{
        /**
         * awebcartadmin view display method
         * @return void
         */
        function display($tpl = null) 
        {
                
                // Assign data to the view
					
				$this->carts = $this->get('Carts');
				$this->dbsize = $this->get('TableSize');
				$this->cronmode = $this->get('CronMode');				
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                // Display the template
                parent::display($tpl);
        }
}