<?php
/**
 * Import page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_fields.php 2391 2013-03-23 21:47:44Z RolandD $
 */

defined('_JEXEC') or die;

$template_id = $this->template->getId();
$uri = JFactory::getURI();
if (empty($template_id)) { ?>
<fieldset>
	<legend>
		<?php echo JText::_('COM_CSVI_SELECT_IMPORT_FIELDS'); ?>
	</legend>
	<div>
		<div class="save_template">
			<?php echo JText::_('COM_CSVI_SAVE_IMPORT_TEMPLATE_FIRST'); ?>
		</div>
	</div>
</fieldset>
<?php }
else {
	$import_fields = $this->template->get('fields');
	$check = (version_compare(JVERSION, '3.0', '<')) ? 'checkAll('.count($import_fields).');' : 'Joomla.checkAll(this);';
	?>
	<fieldset>
		<legend>
			<?php echo JText::_('COM_CSVI_SELECT_IMPORT_FIELDS'); ?>
		</legend>
		<div id="import_fields">
			<table id="newfieldlist" class="adminlist table table-condensed table-striped">
				<thead>
					<tr>
						<th class="title"><?php echo JText::_('COM_CSVI_ADD_FIELD'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_FIELD_NAME'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_FILE_FIELD_NAME'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_DEFAULT_VALUE'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_PROCESS_FIELD'); ?></th>

				</thead>
				<tfoot>
					<tr>
						<td colspan="7" />
					</tr>
				</tfoot>
				<tbody>
					<tr>
						<!-- Add field -->
						<td class="center"><?php echo JHtml::_('link', '#', JHtml::_('image', JURI::root().'administrator/components/com_csvi/assets/images/csvi_add_16.png', JText::_('COM_CSVI_ADD')), array('id' => 'addRow')); ?>
						</td>
						<!-- Field name -->
						<td><?php echo JHtml::_('select.genericlist', $this->templatefields, '_field_name', null, 'value', 'text', null, '_field_name'); ?>
						</td>
						<!-- File field name -->
						<td id="newfield_filefieldname"><input type="text" name="_file_field_name" id="_file_field_name" value="" size="55" />
						<!-- Default value -->
						<td id="newfield_defaultvalue"><input type="text" name="_default_value" id="_default_value" value="" size="55" />
						</td>
						<!-- Process field -->
						<td id="newfield_processfield"><?php echo CsviHelper::getYesNo('_process_field', '1', '', '_process_field_default'); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<br />
			<div id="toolbar" class="toolbar-list">
				<ul>
					<li id="toolbar-quickadd" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_add_32 quickadd-button">&nbsp;</span>'.JText::_('COM_CSVI_QUICKADD'), 'id="quickadd-button"'); ?></li>
					<li id="toolbar-clone" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_clone_32 clone-button" id="clone-button">&nbsp;</span>'.JText::_('COM_CSVI_CLONE'), 'onclick="Joomla.submitbutton(\'process.copy\')"'); ?></li>
					<li id="toolbar-edit" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_edit_32">&nbsp;</span>'.JText::_('COM_CSVI_EDIT'), 'id="editlink" onclick="CsviTemplates.getHref(\''.$uri->toString(array('scheme', 'host')).'\');"'); ?></li>
					<li id="toolbar-apply" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_save_32">&nbsp;</span>'.JText::_('COM_CSVI_APPLY'), 'onclick="return CsviTemplates.saveOrder();"'); ?></li>
					<li class="divider"></li>
					<li id="toolbar-publish" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_publish_32 publish-button" id="publish-button">&nbsp;</span>'.JText::_('COM_CSVI_PUBLISH'), 'onclick="Joomla.submitbutton(\'process.publish\')"'); ?></li>
					<li id="toolbar-unpublish" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_unpublish_32 unpublish-button" id="unpublish-button">&nbsp;</span>'.JText::_('COM_CSVI_UNPUBLISH'), 'onclick="Joomla.submitbutton(\'process.unpublish\')"'); ?></li>
					<li class="divider"></li>
					<li id="toolbar-delete" class="button"><?php echo JHtml::_('link', '#', '<span class="icon-32-csvi_delete_32">&nbsp;</span>'.JText::_('COM_CSVI_DELETE'), 'onclick="return CsviTemplates.deleteFields();"'); ?>
					</li>
				</ul>
			</div>
			<div class="clr"></div>
			<table id="fieldslist" class="adminlist table table-condensed table-striped">
				<thead>
					<tr>
						<th class="center" width="5%"><input type="checkbox" name="toggle" value="" onclick="<?php echo $check; ?>" />
						</th>
						<th class="title" width="5%"><?php echo JText::_('COM_CSVI_FIELD_ORDERING'); ?>
							<?php echo JHtml::_('link', '#', JHtml::_('image', JRoute::_('administrator/components/com_csvi/assets/images/csvi_order_16.png'), JText::_('COM_CSVI_ADD'), 'class="reorder"'), 'onclick="CsviTemplates.renumberFields(); return false;"'); ?>
						</th>
						<th class="title"><?php echo JText::_('COM_CSVI_FIELD_NAME'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_FILE_FIELD_NAME'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_TEMPLATE_TEMPLATE_FIELD_NAME_LABEL'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_DEFAULT_VALUE'); ?></th>
						<th class="title"><?php echo JText::_('COM_CSVI_PROCESS_FIELD') ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8"></td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					if (is_array($import_fields)) {
						$count = 0;
						foreach ($import_fields as $key => $field) { ?>
					<tr>
						<td align="center"><?php echo JHtml::_('grid.id', $count++, $field->field_id); ?></td>
						<td class="order" style="text-align: center;"><input type="text"
							name="ordering[<?php echo $field->field_id; ?>]" size="3"
							value="<?php echo $field->ordering; ?>" />
						</td>
						<td>
							<?php echo JHtml::_('link', JRoute::_('index.php?option=com_csvi&task=templatefield.edit&tmpl=component&id='.$field->field_id.'&template_id='.$this->template->getId().'&process=import'), $field->field_name, 'class="modal" rel="{handler: \'iframe\', size: {x: 500, y: 450}}"'); ?>
							<?php echo (!empty($field->combine)) ? JHtml::_('image', JRoute::_('administrator/components/com_csvi/assets/images/csvi_add_16.png'), JText::_('COM_CSVI_COMBINE_FIELD'), 'class="fltrt"') : ''; ?>
							<?php echo (!empty($field->replace)) ? JHtml::_('image', JRoute::_('administrator/components/com_csvi/assets/images/csvi_replace_16.png'), JText::_('COM_CSVI_REPLACEMENT_FIELD'), 'class="fltrt"') : ''; ?>

						</td>
						<td><?php echo $field->file_field_name; ?></td>
						<td><?php echo $field->template_field_name; ?></td>
						<td><?php echo $field->default_value; ?></td>
						<td class="center" id="field<?php echo $field->field_id; ?>"><?php echo JHtml::_('jgrid.published', $field->process, $field->field_id, 'templatefield.', true, ''); ?></td>
					</tr>
					<?php
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</fieldset>
<?php } ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	Csvi.showSource('fromupload');
	Csvi.updateRowClass('fieldslist');
});
</script>