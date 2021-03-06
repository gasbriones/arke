<?php
/**
 *
 * Template type editing page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: edit.php 2383 2013-03-17 09:08:01Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

// Load some needed behaviors
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_csvi&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm"  id="adminForm" class="form-validate">
<div>
	<fieldset class="adminform">
		<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>
			<li><?php echo $this->form->getLabel('findtext'); ?>
			<?php echo $this->form->getInput('findtext'); ?></li>
			<li><?php echo $this->form->getLabel('replacetext'); ?>
			<?php echo $this->form->getInput('replacetext'); ?></li>
			<li><?php echo $this->form->getLabel('multivalue'); ?>
			<?php echo $this->form->getInput('multivalue'); ?></li>
			<li><?php echo $this->form->getLabel('method'); ?>
			<?php echo $this->form->getInput('method'); ?></li>
			<li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li>
		</ul>
	</fieldset>
</div>
<div class="clr"></div>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>