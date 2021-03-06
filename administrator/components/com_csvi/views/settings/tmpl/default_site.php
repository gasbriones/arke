<?php
/**
 * Site settings page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default_site.php 2301 2013-01-30 19:16:42Z RolandD $
 */

defined( '_JEXEC' ) or die;
?>
<div class="width-60 fltlft">
	<fieldset class="adminform">
		<ul class="adminformlist">
			<?php foreach ($this->form->getGroup('site') as $field) : ?>
			<li>
				<?php echo $field->label; ?>
				<?php echo $field->input; ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</fieldset>
</div>
<div class="clr"></div>