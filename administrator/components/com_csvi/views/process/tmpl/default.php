<?php
/**
 * Import page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2392 2013-03-24 06:44:54Z RolandD $
 */
defined('_JEXEC') or die;

JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.keepalive');
$jinput = JFactory::getApplication()->input;
$settings = $jinput->get('settings', null, null);
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="<?php JText::_('index.php?option=com_csvi'); ?>" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
		<?php echo JHtml::_('form.token'); ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="template_id" value="" />
		<input type="hidden" name="old_template_id" value="" />
		<input type="hidden" name="template_name" value="" />
		<input type="hidden" name="boxchecked" value="" />
		<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_csvi&view=process&template_id='.$jinput->get('template_id')); ?>" />
		<!-- Used to generate cron line -->
		<input type="hidden" name="from" value="process" />

		<!-- Templates -->
		<fieldset id="template_fieldset">
			<legend><?php echo JText::_('COM_CSVI_IMPORT_TEMPLATE_DETAILS'); ?></legend>
			<div id="template_list">
				<?php echo $this->templates; ?>
				<input type="button" class="button btn" onclick="Joomla.submitbutton('process.load')" value="<?php echo JText::_('COM_CSVI_LOAD'); ?>">
				<input type="button" class="button btn" onclick="Joomla.submitbutton('process.remove')" value="<?php echo JText::_('COM_CSVI_REMOVE'); ?>">
				<input type="button" class="button btn" onclick="Joomla.submitbutton('process.save')" value="<?php echo JText::_('COM_CSVI_APPLY'); ?>">
				<input type="button" class="button btn" onclick="jQuery('#dialog-form').dialog('open');" value="<?php echo JText::_('COM_CSVI_SAVE_AS_NEW'); ?>">
			</div>
		</fieldset>

		<!-- Using Form -->
		<fieldset id="manual_fieldset">
			<legend><?php echo JText::_('COM_CSVI_PROCESS_OPTIONS'); ?></legend>
				<?php foreach ($this->form->getGroup('options') as $field) : ?>
					<?php echo $field->input; ?>
				<?php endforeach; ?>
				<?php if ($this->form->getValue('operation', 'options') == 'customexport' || $this->form->getValue('operation', 'options') == 'customimport') : ?>
					<?php echo $this->form->getInput('custom_table'); ?>
				<?php endif; ?>
			<input type="button" onclick="Joomla.submitform('process.display');" value="<?php echo JText::_('COM_CSVI_GO'); ?>" class="button btn">
		</fieldset>

		<?php
		$action = $this->form->getValue('action', 'options');
		$component = $this->form->getValue('component', 'options');
		$operation = $this->form->getValue('operation', 'options');

		if ($action && $component & $operation) {
			// Load the source template
			echo $this->loadTemplate('source');

			// Load the specific templates
			switch($action) {
				case 'import':
					?>
					<!-- Load the option template(s) in tabs -->
					<fieldset id="importtabs">
						<legend><?php echo JText::_('COM_CSVI_IMPORT_DETAILS'); ?></legend>
							<div id="process_page">
								<ul>
									<?php foreach ($this->optiontemplates as $template) { ?>
										<li><a href="#<?php echo $template; ?>_tab"><?php echo JText::_('COM_CSVI_IMPORT_'.$template); ?></a></li>
									<?php } ?>
								</ul>
								<?php foreach ($this->optiontemplates as $template) { ?>
									<div id="<?php echo $template; ?>_tab">
										<?php echo $this->loadTemplate($template); ?>
									</div>
								<?php } ?>
							</div>
					</fieldset>
					<?php
					break;
				case 'export':
					?>
					<!-- Load the option template(s) in tabs -->
					<fieldset id="exporttabs">
						<legend><?php echo JText::_('COM_CSVI_EXPORT_DETAILS'); ?></legend>
							<div id="process_page">
								<ul>
									<?php foreach ($this->optiontemplates as $template) { ?>
										<li><a href="#<?php echo $template; ?>_tab"><?php echo JText::_('COM_CSVI_EXPORT_'.$template); ?></a></li>
									<?php } ?>
								</ul>
								<?php foreach ($this->optiontemplates as $template) { ?>
									<div id="<?php echo $template; ?>_tab">
										<?php echo $this->loadTemplate($template); ?>
									</div>
								<?php } ?>
							</div>
					</fieldset>
					<?php
					break;
			}
		}?>
	</form>
