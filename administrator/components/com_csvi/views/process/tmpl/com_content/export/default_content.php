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

defined( '_JEXEC' ) or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('content_language', 'general'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('content_language', 'general'); ?></div></li>
		<li><div class="option_label"><?php echo $this->form->getLabel('content_categories', 'content'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('content_categories', 'content'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>