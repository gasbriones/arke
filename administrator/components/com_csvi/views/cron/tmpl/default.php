<?php
/**
 * Shows the cronline to use for the chosen export
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2383 2013-03-17 09:08:01Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<div class="crontitle"><?php echo JText::_('COM_CSVI_CRONTITLE_STRING'); ?></div>
<div class="cronline"><?php echo $this->cronline; ?></div>
<div id="cronnote"><?php echo JText::_('COM_CSVI_CRONNOTE'); ?></div>
<form method="post" action="<?php echo JRoute::_('index.php?option=com_csvi'); ?>" name="adminForm" id="adminForm">
	<input type="hidden" name="view" id="view" value="" />
	<input type="hidden" name="task" id="task" value="" />
</form>
<script type="text/javascript">
Joomla.submitbutton = function(task) {
	document.adminForm.view.value = task;
	task = '';
	Joomla.submitform(task);
}
</script>