</div>
<!-- The Quick Add form -->
<div id="quickadd-form" title="<?php echo JText::_('COM_CSVI_QUICK_ADD_FIELDS'); ?>">
	<fieldset>
	<table class="adminlist" id="quickadd-table">
		<tbody>
			<?php
			foreach ($this->templatefields as $fieldname) {
				?><tr><td><input type="checkbox" name="quickfields" value="<?php echo $fieldname->value; ?>" /></td><td class="addfield"><?php echo $fieldname->text; ?></td></tr><?php
			}
			?>
		</tbody>
	</table>
	</fieldset>
</div>

<div id="dialog-form" title="<?php echo JText::_('COM_CSVI_IMPORT_ADD_TEMPLATE_NAME_LABEL'); ?>">
<form>
<fieldset>
<label for="template_name"><?php echo JText::_('COM_CSVI_IMPORT_ADD_TEMPLATE_NAME_DESC'); ?></label>
<input type="text" name="template_name" id="template_name" class="text ui-widget-content ui-corner-all" />
</fieldset>
</form>
</div>
<div id="dialog-delete" class="dialog-hide" title="<?php echo JText::_('COM_CSVI_REMOVE_TEMPLATE_LABEL'); ?>">
	<div class="dialog-info"></div>
	<div class="dialog-text"><?php echo JText::_('COM_CSVI_REMOVE_TEMPLATE_DESC'); ?></div>
</div>
<script type="text/javascript">
jQuery(document).ready(function () {
	if (<?php echo $settings->get('site.cookies', 1); ?>) {
		jQuery("#process_page").tabs({
			cookie: {
				// store cookie for a day, without, it would be a session cookie
				expires: 1
			}

		});
	}
	else jQuery("#process_page").tabs();

	// Show the export location
	if ('<?php echo $action; ?>' == 'export') Csvi.showSource(document.adminForm.jform_general_exportto.value);
});
</script>

<style type="text/css">
.ui-tabs .ui-tabs-hide {
     display: none;
}
</style>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'process.save') {
		document.adminForm.template_id.value = jQuery('#select_template :selected').val();
		Joomla.submitform(task);
	}
	else if (task == 'process.remove') {
		jQuery('#dialog-delete').dialog({
			resizable: false,
			modal: true,
			buttons: {
				"<?php echo JText::_('COM_CSVI_OK'); ?>": function() {
					document.adminForm.template_id.value = jQuery('#select_template :selected').val();
					Joomla.submitform(task);
				},
				Cancel: function() {
					jQuery(this).dialog('close');
				}
			}
		});
	}
	else if (task == 'process.load') {
		// Reload the window with the template ID
		window.location = 'index.php?option=com_csvi&view=process&template_id='+jQuery('#select_template :selected').val();
	}
	else if (task == 'process.imexport') {
		if (document.adminForm.jform_options_action.value == 'import') task = 'importfile.process';
		else if (document.adminForm.jform_options_action.value == 'export') task = 'exportfile.process';
		document.adminForm.template_name.value = jQuery('#select_template :selected').text();
		document.adminForm.template_id.value = jQuery('#select_template :selected').val();
		Joomla.submitform(task);
	}
	else {
		switch (task) {
			case 'cronline':
				document.adminForm.template_id.value = jQuery('#select_template :selected').val();
				Joomla.submitform('cron.cron');
				break;
			case 'analyzer':
				Joomla.submitform('analyzer.analyzer');
				break;
		}
		Joomla.submitform(task);
	}
}

//Selects a field in the quick add list when user clicks on the name only
jQuery(".addfield").click(function() {
	var selectbox = jQuery(this).parent().children('td').children('input');
	if (jQuery(selectbox).attr('checked')) {
		jQuery(selectbox).attr('checked', false);
	}
	else {
		jQuery(selectbox).attr('checked', true);
	}
});

