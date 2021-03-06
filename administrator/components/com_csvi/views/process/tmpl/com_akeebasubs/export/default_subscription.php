<?php
/**
 * Export orders
 *
 * @package 	CSVI
 * @subpackage 	Export
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_subscription.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('ordernostart', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ordernostart', 'order'); ?> <?php echo $this->form->getInput('ordernoend', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderlist', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderlist', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderdaterange', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderdaterange', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderdatestart', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderdatestart', 'order'); ?> <?php echo $this->form->getInput('orderdateend', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderstatus', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderstatus', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderpayment', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderpayment', 'order'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('orderpricestart', 'order'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('orderpricestart', 'order'); ?> <?php echo $this->form->getInput('orderpriceend', 'order'); ?></div></li>
	</ul>
<div class="fltlft">
	<div class="option_label"><?php echo $this->form->getLabel('orderuser', 'order'); ?></div>
	<div class="option_value"><?php echo $this->form->getInput('orderuser', 'order'); ?></div>
</div>

<div class="fltlft ordersearch">
    <div id="searchuser"><input type="text" name="searchuserbox" id="searchuserbox" value="<?php echo JText::_('COM_CSVI_SEARCH'); ?>" /></div>
    <div class="clr"></div>

	<div>
		<table id="selectuserid" class="adminlist">
			<thead>
			<tr><th><?php echo JText::_('COM_CSVI_EXPORT_USER_ID'); ?></th><th><?php echo JText::_('COM_CSVI_EXPORT_USERNAME');?></th></tr>
			</thead>
		</table>
	</div>
</div>


<div class="clr"></div>

<div class="fltlft">
		<div class="option_label"><?php echo $this->form->getLabel('orderproduct', 'order'); ?></div>
		<div class="option_value"><?php echo $this->form->getInput('orderproduct', 'order'); ?></div>
</div>

<div class="fltlft ordersearch">
    <div id="searchproduct"><input type="text" name="searchproductbox" id="searchproductbox" value="<?php echo JText::_('COM_CSVI_SEARCH'); ?>" /></div>
    <div class="clr"></div>

	<div>
		<table id="selectproductsku" class="adminlist">
			<thead>
			<tr><th><?php echo JText::_('COM_CSVI_EXPORT_LEVEL_ID'); ?></th><th><?php echo JText::_('COM_CSVI_EXPORT_LEVEL_TITLE');?></th></tr>
			</thead>
		</table>
	</div>
</div>
</fieldset>
<div class="clr"></div>
<script type="text/javascript">
jQuery('#searchuserbox, #searchproductbox').live('focus', function() {
	jQuery(this).val('');
});
</script>