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
jimport('joomla.application.component.helper');
class ExportModelExport extends JModelLegacy
{
    function constant($constant)
    {
        return array($constant => JText::_($constant));
    }
   
    function Data ()
    {
        $db = JFactory::getDBO ();
        $this->q = "SELECT id, title FROM #__usergroups";
        $db->setQuery ($this->q);
        $row = $db->LoadObjectList ();
        return $row;
    }

   
    public function excel ()
    {
       

        $groupusers = JRequest::getVar ('groupusers', '', 'post');
        $savepass = JRequest::getVar ('check_pass', '', 'post');
        $grouptd = JRequest::getVar ('gr', '', 'post');
        $email = JRequest::getVar ('email', '', 'post');
        $name = JRequest::getVar ('name', '', 'post');
        $username = JRequest::getVar ('username', '', 'post');
        $group = JRequest::getVar ('group', '', 'post');
        $pass = JRequest::getVar ('pass', '', 'post');
        $format = JRequest::getVar ('format1', '', 'post');
        $profileName = JRequest::getVar ('profileName', '', 'post');
        $result = true;
        $message = "";
        $db = JFactory::getDBO ();
        $dir = JPATH_COMPONENT_ADMINISTRATOR . '/tmp/';
        $scan = scandir (JPATH_COMPONENT_ADMINISTRATOR . '/tmp/', 1);
        $PHPExcelFormat = null;
       
        for ($i = 0; $i < count ($scan); $i++)
        {
            if ($scan[$i] == "." or $scan[$i] == ".." or $scan[$i] == "index.html")
                continue;

            unlink ($dir . $scan[$i]);
        }
       
        $objPHPExcel = new PHPExcel();
        $sheet = $objPHPExcel->getActiveSheet ();
       
        if (isset ($groupusers) and $groupusers != '')
        {
           
            if ($grouptd == 'yes')
            {
                $sheet->getColumnDimension ('D')->setAutoSize (true);
            }
           
            if ($savepass == 'yes')
            {
                $sheet->getColumnDimension ('D')->setAutoSize (true);
                $sheet->getColumnDimension ('E')->setAutoSize (true);
            }
           
            $order = array ('u.name' => $name,
                'u.email' => $email,
                'u.username' => $username,
                'GROUP_CONCAT(ug.title) as title' => $group,
                'u.password' => $pass
            );
           
            $firstrow = array (
                'Name' => $name,
                'Email' => $email,
                'Username' => $username,
                'Password' => $pass,
                'Group' => $group
            );
           
            if ($grouptd == 'no')
            {
                unset ($firstrow['Group']);
                unset ($order['GROUP_CONCAT(ug.title) as title']);
            }
           
            if ($savepass == 'no')
            {
                unset ($firstrow['Password']);
                unset ($order['u.password']);
            }
            $order = array_flip ($order);
            ksort ($order);
            $order1 = implode (", ", $order);
            $firstrow = array_flip ($firstrow);
            ksort ($firstrow);
           
            for ($i = 1; $i <= count ($order); $i++)
            {
                $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($i - 1, 1, $firstrow[$i]);
            }
           
            if ($format == "csv")
            {
                $PHPExcelFormat = 'CSV';
                $formatfile = ".csv";
            }
            elseif ($format == "excel")
            {
                $PHPExcelFormat = 'Excel5';
                $formatfile = ".xls";
            }
            else
            {
                $result = false;
            }
            if(gettype($groupusers) != 'string')
                $groupusers1 = implode (", ", $groupusers);
            else
                $groupusers1 = $groupusers;
           
            $sheet->getColumnDimension ('A')->setAutoSize (true);
            $sheet->getColumnDimension ('B')->setAutoSize (true);
            $sheet->getColumnDimension ('C')->setAutoSize (true);
            $tr = 2;
            $date = date ("d.m.G.i");
            if($profileName == "")
                $filename = 'Users_' . $date . $formatfile;
            else
                $filename = $profileName.'_' . $date . $formatfile;
            $firstrow = array_flip ($firstrow);
           
            $sql = "SELECT $order1 FROM #__users AS u, #__user_usergroup_map AS ugm, #__usergroups AS ug WHERE u.id = ugm.user_id AND ugm.group_id = ug.id AND ug.id IN ($groupusers1) GROUP BY u.username";
            $db->setQuery($sql);
            $stmt = $db->LoadObjectList ();
           
            foreach ($stmt as $fields)
            {
                $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($firstrow['Name'] - 1, $tr, $fields->name);

                $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($firstrow['Email'] - 1, $tr, $fields->email);


                $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($firstrow['Username'] - 1, $tr, $fields->username);

               
                if ($savepass == 'yes')
                {
                   $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($firstrow['Password'] - 1, $tr, $fields->password);
                }
               
                if ($grouptd == 'yes')
                {
                    $objPHPExcel->setActiveSheetIndex (0)->setCellValueByColumnAndRow ($firstrow['Group'] - 1, $tr, $fields->title);
                }
                $tr++;
            }
        }
        else
        {
            $result = false;
            $message = JText::_ ('TEXT_FOR_WARNING_GROUP');
        }
       
        if ($PHPExcelFormat != null)
        {
            $objWriter = PHPExcel_IOFactory::createWriter ($objPHPExcel, $PHPExcelFormat);
           
            if ($format == "csv")
            {
                $objWriter->setDelimiter (';');
                $objWriter->setEnclosure ('"');
            }
           
            $objWriter->save (JPATH_COMPONENT_ADMINISTRATOR . '/tmp/' . $filename);
            $url1 = JPATH_COMPONENT_ADMINISTRATOR . '/tmp/' . $filename;
        }
       
        if ($result === false and $message == "")
        {
            $message = JText::_ ('TEXT_FOR_UNIDENTIFIED_WARNING');
        }
       
        if ($result === true)
        {
            $json = array ("result" => $result, "message" => $message, "FILE_URL" => JUri::base () . 'components/com_excelcsv_export_users/tmp/' . $filename);
        }
       
        if ($result === false)
        {
            $json = array ("result" => $result, "message" => $message, "FILE_URL" => false);
        }
        $json = json_encode ($json);
        echo ($json);
    }

   
    