/**
 * Method Description
 *
 * @copyright
 * @author
 * @todo 		Check jQuery when maxHeight function works !!
 * @see
 * @access
 * @param
 * @return
 * @since
 */
 jQuery(function() {
		jQuery("#quickadd-form").dialog({
			autoOpen: false,
			height: 600,
			width: 350,
			modal: true,
			closeOnEscape: false,
			buttons: {
				"<?php echo JText::_('COM_CSVI_CHECK_ALL_FIELDS'); ?>": function() {
					jQuery("input[name='quickfields'][type='checkbox']").each(function() {
						jQuery(this).attr('checked', 'true');
					});
				},
				"<?php echo JText::_('COM_CSVI_UNCHECK_ALL_FIELDS'); ?>": function() {
					jQuery("input[name='quickfields'][type='checkbox']").each(function() {
						jQuery(this).removeAttr('checked');
					});
				},
				"<?php echo JText::_('COM_CSVI_ADD_FIELDS'); ?>": function() {
					var fieldnames = [];
					jQuery("input[name='quickfields'][type='checkbox']").each(function() {
						if (jQuery(this).is(':checked')) {
							fieldnames.push(jQuery(this).val());
						}
					});
					// Send the data to the database
					var template_id = jQuery('#select_template').val();
					if (template_id > 0) {
						jQuery.ajax({
							async: false,
							type: 'post',
							url: 'index.php',
							dataType: 'json',
							data: 'option=com_csvi&task=templatefield.storetemplatefield&format=json&template_id='+template_id+'&field_name='+fieldnames.join(','),
							success: function(data) {
								window.location = "index.php?option=com_csvi&view=process&template_id="+template_id;
							},
							error:function (request, status, error) {
								jQuery('<div>'+Joomla.JText._('COM_CSVI_ERROR_DURING_ADD_FIELDS')+jQuery.trim(request.responseText).substring(0, 2500)+'</div>').dialog();
								jQuery('#quickadd-form').dialog( "close" );
					        }
						});
					}
					jQuery(this).dialog("close");
				},
				Cancel: function() {
					jQuery(this).dialog("close");
				}
			},
			close: function() {

			}
		});

		jQuery( "#quickadd-button" )
			.click(function() {
				jQuery( "#quickadd-form" ).dialog( "open" );
		});

		jQuery( "#dialog-form" ).dialog({
			 autoOpen: false,
			 height: 200,
			 width: 450,
			 modal: true,
			 buttons: {
				 '<?php echo JText::_('COM_CSVI_SAVE_TEMPLATE'); ?>': function() {
					document.adminForm.old_template_id.value = jQuery('#select_template :selected').val();
					document.adminForm.template_name.value = jQuery('#template_name').val();
					Joomla.submitform('process.saveasnew');
				 },
				 '<?php echo JText::_('COM_CSVI_CANCEL_DIALOG'); ?>': function() {
				 	jQuery(this).dialog("close");
				 }
			 },
			 close: function() {
			 }
		});

});

//Add new field to fields list
 jQuery("#addRow").click(function() {
 	// Send the data to the database
 	var template_id = jQuery('#select_template').val();
 	if (jQuery('#jform_options_action').val() == 'import') var column_header = '';
 	else var column_header = jQuery('#_column_header').val();
 	if (jQuery('#_file_field_name').length ) var file_field_name = jQuery('#_file_field_name').val()
 	else var file_field_name = '';
 	if (template_id > 0) {
 		jQuery.ajax({
 			async: false,
 			url: 'index.php',
 			dataType: 'json',
 			type: 'post',
 			data: 'option=com_csvi&task=templatefield.storetemplatefield&format=json&template_id='+template_id
 					+'&field_name='+jQuery('#_field_name').val()
					+'&file_field_name='+file_field_name
 					+'&column_header='+column_header
 					+'&default_value='+jQuery('#_default_value').val()
 					+'&process='+jQuery('#_process_field_default').val()
 					+'&sort='+jQuery('#_sort_field_default').val(),
 			success: function(data) {
 				window.location = "index.php?option=com_csvi&view=process&template_id="+template_id;
 			},
 			error:function (request, status, error) {
 				jQuery('<div>'+Joomla.JText._('COM_CSVI_ERROR_DURING_PROCESS')+jQuery.trim(request.responseText).substring(0, 2500)+'</div>').dialog();
 	        }
 		});
 	}
 });

//Joomla overrides !!
function listItemTask(id, task) {
	jQuery.ajax({
		async: false,
		url: 'index.php',
		dataType: 'json',
		type: 'post',
		data: 'option=com_csvi&task='+task+'&format=json&cid='+id,
		success: function(data) {
			var template_id = jQuery('#select_template').val();
			window.location = "index.php?option=com_csvi&view=process&template_id="+template_id;
		},
		error: function(data, status, statusText) {
			jQuery('<div>'+statusText+'\r\n'+data.responseText+'</div>').dialog();
		}
	});
}
</script>