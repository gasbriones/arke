<?php
/**
 * Available fields template
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
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="index.php?option=com_csvi&view=availablefields" method="post" id="adminForm" name="adminForm">
		<div id="filterbox">
			<?php echo JText::_('COM_CSVI_AV_FILTER'); ?>:
			<?php echo $this->actions; ?>
			<?php echo $this->components; ?>
			<?php echo $this->operations; ?>
			<input type="text" value="<?php echo JRequest::getVar('filter_avfields'); ?>" name="filter_avfields" id="filter_avfields" size="25" />
			<input type="submit" class="btn" onclick="this.form.submit();" value="<?php echo JText::_('COM_CSVI_AV_GO'); ?>" />
			<input type="submit" class="btn" onclick="document.adminForm.filter_avfields.value = ''; document.adminForm.filter_idfields.checked=false;" value="<?php echo JText::_('COM_CSVI_AV_RESET'); ?>" />
			<?php
				if (JRequest::getBool('filter_idfields', 0)) $checked = 'checked="checked"';
				else $checked = '';
			?>
			<input type="checkbox" value="1" <?php echo $checked; ?> name="filter_idfields" id="filter_idfields" /><?php echo JText::_('COM_CSVI_SHOW_IDFIELDS'); ?>
			<div class="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
		</div>
		<div id="availablefieldslist" style="text-align: left;">
			<table id="available_fields" class="adminlist table table-condensed table-striped">
				<thead>
				<tr>
					<th width="20">
					<?php echo JText::_('COM_CSVI_AV_ID'); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_AV_CSVI_NAME', 'csvi_name', $listDirn, $listOrder ); ?>
					</th>
					<th class="title">
					<?php echo JText::_('COM_CSVI_AV_VM_NAME'); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_AV_TABLE', 'component_table', $listDirn, $listOrder ); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				for ($i=0, $n=count( $this->availablefields ); $i < $n; $i++) {
					$row = $this->availablefields[$i];
					?>
					<tr>
						<td align="center">
						<?php echo $this->pagination->getRowOffset($i); ?>
						</td>
						<td>
							<?php
								echo $row->csvi_name;
								if ($row->isprimary) echo '<span class="isprimary">'.JText::_('COM_CSVI_IS_PRIMARY').'</span>';
							?>
						</td>
						<td>
							<?php echo $row->component_name; ?>
						</td>
						<td>
							<?php echo $row->component_table; ?>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="option" value="com_csvi" />
		<input type="hidden" name="task" value="availablefields.display" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</form>
</div>
<script type="text/javascript">
	Csvi.updateRowClass('available_fields');
</script>