    public function previewUsers($groupusers = null)
    {
        $db = JFactory::getDBO ();
        if($groupusers)
        {
            $groups = implode(',',$groupusers);
            $sql1 = "AND ug.id IN ($groups)";
        }
        else
            $sql1 = "";
        $sql = "SELECT u.name, u.email, u.username, u.password, GROUP_CONCAT(ug.title) as groupusers FROM #__users AS u, #__user_usergroup_map AS ugm, #__usergroups AS ug WHERE u.id = ugm.user_id AND ugm.group_id = ug.id $sql1 AND ug.id GROUP BY u.username LIMIT 5";
        $db->setQuery($sql);
        return $stmt = $db->LoadObjectList();
    }
   
    public function previewAjax()
    {
        $groupusers = JRequest::getVar ('groupusers', 0, 'post');
        $pre = $this->previewUsers($groupusers);
        print_r(json_encode($pre));
    }
   
    
    public function count_u($groupusers = "")
    {   
        $db = JFactory::getDBO ();
        if($groupusers == "")
        {
            $sql = "SELECT id FROM`#__usergroups`";
            $db->setQuery($sql);
            $stmt = $db->LoadObjectList();
            $groupusers = "";
            for($i = 0; $i < count($stmt); $i++)
            {
                $groupusers[$i] = $stmt[$i]->id;
            }
            $groupusers = implode(', ', $groupusers);
        }
        $sql = "SELECT COUNT(u.id) AS amount FROM `#__users` AS u, `#__user_usergroup_map` AS ug WHERE u.id = ug.user_id AND ug.group_id IN ($groupusers)";
        $db->setQuery($sql);
        $stmt = $db->LoadObjectList();
        if(!$stmt)
            {return 0;}
        else
            {return $stmt[0];}
    }
   
