<?php
/**
 * Export Joomla Content
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_product.php 2052 2012-08-02 05:44:47Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('user_state', 'user'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('user_state', 'user'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('user_active', 'user'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('user_active', 'user'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('user_group', 'user'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('user_group', 'user'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('user_range', 'user'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('user_range', 'user'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>