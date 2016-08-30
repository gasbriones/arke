<?php
/**
 * Log settings page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2390 2013-03-23 16:54:46Z RolandD $
 */

defined('_JEXEC') or die;

// Load some behaviour
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="index.php" method="post" name="adminForm" id="adminForm">
		<input type="hidden" name="option" value="com_csvi" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
		<?php
		if (version_compare(JVERSION, '3.0', '<')) {
			echo JHtml::_('tabs.start','settings', array('useCookie'=>1));
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_SITE_SETTINGS'), 'site_settings');
					echo $this->loadTemplate('site');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_IMPORT_SETTINGS'), 'import_settings');
					echo $this->loadTemplate('import');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_GOOGLE_BASE_SETTINGS'), 'google_base_settings');
					echo $this->loadTemplate('google_base');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_YANDEX_SETTINGS'), 'yandex_settings');
					echo $this->loadTemplate('yandex');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_ICECAT_SETTINGS'), 'icecat_settings');
					echo $this->loadTemplate('icecat');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_LOG_SETTINGS'), 'log_settings');
					echo $this->loadTemplate('log');
				echo JHtml::_('tabs.panel', JText::_('COM_CSVI_SETTINGS_CUSTOM_TABLES'), 'custom_tables_settings');
					echo $this->loadTemplate('custom_tables');
			echo JHtml::_('tabs.end');
		}
		else {
			?>
			<div class="row-fluid">
				<div class="span2">
					<ul class="nav nav-tabs nav-stacked">
						<li class="active"><a data-toggle="tab" href="#site_settings"><?php echo JText::_('COM_CSVI_SETTINGS_SITE_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#import_settings"><?php echo JText::_('COM_CSVI_SETTINGS_IMPORT_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#google_base_settings"><?php echo JText::_('COM_CSVI_SETTINGS_GOOGLE_BASE_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#yandex_settings"><?php echo JText::_('COM_CSVI_SETTINGS_YANDEX_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#icecat_settings"><?php echo JText::_('COM_CSVI_SETTINGS_ICECAT_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#log_settings"><?php echo JText::_('COM_CSVI_SETTINGS_LOG_SETTINGS'); ?></a></li>
						<li><a data-toggle="tab" href="#custom_tables_settings"><?php echo JText::_('COM_CSVI_SETTINGS_CUSTOM_TABLES'); ?></a></li>
					</ul>
				</div>
				<div class="span10">
					<div class="tab-content">
						<div id="site_settings" class="tab-pane active">
							<?php echo $this->loadTemplate('site'); ?>
						</div>
						<div id="import_settings" class="tab-pane">
							<?php echo $this->loadTemplate('import'); ?>
						</div>
						<div id="google_base_settings" class="tab-pane">
							<?php echo $this->loadTemplate('google_base'); ?>
						</div>
						<div id="yandex_settings" class="tab-pane">
							<?php echo $this->loadTemplate('yandex'); ?>
						</div>
						<div id="icecat_settings" class="tab-pane">
							<?php echo $this->loadTemplate('icecat'); ?>
						</div>
						<div id="log_settings" class="tab-pane">
							<?php echo $this->loadTemplate('log'); ?>
						</div>
						<div id="custom_tables_settings" class="tab-pane">
							<?php echo $this->loadTemplate('custom_tables'); ?>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</form>
</div>

<div id="dialog-confirm" class="dialog-hide" title="<?php echo JText::_('COM_CSVI_CONFIRM_RESET_SETTINGS_TITLE'); ?>">
	<div class="dialog-info"></div>
	<div class="dialog-text"><?php echo JText::_('COM_CSVI_CONFIRM_RESET_SETTINGS_TEXT'); ?></div>
</div>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'settings.reset') {
			jQuery("#dialog-confirm").dialog({
				resizable: false,
				modal: true,
				buttons: {
					"<?php echo JText::_('COM_CSVI_RESET_SETTINGS'); ?>": function() {
						Joomla.submitform(task);
					},
					"<?php echo JText::_('COM_CSVI_CANCEL_DIALOG'); ?>": function() {
						jQuery(this).dialog("close");
					}
				}
			});

		}
		else if (document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task);
		}
		else {
			jQuery('<div title="<?php echo JText::_('COM_CSVI_ERROR'); ?>"><div class="dialog-important"></div><div class="dialog-text"><?php echo JText::_('COM_CSVI_INCOMPLETE_FORM'); ?></div></div>').dialog({buttons: { "<?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>": function() { jQuery(this).dialog('close'); }}});
		}
	}
</script>