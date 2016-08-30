<?php
/**
 * Maps page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2281 2013-01-04 13:34:58Z RolandD $
 */

defined('_JEXEC') or die;

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$check = (version_compare(JVERSION, '3.0', '<')) ? 'checkAll('.count($this->items).');' : 'Joomla.checkAll(this);';
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<form action="<?php echo JRoute::_('index.php?option=com_csvi&view=maps'); ?>" method="post" name="adminForm" id="adminForm">
		<div id="availablefieldslist" style="text-align: left;">
			<table class="adminlist table table-condensed table-striped" id="logs">
				<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="<?php echo $check; ?>" />
					</th>
					<th class="title">
						<?php echo JHtml::_('grid.sort', 'COM_CSVI_MAP_NAME', 'm.name',  $listDirn, $listOrder); ?>
					</th>
					<th class="title">
						<?php echo JText::_('COM_CSVI_MAP_TEMPLATE'); ?>
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
				if ($this->items) {
					for ($i=0, $n=count( $this->items); $i < $n; $i++) {
						$row = $this->items[$i];

						$link 	= 'index.php?option=com_csvi&task=map.edit&id='. $row->id;
						$checked = JHtml::_('grid.checkedout',  $row, $i);
						?>
						<tr>
							<td>
								<?php echo $checked; ?>
							</td>
							<td>
								<?php echo JHtml::_('link', $link, $row->name); ?>
							</td>
							<td>
								<?php echo JHtml::_('link', 'index.php?option=com_csvi&view=maps', JHtml::_('image', JURI::root().'administrator/components/com_csvi/assets/images/csvi_template_16.png', JText::_('COM_CSVI_MAKE_TEMPLATE')), 'onclick="jQuery(\'#dialog-form\').data(\'id\', \''.$row->id.'\').dialog(\'open\'); return false;"'); ?>
						</tr>
						<?php
					}
				}
				?>
				</tbody>
			</table>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<div id="dialog-form" title="<?php echo JText::_('COM_CSVI_IMPORT_ADD_TEMPLATE_NAME_LABEL'); ?>">
	<form>
		<fieldset>
			<label for="template_name"><?php echo JText::_('COM_CSVI_IMPORT_ADD_TEMPLATE_NAME_DESC'); ?></label>
			<input type="text" name="template_name" id="template_name" class="text ui-widget-content ui-corner-all" />
		</fieldset>
	</form>
</div>
<script type="text/javascript">
jQuery( "#dialog-form" ).dialog({
	autoOpen: false,
	height: 200,
	width: 450,
	modal: true,
	buttons: {
		'<?php echo JText::_('COM_CSVI_SAVE_TEMPLATE'); ?>': function() {
			var id = jQuery(this).data('id');
	 		jQuery.ajax({
				async: false,
				url: 'index.php',
				dataType: 'json',
				type: 'get',
				data: 'option=com_csvi&task=maps.createtemplate&format=json&id='+id+'&template_name='+jQuery('#template_name').val(),
				success: function(data) {
					jQuery('#template_name').val('');
					jQuery('#dialog-form').dialog("close");
					jQuery('<div title="<?php echo JText::_('COM_CSVI_INFORMATION'); ?>"><div class="dialog-info"></div><div class="dialog-text"><?php echo JText::_('COM_CSVI_TEMPLATE_CREATED'); ?></div></div>').dialog({buttons: { "<?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>": function() { jQuery(this).dialog('close'); }}});

				},
				error: function(data, status, statusText) {
					jQuery('<div title="<?php echo JText::_('COM_CSVI_ERROR'); ?>"><div class="dialog-important"></div><div class="dialog-text">'+statusText+'<br /><br />'+data.responseText+'</div></div>').dialog({buttons: { "<?php echo JText::_('COM_CSVI_CLOSE_DIALOG'); ?>": function() { jQuery(this).dialog('close'); }}});
				}
			});
	 	},
	 	'<?php echo JText::_('COM_CSVI_CANCEL_DIALOG'); ?>': function() {
	 		jQuery(this).dialog("close");
	 	}
	 },
	 close: function() {
	 }
});
</script>