<?php
/**
 * Export page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_source.php 2292 2013-01-17 16:52:51Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_EXPORT_SOURCE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('exportto', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('exportto', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('localpath', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('localpath', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftphost', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftphost', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftpport', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftpport', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftpusername', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftpusername', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftppass', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftppass', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftproot', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftproot', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('ftpfile', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ftpfile', 'general'); ?></div></li>
	</ul>
	<input type="button" value="<?php echo JText::_('COM_CSVI_TESTFTP_BUTTON'); ?>" name="testftp" id="testftp" onclick="Csvi.testFtp('export'); return false;"/>
</fieldset>
<div class="clr"></div>