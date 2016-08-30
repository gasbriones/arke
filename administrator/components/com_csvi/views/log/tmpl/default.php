<?php
/**
 * Log results
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2389 2013-03-21 09:03:25Z RolandD $
 */

defined( '_JEXEC' ) or die;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$check = (version_compare(JVERSION, '3.0', '<')) ? 'checkAll('.count($this->logentries).');' : 'Joomla.checkAll(this);';
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="<?php echo JRoute::_('index.php?option=com_csvi&view=log'); ?>" method="post" name="adminForm" id="adminForm">
		<div id="filterbox">
			<?php echo JText::_('COM_CSVI_FILTER'); ?>:
			<?php echo $this->lists['actions']; ?>
			<input type="submit" class="btn" onclick="this.form.submit();" value="<?php echo JText::_('COM_CSVI_GO'); ?>" />
			<input type="submit" class="btn" onclick="document.adminForm.filter_actiontype.value = '';" value="<?php echo JText::_('COM_CSVI_RESET'); ?>" />
			<div class="resultscounter"><?php echo $this->pagination->getResultsCounter(); ?></div>
		</div>
		<div id="availablefieldslist" style="text-align: left;">
			<table class="adminlist table table-condensed table-striped" id="logs">
				<thead>
				<tr>
					<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="<?php echo $check; ?>" />
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_ACTION', 'l.action',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_ACTION_TYPE', 'l.action_type',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_TEMPLATE_NAME_TITLE', 'l.template_name',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_TIMESTAMP', 'l.logstamp',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_USER', 'l.userid',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_RECORDS', 'l.records',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_RUN_CANCELLED', 'l.run_cancelled',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_CSVI_FILENAME', 'l.file_name',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
					<?php echo JText::_('COM_CSVI_RUN_ID'); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
				<?php
				//* Check for logentries
				if ($this->logentries) {
					for ($i=0, $n=count( $this->logentries); $i < $n; $i++) {
						$row = $this->logentries[$i];
						// Pseudo entry for satisfying Joomla
						$row->checked_out = 0;

						$link 	= 'index.php?option=com_csvi&task=logdetails.display&run_id[]='. $row->run_id;
						$checked = JHtml::_('grid.checkedout',  $row, $i);
						$user = JFactory::getUser($row->userid);
						?>
						<tr>
							<td>
								<?php echo $checked; ?>
							</td>
							<td>
								<a href="<?php echo $link; ?>"><?php echo JText::_('COM_CSVI_'.$row->action); ?></a>
							</td>
							<td>
								<?php echo JText::_('COM_CSVI_'.$row->action_type); ?>
							</td>
							<td>
								<?php echo $row->template_name; ?>
							</td>
							<td>
								<?php echo JHtml::_('date',$row->logstamp, 'Y-m-d H:i:s'); ?>
							</td>
							<td>
								<?php echo $user->name; ?>
							</td>
							<td>
								<?php echo $row->records; ?>
							</td>
							<td>
								<?php $run_cancelled = ($row->run_cancelled) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO');
								echo $run_cancelled;?>
							</td>
							<td>
								<?php echo $row->file_name; ?>
							</td>
							<td>
								<?php
								if (substr($row->action_type, -6) == 'import' || substr($row->action_type, -6) == 'export') {
									if (file_exists(CSVIPATH_DEBUG.'/com_csvi.log.'.$row->run_id.'.php')) {
										$attribs = 'class="modal" onclick="" rel="{handler: \'iframe\', size: {x: 950, y: 500}}"';
										echo JHTML::_('link', JRoute::_('index.php?option=com_csvi&task=log.logreader&tmpl=component&run_id='.$row->run_id), $row->run_id, $attribs);
										echo ' | ';
										echo JHTML::_('link', JRoute::_('index.php?option=com_csvi&task=log.logreader&tmpl=component&run_id='.$row->run_id), JHtml::_('image', 'administrator/components/com_csvi/assets/images/csvi_continue_16.png', $row->run_id), 'target="_new"');
										echo ' | ';
										echo JHtml::_('link', JRoute::_('index.php?option=com_csvi&task=log.downloaddebug&run_id='.$row->run_id), JHtml::_('image', 'administrator/components/com_csvi/assets/images/csvi_export_16.png', $row->run_id));
									}
									else echo $row->run_id;
								}
								else echo $row->run_id;
								?>
							</td>
						</tr>
						<?php
					}
				}
				else echo '<tr><td colspan="10" class="center">'.JText::_('COM_CSVI_NO_LOG_ENTRIES_FOUND').'</td></tr>';
				?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="task" value="log.display" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	</form>
</div>
<div id="dialog-delete" class="dialog-hide" title="<?php echo JText::_('COM_CSVI_DELETE'); ?>">
	<div class="dialog-info"></div>
	<div class="dialog-text"><?php echo JText::_('COM_CSVI_LOG_ARE_YOU_SURE_REMOVE'); ?></div>
</div>
<div id="dialog-deleteall" class="dialog-hide" title="<?php echo JText::_('COM_CSVI_DELETE_ALL'); ?>">
	<div class="dialog-info"></div>
	<div class="dialog-text"><?php echo JText::_('COM_CSVI_LOG_ARE_YOU_SURE_REMOVE_ALL'); ?></div>
</div>
<script type="text/javascript">
	Csvi.updateRowClass('logs');

	Joomla.submitbutton = function(task) {
		document.adminForm.task.value = task;
		if (task != 'logdetails.logdetails') {
			if (task == 'log.remove') {
				var modal = 'dialog-delete';
			}
			else if (task == 'log.remove_all') {
				var modal = 'dialog-deleteall';
			}
			jQuery('#'+modal).dialog({
				resizable: false,
				modal: true,
				buttons: {
					"<?php echo JText::_('COM_CSVI_OK'); ?>": function() {
						Joomla.submitform(task);
					},
					Cancel: function() {
						jQuery(this).dialog('close');
					}
				}
			});
		}
		else {
			Joomla.submitform(task);
		}
	}
</script>