<?php
/**
 * Analyzer page
 *
 * @author 		Roland Dalmulder
 * @todo
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2159 2012-10-12 13:15:58Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form  action="index.php?option=com_csvi&view=analyzer" id="adminForm" name="adminForm" method="post" enctype="multipart/form-data">
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="process" name="process" value="1" />
		<?php echo JHtml::_('form.token'); ?>
		<div class="width-100 fltlft">
			<fieldset class="adminform">
				<ul class="adminformlist">
					<li><label for="filename"><?php echo JText::_('COM_CSVI_ANALYZER_FILENAME'); ?></label>
						<input type="file" id="filename" name="filename" size="80" /></li>
					<li><label for="columnheader"><?php echo JText::_('COM_CSVI_ANALYZER_COLUMNHEADERS'); ?></label>
						 <input	type="checkbox" id="columnheader" name="columnheader" value="1" checked="checked" /></li>
					<li><label for="lines"><?php echo JText::_('COM_CSVI_ANALYZER_LINES_TO_SHOW'); ?></label>
						<input type="text" id="lines" name="lines" value="3" size="2" /></li>
				</ul>
			</fieldset>
		</div>
	</form>
	<?php if ($this->process) : ?>
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<ol id="menulist">
				<?php if (!empty($this->items->csverrors)) { ?>
					<li><a href="#csverrors"><?php echo JText::_('COM_CSVI_ANALYZER_ERRORS'); ?></a></li>
				<?php } ?>
				<?php if (!empty($this->items->messages)) { ?>
					<li><a href="#csvmessages"><?php echo JText::_('COM_CSVI_ANALYZER_MESSAGES'); ?></a></li>
				<?php } ?>
				<?php if (!empty($this->items->fields)) { ?>
					<li><a href="#csvfields"><?php echo JText::_('COM_CSVI_ANALYZER_CSVFIELDS'); ?></a></li>
				<?php } ?>
				<?php if (!empty($this->items->csvdata)) { ?>
					<li><a href="#csvdata"><?php echo JText::_('COM_CSVI_ANALYZER_CSVDATA'); ?></a></li>
				<?php } ?>
				<?php if (!empty($this->items->recommend)) { ?>
					<li><a href="#csvrecommend"><?php echo JText::_('COM_CSVI_ANALYZER_RECOMMENDATIONS'); ?></a></li>
				<?php } ?>
			</ol>
			<?php
			// Print out any errors
			if (!empty($this->items->csverrors)) {
				?>
			<div class="msgbox">
				<fieldset class="adminform">
					<legend><span class="error"><?php echo JText::_('COM_CSVI_ANALYZER_ERRORS'); ?></span></legend>
					<div id="csverrors">
						<ol class="fields">
							<?php foreach ($this->items->csverrors as $fields) { ?>
							<li><?php echo $fields; ?></li>
							<?php } ?>
						</ol>
					</div>
				</fieldset>
			</div>
			<?php }

			// Print out any messages
			if (!empty($this->items->messages)) { ?>
			<div class="msgbox">
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_CSVI_ANALYZER_MESSAGES'); ?></legend>
					<div id="csvmessages">
						<?php echo implode('<br />', $this->items->messages); ?>
					</div>
				</fieldset>
			</div>
			<?php }

			// Print out fields
			if (!empty($this->items->fields)) { ?>
			<div class="msgbox">
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_CSVI_ANALYZER_CSVFIELDS'); ?></legend>
					<a href="#top" class="top"><?php echo JText::_('COM_CSVI_ANALYZER_TOP'); ?></a>
					<div id="csvfields">
						<ol class="fields">
							<?php foreach ($this->items->fields as $fields) { ?>
							<li><?php echo $fields; ?></li>
							<?php } ?>
						</ol>
					</div>
				</fieldset>
			</div>
			<?php }

			// Print out data
			if (!empty($this->items->csvdata)) { ?>
				<div class="msgbox">
					<fieldset class="adminform">
						<legend><?php echo JText::_('COM_CSVI_ANALYZER_CSVDATA'); ?></legend>
						<a href="#top" class="top"><?php echo JText::_('COM_CSVI_ANALYZER_TOP'); ?></a>
						<div id="csvdata">
							<div class="notice"><?php echo JText::_('COM_CSVI_ANALYZER_CSVDATA_NOTICE'); ?></div>
							<table class="data_table table table-condensed table-striped">
								<thead>
									<tr>
										<?php for ($i = 0; $i < count($this->items->csvdata[0]); $i++) { ?>
										<th><?php echo ($i+1); ?></th>
										<?php } ?>
									</tr>
								</thead>
								<tfoot></tfoot>
								<tbody>
									<?php foreach ($this->items->csvdata as $data) { ?>
									<tr>
										<?php foreach ($data as $value) { ?>
										<td><?php echo $value; ?></td>
										<?php } ?>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</fieldset>
				</div>
			<?php }

			// Print out any recommendations
			if (!empty($this->items->recommend)) { ?>
				<div class="msgbox">
					<fieldset class="adminform">
						<legend><?php echo JText::_('COM_CSVI_ANALYZER_RECOMMENDATIONS'); ?></legend>
						<a href="#top" class="top"><?php echo JText::_('COM_CSVI_ANALYZER_TOP'); ?></a>
						<div id="csvrecommend">
							<ol class="fields">
							<?php foreach ($this->items->recommend as $recommend) : ?>
								<li><?php echo $recommend; ?></li>
							<?php endforeach;?>
							</ol>
						</div>
					</fieldset>
				</div>
			<?php } ?>
		</fieldset>
	</div>
	<?php endif; ?>
</div>