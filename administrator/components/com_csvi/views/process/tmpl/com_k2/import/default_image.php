<?php
/**
 * Import path options
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_image.php 2059 2012-08-04 13:36:46Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset class="float31">
	<legend><?php echo JText::_('COM_CSVI_IMPORT_GENERAL_IMAGES'); ?></legend>
	<ul>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('process_image', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('process_image', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('resize_max_width', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('resize_max_width', 'image'); ?></div></li>
		<li><div class="option_label_image"><?php echo $this->form->getLabel('resize_max_height', 'image'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('resize_max_height', 'image'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>