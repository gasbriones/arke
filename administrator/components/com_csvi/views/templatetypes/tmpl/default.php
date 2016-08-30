<?php
/**
 * Templatetypes page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2390 2013-03-23 16:54:46Z RolandD $
 */

defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder == 'ordering';
$state = $this->state->get('filter.published');
if (strlen($state) == 0) $state = '*';
$components = CsviHelper::getComponents();
array_unshift($components, JHtml::_('select.option', '', JText::_('JALL')));
$check = (version_compare(JVERSION, '3.0', '<')) ? 'checkAll('.count($this->templatetypes).');' : 'Joomla.checkAll(this);';
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="<?php echo JRoute::_('index.php?option=com_csvi&view=templatetypes'); ?>" method="post" name="adminForm" id="adminForm">
		<table class="adminlist table table-condensed table-striped">
			<thead>
				<tr>
					<th></th>
					<th>
						<input type="text" name="filter_name" id="filter_name" value="<?php echo $this->state->get('filter.name'); ?>" onchange="this.form.submit();" />
						<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
						<button type="button" class="btn" onclick="document.id('filter_name').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
					</th>
					<th></th>
					<th><?php echo JHtml::_('select.genericlist', $components, 'filter_component', 'onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.component'), false, true); ?></th>
					<th><?php echo JHtml::_('select.genericlist', array('*' => JText::_('JALL'), 'import' => JText::_('COM_CSVI_IMPORT'),'export' => JText::_('COM_CSVI_EXPORT')), 'filter_process', 'onchange="this.form.submit()"', 'value', 'text', $this->state->get('filter.process'), false, true); ?></th>
					<th><?php echo JHtml::_('select.genericlist', JHtml::_('jgrid.publishedOptions'), 'filter_published', 'onchange="this.form.submit()"', 'value', 'text', $state, false, true); ?></th>
					<th></th>
				</tr>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="<?php echo $check; ?>" />
					</th>
					<th class="title"><?php echo JHtml::_('grid.sort', 'COM_CSVI_TEMPLATE_TYPE_NAME', 'template_type_name', $listDirn, $listOrder); ?></th>
					<th class="title"><?php echo JText::_('COM_CSVI_TEMPLATE_TYPE_DESC'); ?></th>
					<th class="title"><?php echo JHtml::_('grid.sort', 'COM_CSVI_COMPONENT_NAME', 'component', $listDirn, $listOrder); ?></th>
					<th class="title"><?php echo JHtml::_('grid.sort', 'COM_CSVI_TEMPLATE_PROCESS', 'template_type', $listDirn, $listOrder); ?></th>
					<th class="title">
						<?php echo JText::_('JSTATUS'); ?>
					</th>
					<th class="title" width="10%">
						<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
						<?php if ($saveOrder) :?>
							<?php echo JHtml::_('grid.order',  $this->templatetypes, 'filesave.png', 'templatetypes.saveorder'); ?>
						<?php endif; ?>
					</th>
				</tr>
			<thead>
			<tfoot>
				<tr>
					<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->templatetypes as $i => $template) {
					$ordering	= ($listOrder == 'ordering');
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $template->id); ?>
						</td>
						<td>
							<?php
							if (!empty($template->url)) echo JHtml::_('link', JRoute::_($template->url), JText::_('COM_CSVI_'.$template->template_type_name), 'target="_blank"');
							else echo JText::_('COM_CSVI_'.$template->template_type_name);
							?>
						</td>
						<td><?php echo JText::_('COM_CSVI_'.strtoupper($template->template_type_name).'_DESC'); ?></td>
						<td>
							<?php echo JHtml::_('link', JRoute::_('index.php?option='.$template->component), JText::_('COM_CSVI_'.$template->component), 'target="_blank"'); ?>
						</td>
						<td><?php echo JText::_('COM_CSVI_'.strtoupper($template->template_type)); ?></td>
						<td class="center"><?php echo JHtml::_('jgrid.published', $template->published, $i, 'templatetypes.'); ?></td>
						<td class="order"> <?php
							if ($saveOrder) {?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'templatetypes.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'templatetypes.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php
							}
							$disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $template->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<div id="dialog-confirm" class="dialog-hide" title="<?php echo JText::_('COM_CSVI_CONFIRM_RESET_TEMPLATETYPES_TITLE'); ?>">
	<div class="dialog-info"></div>
	<div class="dialog-text"><?php echo JText::_('COM_CSVI_CONFIRM_RESET_TEMPLATETYPES_TEXT'); ?></div>
</div>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'templatetypes.reset') {
			jQuery("#dialog-confirm").dialog({
				resizable: false,
				height:140,
				modal: true,
				buttons: {
					'<?php echo JText::_('COM_CSVI_RESET_TEMPLATETYPES'); ?>': function() {
						Joomla.submitform(task);
					},
					'<?php echo JText::_('COM_CSVI_CANCEL_DIALOG'); ?>': function() {
						jQuery(this).dialog("close");
					}
				}
			});

		}
		else Joomla.submitform(task);
	}
</script>