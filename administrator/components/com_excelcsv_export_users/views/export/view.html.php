<?php
/**
* @package Component Excel / CSV user Export - UkrSolution for Joomla! 1.6, 1.7, 2x, 3.x* @version 1.4.2
* @author UkrSolution
* @copyright (C) 2015 - UkrSolution
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/


defined ('_JEXEC') or die ('Restricted access');

jimport ('joomla.application.component.controller');
jimport ('joomla.application.component.view');
jimport ('joomla.application.component.model');
jimport('joomla.application.component.helper');
class ExportViewExport extends JViewLegacy
{
    public $row;

    public function display ($tpl = null)
    {
        JFactory::getDocument ()->addStyleSheet (JUri::base () . 'components/com_excelcsv_export_users/css/modal.css', $type = 'text/css');
        JFactory::getDocument ()->addStyleSheet (JUri::base () . 'components/com_excelcsv_export_users/css/style.css', $type = 'text/css');
        print "<script src = '".JUri::base () . "components/com_excelcsv_export_users/library/jquery.js'></script>";
        print "<script src = '".JUri::base () . "components/com_excelcsv_export_users/library/jquery_ui/jquery-ui.js'></script>"; 
        print "<script src = '".JUri::base () . "components/com_excelcsv_export_users/library/jquery_ui/jquery-ui.min.js'></script>"; 
        print "<script src = '".JUri::base () . "components/com_excelcsv_export_users/js/BugReport.js'></script>";
        print "<script src = '".JUri::base () . "components/com_excelcsv_export_users/js/script.js'></script>";
        $model = $this->getModel ('export');
        if($tpl == null)
            $tpl = JRequest::getCmd ('task', null, 'get');

        $this->addToolbar ($tpl);
        $users=$model->count_u();
        if(gettype($users) == "object")
            $this->users = $users->amount;
        else
            $this->users = 0;
        $this->preview = json_encode($model->previewUsers());
        $this->count = count($model->previewUsers());
        $this->row = $model->Data ();
        $version = new JVersion;
        $short_version = $version->getShortVersion();
       
        $constant = $model->constant('TEXT_FOR_COMFIRM_DELETE');
        $constant += $model->constant('ERROR_FOR_ISSET_NAME_SETTINGS');
        $constant += $model->constant('TEXT_FOR_WARNING_GROUP');
        $constant += $model->constant('TEXT_DOWNLOAD_PROFILES');
        $constant += $model->constant('TEXT_PROFILE_USER_FOUND');
        $profiles = $model->Settings(true);
        if(JRequest::getCmd ('task', null, 'get') == null && empty($profiles))
        {
           
            header('Location: index.php?option=com_excelcsv_export_users&task=create');
        }
        print "<script>var version = 'JoomlaVersion_".$short_version."'</script>";
        print "<script>var profiles = ".json_encode($profiles)."</script>";
        print "<script>var constants = '".json_encode($constant)."'</script>";
        print "<div class='modalWindow'><div><h2></h2><div title='Close' class='close'>x</div></div></div>";
        parent::display ($tpl);
    }

    protected function addToolbar ($vName)
    {
        $document = JFactory::getDocument ();
        $style = '.icon-48-export { background: url("../administrator/components/com_excelcsv_export_users/img/export_icon_48.png");}';
        $document->addStyleDeclaration ($style);
        JToolBarHelper::title (JText::_ ('TEXT_EXCELCSV_EXPORT_USERS_TITLE'), 'export');

        JSubMenuHelper::addEntry(
            JText::_('COM_EXCELCSV_EXPORT_PROFILE'),
            'index.php?option=com_excelcsv_export_users',
            $vName == ''
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_EXCELCSV_EXPORT_PROFILE_CREATE'),
            'index.php?option=com_excelcsv_export_users&task=create',
            $vName == 'create'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_EXCELCSV_EXPORT_HELP'),
            'index.php?option=com_excelcsv_export_users&task=help',
            $vName == 'help'
        );
        JSubMenuHelper::addEntry(
            JText::_('COM_EXCELCSV_EXPORT_SUPPORT_NOTICE'),
            'index.php?option=com_excelcsv_export_users&task=subscr',
            $vName == 'subscr'
        );
    }
}