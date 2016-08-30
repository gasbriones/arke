<?php
/**
* @package Component Excel / CSV user Export - UkrSolution for Joomla! 1.6, 1.7, 2x, 3.x* @version 1.4.2
* @author UkrSolution
* @copyright (C) 2015 - UkrSolution
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/



defined ('_JEXEC') or die;

jimport ('joomla.application.component.controller');
jimport ('joomla.application.component.view');
jimport ('joomla.application.component.model');

if (!class_exists ('JControllerLegacy') AND class_exists ('JController'))
{

    Class JControllerLegacy extends JController
    {

    }
}
if (!class_exists ('JViewLegacy') AND class_exists ('JView'))
{

    Class JViewLegacy extends JView
    {

    }
}
if (!class_exists ('JModelLegacy') AND class_exists ('JModel'))
{

    Class JModelLegacy extends JModel
    {

    }
}


$controller = JControllerLegacy::getInstance ('export');
$controller->execute (JRequest::getCmd ('task', 'display'));
$controller->redirect ();
