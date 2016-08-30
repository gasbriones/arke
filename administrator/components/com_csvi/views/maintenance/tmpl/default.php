<?php
/**
 * Maintenance page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2390 2013-03-23 16:54:46Z RolandD $
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form method="post" action="<?php echo JRoute::_('index.php?option=com_csvi'); ?>" id="adminForm" name="adminForm" enctype="multipart/form-data">
		<div>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_CSVI_MAKE_CHOICE_MAINTENANCE'); ?></legend>
				<div>
					<?php echo JHtml::_('select.genericlist', $this->components, 'component', 'onchange=CsviMaint.loadOperation(this.value)','value', 'text', null, false, true); ?>
					<?php echo JHtml::_('select.genericlist', $this->options, 'operation', 'onchange=CsviMaint.loadOptions(this.value)'); ?>
				</div>
				<div id="optionfield"></div>
			</fieldset>
		</div>
		<input type="hidden" name="task" value="" />
		<!-- Used to generate the correct cron line -->
		<input type="hidden" name="from" value="maintenance" />

	</form>
</div>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (jQuery('#operation').val() == '') {
			jQuery('<div title="<?php echo JText::_('COM_CSVI_ERROR'); ?>"><div class="dialog-important"></div><div class="dialog-text"><?php echo JText::_('COM_CSVI_NO_CHOICE'); ?></div></div>').dialog({buttons: { "<?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>": function() { jQuery(this).dialog('close'); }}});
			return false;
		}
		else {
			if (task == 'cron.cron') {
				if (document.adminForm.task.value == 'maintenance.restoretemplates' || document.adminForm.task.value == 'maintenance.backuptemplates') {
					jQuery('<div title="<?php echo JText::_('COM_CSVI_NOTICE'); ?>"><div class="dialog-info"></div><div class="dialog-text"><?php echo JText::_('COM_CSVI_OPTION_CRON_NO_SUPPORT'); ?></div></div>').dialog({buttons: { "<?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>": function() { jQuery(this).dialog('close'); }}});
					return false;
				}
				// else document.adminForm.view.value = 'cron';
			}
			else task = document.adminForm.task.value;
			Joomla.submitform(task);
		}
	}
</script>