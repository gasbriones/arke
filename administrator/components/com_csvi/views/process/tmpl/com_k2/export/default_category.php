<?php
/**
 * Export category options
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_category.php 2048 2012-07-28 16:27:43Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('category_separator', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('category_separator', 'general'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>