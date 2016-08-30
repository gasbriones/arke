<?php
/**
 * Shipping rate options
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_manufacturer.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('target_language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('target_language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('vmshipment', 'shippingrate'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('vmshipment', 'shippingrate'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>