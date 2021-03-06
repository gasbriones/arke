<?php
/**
 * General import options
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_file.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_FILE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_filename', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_filename', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_file', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_file', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_site', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_site', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('field_delimiter', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('field_delimiter', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('text_enclosure', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('text_enclosure', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('include_column_headers', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('include_column_headers', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('signature', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('signature', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('export_frontend', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('export_frontend', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('collect_debug_info', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('collect_debug_info', 'general'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_EXPORT_FILTER_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('recordstart', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('recordstart', 'general'); ?> <?php echo $this->form->getInput('recordend', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('groupby', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('groupby', 'general'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>
<script type="text/javascript">
jQuery(document).ready(function() {
	Csvi.loadExportSites(jQuery("#jform_general_export_file").val(), '<?php echo $this->form->getValue('export_site', 'general'); ?>');
});

jQuery("#jform_general_export_file").live('change', function() {
	Csvi.loadExportSites(jQuery(this).val(), '');
});
</script>
