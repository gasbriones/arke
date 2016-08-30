<?php
/**
 * ICEcat settings page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_icecat.php 2383 2013-03-17 09:08:01Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div>
	<fieldset class="adminform settings">
		<ul class="adminformlist">
			<?php foreach ($this->form->getGroup('icecat') as $field) : ?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
	<fieldset class="adminform">
		<table class="adminlist table table-condensed table-striped">
			<thead><tr><th><?php echo JText::_('COM_CSVI_ICECAT_STAT_TABLE'); ?></th><th><?php echo JText::_('COM_CSVI_ICECAT_STAT_COUNT'); ?></th></tr></thead>
			<tbody></tbody>
			<tbody>
				<tr>
					<td><?php echo JText::_('COM_CSVI_ICECAT_INDEX_COUNT'); ?></td><td><?php echo $this->icecat_stats['index']; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_CSVI_ICECAT_SUPPLIER_COUNT'); ?></td><td><?php echo $this->icecat_stats['supplier']; ?></td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<br />
	<?php echo JHtml::_('link', 'http://icecat.biz/en/menu/register/index.htm', JText::_('COM_CSVI_GET_ICECAT_ACCOUNT'), 'target="_blank"'); ?>
</div>
<div class="clr"></div>