    public function count_users()
    {
        $groupusers = JRequest::getVar ('groupusers', 0, 'post');

        if($groupusers != 0)
        {
            $pre = $this->count_u(implode(', ', $groupusers));
             if(gettype($pre) == 'object')
                print ($pre->amount);
            else
                print($pre);
        }
        else
            print null;
    }

   
    public function save ()
    {
       
        $nameSet = JRequest::getVar ('nameSet', '', 'post');
        $groupusers = JRequest::getVar ('groupusers', '', 'post');
        $savepass = JRequest::getVar ('check_pass', '', 'post');
        $grouptd = JRequest::getVar ('gr', '', 'post');
        $email = JRequest::getVar ('email', '', 'post');
        $name = JRequest::getVar ('name', '', 'post');
        $username = JRequest::getVar ('username', '', 'post');
        $group = JRequest::getVar ('group', '', 'post');
        $pass = JRequest::getVar ('pass', '', 'post');
        $format = JRequest::getVar ('format1', '', 's');
        $db = JFactory::getDBO ();
        $json = array();
        $json['groupusers'] = implode("," ,$groupusers);
        $json['save_pass'] = $savepass;
        $json['grouptd'] = $grouptd;
        $json['email'] = $email;
        $json['name'] = $name;
        $json['username'] = $username;
        $json['group'] = $group;
        $json['pass'] = $pass;
        $json['format'] = $format;
        $json['nameSet'] = $nameSet;
        $settings = $this->Settings();
       
        if(count($settings) >= 1)
        {
            echo JText::_('TEXT_ERROR_MORE_ONE_PROFILE');
            die;
        }
       
       
        for($i = 0; $i < count($settings);$i++)
        {
            if($settings[$i]->nameSet == $nameSet)
            {
                echo 1;
                die;
            }
        }
        $count = count($settings);
        $settings[$count] = $json;

        $settings = json_encode($settings);
        $sql = "UPDATE `#__extensions` SET `params` = '$settings' WHERE `name` = 'com_excelcsv_export_users'";
        $db->setQuery($sql);
        $stmt = $db->Query();
    }
    public function Settings($amount = null)
    {
        $db = JFactory::getDBO ();
        $sql = "SELECT `params` FROM `#__extensions` WHERE `name` = 'com_excelcsv_export_users'";
        $db->setQuery($sql);
        $stmt = $db->LoadObjectList();
        $stmt = $stmt[0];
        $json = $stmt->params;
        
        $jsonArray = json_decode($json);

        if(gettype($jsonArray) == "array" && count($jsonArray) > 0)
        {
            for($i = 0; $i < count($jsonArray);$i++)
            {
                $groupusers = $jsonArray[$i]->groupusers;
                $sql1 = "SELECT COUNT(u.id) AS amount FROM #__users AS u, #__user_usergroup_map AS ug WHERE u.id = ug.user_id AND ug.group_id IN ($groupusers)";
                $db->setQuery($sql1);
                $stmt1 = $db->LoadObjectList();
                $stmt1 = $stmt1[0];
                if($amount == true)
                {
                    $jsonArray[$i]->amount = $stmt1->amount;
                }
            }
        }
        else
            $jsonArray = array();
        
        return $jsonArray;
    }
   
    public function DeleteSet()
    {
        $db = JFactory::getDBO ();
        $nameSet = JRequest::getVar ('nameSet', '', 'post');
        $settings = $this->Settings(true);
       
        for($i = 0;$i < count($settings); $i++)
        {
            if($settings[$i]->nameSet == $nameSet)
            {
                unset($settings[$i]);
            }
        }
        sort($settings);
        $json = json_encode($settings);
        $sql = "UPDATE `#__extensions` SET `params` = '$json'  WHERE `name` = 'com_excelcsv_export_users'";
        $db->setQuery($sql);
        $stmt = $db->Query();
        if($stmt)
            echo($json);
    }
}