<?php
/**
 * Install page
 *
 * @author 		Roland Dalmulder
 * @todo
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2388 2013-03-17 16:55:09Z RolandD $
 */

defined('_JEXEC') or die;
?>

<div id="installcsvi">

    <div id="versions">
		<div id="oldversionbox">
			<?php
				if ($this->selectversion == 'current') echo JText::_('COM_CSVI_NONEW_VERSION');
				else echo JText::sprintf('COM_CSVI_FOUND_VERSION', $this->selectversion);
			?>
		</div>
		<div id="newversionbox">
			<?php echo JText::sprintf('COM_CSVI_NEW_VERSION', $this->newversion); ?>
		</div>
	</div>

  <div id="rightbox">

	<div id="options">
		<div>
			<?php
				foreach ($this->installoptions as $installoption) {
					if ($installoption->value == 'availablefields') $checked = 'checked="checked"';
					else $checked = '';
					?>
					<input type="checkbox" name="installoptions[]" value="<?php echo $installoption->value; ?>" id="<?php echo $installoption->value; ?>" <?php echo $checked; ?> /><?php echo $installoption->text; ?>
					<?php
				}
			?>
		</div>
	</div>

	<div id="progress">
		<div id="steps">
			<div id="install">
				<a onclick="updateVersion('<?php echo str_ireplace('+', '', $this->selectversion); ?>'); return false;" href="#"><?php echo JText::_('COM_CSVI_INSTALL_CSVI'); ?></a>
			</div>
			<div id="spinner"></div>
			<div id="installrunning"></div>
		</div>
	</div>

   </div>


</div>

<script type="text/javascript">
function updateVersion(version) {
	jQuery('#install').hide();
	jQuery('#finished').remove();
	jQuery('#installrunning').html('<?php echo JText::_('COM_CSVI_INSTALL_LOG'); ?><hr />');
	jQuery('#spinner').html("<img src='<?php echo JURI::root(); ?>/administrator/components/com_csvi/assets/images/csvi_ajax-loading.gif' />");
	var tasks = 'upgrade';
	if (jQuery('#availablefields').is(':checked')) tasks = tasks + '.availablefields';
	if (jQuery('#sampletemplates').is(':checked')) tasks = tasks + '.sampletemplates';
	if (jQuery('#removeoldtables').is(':checked')) removeold = '1';
	else removeold = '0';
	executeTask(version, tasks);
}

function executeTask(version, tasks) {
	jQuery.ajax({
		async: false,
		url: 'index.php',
		dataType: 'json',
		data: 'option=com_csvi&task=install.upgrade&format=json&version='+version+'&tasks='+tasks+'&removeoldtables='+removeold,
		success: function(data) {
			if(typeof(data.results.error) !== 'undefined') {
				for (var i = 0; i < data.results.error.length; i++) {
					jQuery('#installrunning').append(data.results.error[i]+"<br />");
				}
				jQuery('#install').show();
			}

			for (var i = 0; i < data.results.messages.length; i++) {
				jQuery('#installrunning').append(data.results.messages[i]+"<br />");
			}

			// Check if more tasks need to be performed
			if (data.tasks !== "") {
				// Execute tasks
				executeTask(version, data.tasks);
			}
			else {
				jQuery('#spinner').remove();
				jQuery('#progress').prepend('<div id="finished"><a href="index.php?option=com_csvi&view=process"><img id="install_continue" src="<?php echo JURI::root(); ?>/administrator/components/com_csvi/assets/images/csvi_continue_48.png" /><span id="finished_text"><?php echo JText::_('COM_CSVI_INSTALL_FINISHED'); ?></span></a></div>');
			}

		},
		error:function (request, status, error) {
			jQuery('#spinner').html('<?php echo JText::_('COM_CSVI_ERROR_UPDATING_VERSION'); ?>');
			jQuery('<div>'+Joomla.JText._('COM_CSVI_ERROR_DURING_INSTALL')+jQuery.trim(request.responseText)+'</div>').dialog({width: 800, buttons: { "Close": function() { jQuery(this).dialog('close'); }}});
        }
	});
}
</script>