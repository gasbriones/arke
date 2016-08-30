<?php
/**
 * General import options
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_file.php 2046 2012-07-21 21:19:21Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_FILE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('auto_detect_delimiters', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('auto_detect_delimiters', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('field_delimiter', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('field_delimiter', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('text_enclosure', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('text_enclosure', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('use_file_extension', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('use_file_extension', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('im_mac', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('im_mac', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('use_column_headers', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('use_column_headers', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('add_extra_fields', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('add_extra_fields', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('skip_first_line', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('skip_first_line', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('overwrite_existing_data', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('overwrite_existing_data', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('collect_debug_info', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('collect_debug_info', 'general'); ?></div></li>
	</ul>
</fieldset>

<fieldset class="float70">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_XML_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('xml_record_name', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('xml_record_name', 'general'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>
<script type="text/javascript">
jQuery(document).ready(function() {
	if (<?php echo $this->template->get('auto_detect_delimiters', 'general', '1'); ?> == '1') {
		jQuery('#jform_general_field_delimiter, #jform_general_text_enclosure').parent().parent().hide();
	}
});
jQuery('#jform_general_auto_detect_delimiters').live('change', function() {
	jQuery('#jform_general_field_delimiter, #jform_general_text_enclosure').parent().parent().toggle();
});

jQuery('#jform_general_use_column_headers').live('change', function() {
	if (jQuery(this).val() == 1) jQuery('#jform_general_skip_first_line').val("0");
	else {
		jQuery('#jform_general_refresh_xml_headers').val("0");
	}
});

jQuery('#jform_general_refresh_xml_headers').live('change', function() {
	if (jQuery(this).val() == 1) {
		jQuery('#jform_general_use_column_headers').val("1");
		jQuery('#jform_general_skip_first_line').val("0");
	}
});

jQuery('#jform_general_skip_first_line').live('change', function() {
	if (jQuery(this).val() == 1) {
		jQuery('#jform_general_use_column_headers').val("0");
		jQuery('#jform_general_refresh_xml_headers').val("0");
	}
});
</script>
