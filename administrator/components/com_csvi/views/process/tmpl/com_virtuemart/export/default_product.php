<?php
/**
 * Export products
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_product.php 2344 2013-02-26 16:29:32Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('exportsef', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('exportsef', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('producturl_suffix', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('producturl_suffix', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('vm_itemid', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('vm_itemid', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('picture_limit', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('picture_limit', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('featured', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('featured', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('category_separator', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('category_separator', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('product_categories', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('product_categories', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('publish_state_categories', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('publish_state_categories', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('incl_subcategory', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('incl_subcategory', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('parent_only', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('parent_only', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('child_only', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('child_only', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('custom_title', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('custom_title', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('productskufilter', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('productskufilter', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('stocklevelstart', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('stocklevelstart', 'product'); ?> <?php echo $this->form->getInput('stocklevelend', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('shopper_groups', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('shopper_groups', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('manufacturers', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('manufacturers', 'product'); ?></div></li>
	</ul>
</fieldset>
<fieldset class="float30">
	<legend><?php echo JText::_('COM_CSVI_PRICE_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('shopper_group_price', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('shopper_group_price', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('pricefrom', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('priceoperator', 'product'); ?> <?php echo $this->form->getInput('pricefrom', 'product'); ?> <?php echo $this->form->getInput('priceto', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('price_quantity_start', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('price_quantity_start', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('price_quantity_end', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('price_quantity_end', 'product'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('targetcurrency', 'product'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('targetcurrency', 'product'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>