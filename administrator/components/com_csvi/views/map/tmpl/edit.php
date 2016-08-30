<?php
/**
 *
 * Template type editing page
 *
 * @package 	CSVI
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: edit.php 2273 2013-01-03 16:33:30Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load some needed behaviors
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_csvi&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div>
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>
				<li><?php echo $this->form->getLabel('mapfile'); ?>
				<?php echo $this->form->getInput('mapfile'); ?></li>
				<li><?php echo $this->form->getLabel('action', 'options'); ?>
				<?php echo $this->form->getInput('action', 'options'); ?></li>
				<li><?php echo $this->form->getLabel('component', 'options'); ?>
				<?php echo $this->form->getInput('component', 'options'); ?></li>
				<li><?php echo $this->form->getLabel('operation', 'options'); ?>
				<?php echo $this->form->getInput('operation', 'options'); ?></li>
			</ul>
		</fieldset>
		<div id="fieldchange" class="dialog-hide save_template"><?php echo JText::_('COM_CSVI_SAVE_MAP_FIRST'); ?></div>
		<table id="fieldmap" class="adminlist table table-condensed table-striped">
			<thead>
				<tr><th><?php echo JText::_('COM_CSVI_FILEHEADER'); ?></th><th><?php echo JText::_('COM_CSVI_TEMPLATEHEADER')?></th></tr>
			</thead>
			<tbody></tbody>
			<tbody>
				<?php if (!empty($this->item->headers)) :?>
					<?php foreach ($this->item->headers as $header) :?>
						<tr><td><?php echo $header->csvheader; ?></td><td><?php echo JHtml::_('select.genericlist', $this->templatefields, 'templateheader['.$header->csvheader.']', null, 'value', 'text', $header->templateheader); ?></tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</form>