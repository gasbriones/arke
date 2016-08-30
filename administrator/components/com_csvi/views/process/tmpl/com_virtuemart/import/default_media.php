<?php
/**
 * Import product options
 *
 * @package 	CSVI
 * @subpackage 	Import
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_media.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend><?php echo JText::_('COM_CSVI_OPTIONS'); ?></legend>
	<ul>
		<li><div class="option_label"><?php echo $this->form->getLabel('ignore_non_exist', 'media'); ?></div>
			<div class="option_value"><?php echo $this->form->getInput('ignore_non_exist', 'media'); ?></div></li>
	</ul>
</fieldset>
<div class="clr"></div>