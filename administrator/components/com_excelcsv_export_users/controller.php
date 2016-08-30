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
require_once JPATH_COMPONENT_ADMINISTRATOR . '/library/PHPExcel.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/library/PHPExcel/IOFactory.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/library/PHPExcel/Writer/Excel5.php';
require_once JPATH_COMPONENT_ADMINISTRATOR . '/library/PHPExcel/Writer/CSV.php';


class ExportController extends JControllerLegacy
{
    public $excel;
    public function exportJson ()
    {
        $model = $this->getModel ('export');
        echo $model->Excel ();
        exit;
    }
    public function countUsers()
    {
        $model = $this->getModel ('export');
        echo $row = $model->count_users ();
        die;
    }
    public function previewTable()
    {
        $model = $this->getModel ('export');
        echo $row = $model->previewAjax();
        die;
    }
    public function saveSettings()
    {
        $model = $this->getModel ('export');
        echo $model->save ();
        exit;
    }
    public function DelSettings()
    {
        $model = $this->getModel ('export');
        echo $model->DeleteSet ();
        exit;
    }

    public function create()
    {
        $db = JFactory::getDBO ();

        $model = $this->getModel ('export');
        $row = $model->Data ();
        parent::display (); 
    }
}