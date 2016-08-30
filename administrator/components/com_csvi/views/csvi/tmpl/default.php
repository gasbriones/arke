<?php
/**
 * Default page for CSVI
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2391 2013-03-23 21:47:44Z RolandD $
 */

defined( '_JEXEC' ) or die;

if (version_compare(JVERSION, '3.0', '<')) {
?>
<div id="main">
	<div id="cpanel">
		<?php echo $this->cpanel_images->process; ?>
		<?php echo $this->cpanel_images->replacements; ?>
		<?php echo $this->cpanel_images->maps; ?>
		<?php echo $this->cpanel_images->log; ?>
		<?php echo $this->cpanel_images->maintenance; ?>
		<?php echo $this->cpanel_images->availablefields; ?>
		<br class="clear" />
		<?php echo $this->cpanel_images->tasks; ?>
		<?php echo $this->cpanel_images->analyzer; ?>
		<?php echo $this->cpanel_images->settings; ?>
		<?php echo $this->cpanel_images->about; ?>
		<?php echo $this->cpanel_images->help; ?>
		<?php echo $this->cpanel_images->install; ?>
	</div>
</div>
<?php }
else { ?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
</div>
<?php } ?>
<br class="clear" />
<?php echo LiveUpdate::getIcon(); ?>