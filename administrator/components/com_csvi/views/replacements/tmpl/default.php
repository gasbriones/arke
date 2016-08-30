<?php
/**
 * Replacements page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2389 2013-03-21 09:03:25Z RolandD $
 */

defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'ordering';
$check = (version_compare(JVERSION, '3.0', '<')) ? 'checkAll('.count($this->items).');' : 'Joomla.checkAll(this);';
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="<?php echo JRoute::_('index.php?option=com_csvi&view=replacements'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist table table-condensed table-striped">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="<?php echo $check; ?>" />
					</th>
					<th class="title"><?php echo JHtml::_('grid.sort', 'COM_CSVI_REPLACEMENT_NAME_LABEL', 'name', $listDirn, $listOrder); ?></th>
					<th class="title"><?php echo JText::_('COM_CSVI_REPLACEMENT_FIND_LABEL'); ?></th>
					<th class="title"><?php echo JText::_('COM_CSVI_REPLACEMENT_REPLACE_LABEL'); ?></th>
					<th class="title"><?php echo JHtml::_('grid.sort', 'COM_CSVI_REPLACEMENT_METHOD_LABEL', 'method', $listDirn, $listOrder); ?></th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
						<?php if ($saveOrder) :?>
							<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'replacements.saveorder'); ?>
						<?php endif; ?>
					</th>
				</tr>
			<thead>
			<tfoot>
				<tr>
					<td colspan="6"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php
	        if (!empty($this->items)) {
	          foreach ($this->items as $i => $item) {
	          	$ordering	= ($listOrder == 'ordering');
	          ?>
	          <tr class="row<?php echo $i % 2; ?>">
	            <td align="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
	            <td><?php
	                if ($item->checked_out) {
	                  echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'replacements.');
	                  echo $this->escape($item->name);
	                }
	                else { ?>
	                  <a href="<?php echo JRoute::_('index.php?option=com_csvi&task=replacement.edit&id='.(int) $item->id); ?>">
	                    <?php echo $this->escape($item->name); ?>
	                  </a>
	                <?php } ?>
	            </td>
	            <td><?php echo substr(htmlspecialchars($item->findtext), 0, 100); if (strlen($item->findtext) > 100) echo '...';?></td>
	            <td><?php echo substr(htmlspecialchars($item->replacetext), 0, 100); if (strlen($item->replacetext) > 100) echo '...'; ?></td>
	            <td><?php echo JText::_('COM_CSVI_REPLACEMENT_'.$item->method); ?></td>
	            <td class="order">
	            	<?php if ($saveOrder) : ?>
						<span><?php echo $this->pagination->orderUpIcon($i, true, 'replacements.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
						<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'replacements.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
					<?php endif;
					$disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
					<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
				</td>
	          </tr>
	        <?php }
	        } ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>