<?php
/**
 * Maintenance page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_sortcategories.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div class="width-80 fltlft">
<fieldset class="adminform">
<legend><?php echo JText::_('COM_CSVI_SORTCATEGORIES_LABEL'); ?></legend>
<ul>
	<!-- Language labels -->
	<li>
		<label class="hasTip" title="<?php echo JText::_('COM_CSVI_LANGUAGE_LABEL'); ?> :: <?php echo JText::_('COM_CSVI_LANGUAGE_DESC'); ?>"><?php echo JText::_('COM_CSVI_LANGUAGE_LABEL'); ?></label>
			<?php echo JHtml::_('select.genericlist', $this->languages, 'language'); ?>
	</li>
</ul>
</fieldset>
</div>
<div class="clr"></div>