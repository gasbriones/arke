<?php
/**
 * Import file
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2389 2013-03-21 09:03:25Z RolandD $
 */

defined('_JEXEC') or die;
$jinput = JFactory::getApplication()->input;
?>
<form method="post" action="index.php" id="adminForm" name="adminForm">
	<table class="adminlist table table-condensed table-striped" id="progresstable" style="width: 45%;">
		<thead>
		<tr><th colspan="2" style="white-space:nowrap;"><?php echo JText::sprintf('COM_CSVI_TEMPLATE_NAME', $this->template_name); ?></th></tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="2">
					<div id="progressbar"></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<tr><td><?php echo JText::_('COM_CSVI_RECORDS_PROCESSED'); ?></td><td><div id="status"></div></td></tr>
			<tr><td><?php echo JText::_('COM_CSVI_TIME_RUNNING'); ?></td><td><div class="uncontrolled-interval"><span></span></div></td></tr>
			<tr><td colspan="2"><img id="spinner" src='<?php echo JURI::root(); ?>/administrator/components/com_csvi/assets/images/csvi_ajax-loading.gif' /></td></tr>
		</tbody>
	</table>
	<div id="preview">
		<table id="tablepreview" class="adminlist" style="empty-cells: show;">
		<thead></thead>
		<tfoot></tfoot>
		<tbody></tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_csvi" />
</form>
<script type="text/javascript">
jQuery(function() {
	<?php if ($jinput->get('do_preview', false, 'bool')) { ?>
		jQuery('#toolbar-csvi_import_32').hide();
	<?php } ?>
	startTime();
	doImport();
});

// Build the timer
function startTime() {
	jQuery(".uncontrolled-interval span").everyTime(1000, 'importcounter', function(i) {
		if (<?php echo ini_get('max_execution_time'); ?> > 0 && i > <?php echo ini_get('max_execution_time'); ?>) {
			jQuery("#spinner").remove();
			jQuery("#progress").remove();
			jQuery(this).html('<?php echo addslashes(JText::_('COM_CSVI_MAX_IMPORT_TIME_PASSED')); ?>');
		}
		else {
			jQuery(this).html(i);
			var ptime = (100 / <?php echo ini_get('max_execution_time'); ?>) * i;
			jQuery("#progressbar").progressbar({ value: ptime });
		}
	});
}

// Catch the submitbutton
function submitbutton(task) {
	if (task == 'doimport') {
		jQuery('#toolbar-csvi_import_32').hide();
		jQuery('#preview').remove();
		jQuery('#progresstable').show();
		doImport();
		return true;
	}
	else {
		submitform(task);
	}
}

// Start the import
function doImport() {
	jQuery.ajax({
		async: true,
		url: 'index.php',
		dataType: 'json',
		data: 'option=com_csvi&task=importfile.doimport&format=json',
		success: function(data) {
			if (data) {
				if (data.process == true) {
					jQuery('#status').html(data.records);
					jQuery(".uncontrolled-interval span").stopTime('importcounter');
					startTime();
					doImport();
				}
				else {
					jQuery(".uncontrolled-interval span").stopTime('importcounter');
					window.location = data.url;
				}
			}
		},
		error:function (request, status, error) {
			jQuery('<div>'+Joomla.JText._('COM_CSVI_ERROR_DURING_PROCESS')+"\n\n"+'Status error: '+request.status+"\n"+'Status message: '+request.statusText+"\n"+jQuery.trim(request.responseText)+'</div>').dialog({width: 800});
        }
	});
}
</script>