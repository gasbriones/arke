<?php
/**
 * Export coupons
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_coupon.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('category_separator', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('category_separator', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('estore', 'coupon'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('estore', 'coupon'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('function_type', 'coupon'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('function_type', 'coupon'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('function_type2', 'coupon'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('function_type2', 'coupon'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('coupon_value_type', 'coupon'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('coupon_value_type', 'coupon'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('discount_type', 'coupon'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('discount_type', 'coupon'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>