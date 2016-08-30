<?php
/**
* @package Component Excel / CSV user Export - UkrSolution for Joomla! 1.6, 1.7, 2x, 3.x* @version 1.4.2
* @author UkrSolution
* @copyright (C) 2015 - UkrSolution
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/


defined ('_JEXEC') or die;
?>
<div class='body_excelcsv_export' name='create'>
    <br/>
    <!-- <div id="error"></div> -->
    <div class="right_col">
        <p class='head_group_users'><?= JText::_ ('TEXT_FOR_CHECKED_GROUP'); ?></p> 
        <div class="group_users"><?php
            JText::script('ERROR_FOR_ISSET_NAME_SETTINGS');
            JText::script('TEXT_FOR_WARNING_GROUP');
            $row = $this->row;

            foreach ($row as $i => $v)
            {
                print "<div class='box'><label><input class = 'input_box' type = 'checkbox' name = 'groupusers[]' value = '" . $row[$i]->id . "' checked = 'checked' ><span class='box_title'>" . $row[$i]->title . "</span></label></div>\n";
            }
            ?>
        </div>
        <p class = 'head_order_col'><?= JText::_ ('TEXT_FOR_SORTABLE'); ?></p>
        
        <div id="jsTable" count = "<?=$this->count;?>" value='<?=$this->preview;?>'></div>
            <div class="head_order_col1"><span id ='count_u'><?php print $this->users."</span> ".JText::_("TEXT_FOUND_USERS");?> </div>
            <div class="error1"><?=JText::_("TEXT_FOR_WARNING_GROUP");?></div>
        <div class="panel">    
            <div class="download">
                <div id = "start_test_export_ajax" type = "submit">
                    <div class="btn_download"></div>
                </div>
                <img id="loader" src="components/com_excelcsv_export_users/img/720.GIF">
            </div>

            <div class="save">
                <div class="btn_save">
                    <a href="#input" class="openModal1"><?= JText::_("TEXT_FOR_BUTTON_SAVE");?></a>
                    <aside id="input" class="modal">
                        <div>
                            <h2><?= JText::_ ('TEXT_FOR_LABEL_INPUT');?></h2>
                            <input type="text" id = "nameSet" class="style" maxlength="60">
                            <div class="error"></div>
                            <button  class ="btn1"><?= JText::_ ('TEXT_FOR_BUTTON_OK');?></button><img src="components/com_excelcsv_export_users/img/720.GIF" id ="ok_loader">
                            <a href="#close" title="Закрыть"></a>
                        </div>
                    </aside>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="left_col">
       <p class = 'head'><?= JText::_ ('TEXT_FOR_SAVE_GROUP'); ?></p>
        <div class = 'p'><label><input id = 'group_box_yes' type = 'radio' name = 'gr' value = 'yes' class = 'input_box' checked><span class="box_title">Yes</span></label></div>
        <div class = 'p'><label><input id = 'group_box_no' type = 'radio' name = 'gr' value = 'no' class = 'input_box'><span class="box_title">No</span></label></div><div class = 'clear'></div> <br />

        <p class = 'head'><?= JText::_ ('TEXT_FOR_SAVE_PASS'); ?></p>
        <div class = 'p'><label><input id='pass_box_yes' type = 'radio' name = 'check_pass' value = 'yes' class = 'input_box' checked><span class="box_title">Yes</span></label></div>
        <div class = 'p'><label><input id='pass_box_no' type = 'radio' name = 'check_pass' value = 'no' class = 'input_box'><span class="box_title">No</span></label></div><div class = 'clear'></div> <br />

        <p class = 'head'><?= JText::_ ('TEXT_FOR_USE_FORMAT'); ?></p>
        <div class = 'p'><label><input type = 'radio' name = 'format1' value = 'excel' class = 'input_box' checked><span class="box_title">Excel</span></label></div>
        <div class = 'p'><label><input type = 'radio' name = 'format1' value = 'csv' class = 'input_box' ><span class="box_title">CSV</span></label></div><div class = 'clear'></div>
    </div>
    <div class="clear"></div>
</div>

