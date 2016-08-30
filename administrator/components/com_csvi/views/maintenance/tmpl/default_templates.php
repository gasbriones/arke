<?php
/**
 * Maintenance page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_sortcategories.php 1995 2012-05-24 14:40:49Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div class="clr"></div>
<fieldset class="adminform">
<legend><?php echo JText::_('COM_CSVI_BACKUPTEMPLATES_LABEL'); ?></legend>
	<input type="checkbox" onclick="Joomla.checkAll(this)" checked="checked" title="<?php echo JText::_('COM_CSVI_CHECK_ALL_FIELDS'); ?>" value="" name="checkall-toggle" /><?php echo JText::_('COM_CSVI_CHECK_ALL_FIELDS'); ?>
	<?php foreach ($this->templates as $key => $template) {
		if ($key > 0) {
			if (empty($template->value)) :
				if ($key > 1) echo '</ul>'; ?>
				<div><label><?php echo $template->text; ?></label></div>
				<div class="clr"></div>
				<ul>
			<?php else : ?>
				<li><input type="checkbox" checked="checked" name="templates[]" id="cb<?php echo $key; ?>" value="<?php echo $template->value?>" /><?php echo $template->text; ?></li>
			<?php endif;
		}
	}?>
</fieldset>
<div class="clr"></div>