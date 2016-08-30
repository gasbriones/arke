<?php
/**
 * About page
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: default.php 2389 2013-03-21 09:03:25Z RolandD $
 */

defined('_JEXEC') or die;
?>
<div class="span1">
	<?php echo $this->sidebar; ?>
</div>
<div class="span11">
	<table class="adminlist table table-condensed table-striped">
		<thead>
			<tr>
				<th width="650"><?php echo JText::_('COM_CSVI_FOLDER'); ?></th>
				<th><?php echo JText::_('COM_CSVI_FOLDER_STATUS'); ?></th>
				<th><?php echo JText::_('COM_CSVI_FOLDER_OPTIONS'); ?></th>
			</tr>


		<thead>


		<tfoot>
		</tfoot>
		<tbody>
			<?php
			$i = 1;
				foreach ($this->folders as $name => $access) { ?>
			<tr>
				<td><?php echo $name; ?></td>
				<td><?php if ($access) {
					echo '<span class="writable">'.JText::_('COM_CSVI_WRITABLE').'</span>';
				} else { echo '<span class="not_writable">'.JText::_('COM_CSVI_NOT_WRITABLE').'</span>';
	} ?>

				<td><?php if (!$access) { ?>
					<form action="index.php?option=com_csvi&view=about">
						<input type="button" class="button"
							onclick="Csvi.createFolder('<?php echo $name; ?>', 'createfolder<?php echo $i; ?>'); return false;"
							name="createfolder"
							value="<?php echo JText::_('COM_CSVI_FOLDER_CREATE'); ?>" />
					</form>
					<div id="createfolder<?php echo $i;?>"></div> <?php } ?>
				</td>
			</tr>
			<?php $i++;
				} ?>
		</tbody>
	</table>
	<div class="clr"></div>
	<table class="adminlist table table-condensed table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_CSVI_ABOUT_SETTING'); ?></th>
				<th><?php echo JText::_('COM_CSVI_ABOUT_VALUE'); ?></th>
			</tr>
		</thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<td style="width: 10%"><?php echo JText::_('COM_CSVI_ABOUT_DISPLAY_ERRORS'); ?></td>
				<td><?php echo (ini_get('display_errors')) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO'); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_CSVI_ABOUT_MAGIC_QUOTES_RUNTIME'); ?></td>
				<td><?php echo (ini_get('magic_quotes')) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO'); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_CSVI_ABOUT_MAGIC_QUOTES_GPC'); ?></td>
				<td><?php echo (get_magic_quotes_gpc()) ? JText::_('COM_CSVI_YES') : JText::_('COM_CSVI_NO'); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_CSVI_ABOUT_PHP'); ?></td>
				<td><?php echo PHP_VERSION; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_CSVI_ABOUT_JOOMLA'); ?></td>
				<td><?php echo JVERSION; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_CSVI_ABOUT_DATABASE_SCHEMA_VERSION'); ?></td>
				<td><?php echo $this->schemaVersion; ?></td>
			</tr>
			<?php
				$messages = array();
				$ignore = array();
				$ignore[] = "SHOW COLUMNS IN `#__csvi_template_fields` WHERE field = 'combine' AND type = 'VARCHAR(5)'";
				foreach($this->errors as $line => $error) :
				// Filter out CHANGE statements, Joomla! can't check them correctly
				if (!in_array($error->checkQuery, $ignore)) : ?>
					<?php $key = 'COM_CSVI_MSG_DATABASE_' . $error->queryType;
					$msgs = $error->msgElements;
					$file = basename($error->file);
					$msg0 = (isset($msgs[0])) ? $msgs[0] : ' ';
					$msg1 = (isset($msgs[1])) ? $msgs[1] : ' ';
					$msg2 = (isset($msgs[2])) ? $msgs[2] : ' ';
					$messages[] = JText::sprintf($key, $file, $msg0, $msg1, $msg2); ?>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if (count($messages) > 0) :?>
				<tr>
					<td></td>
					<td>
						<div>
							<div class="error"><?php echo JText::_('COM_CSVI_MSG_DATABASE_ERRORS'); ?></div>
							<ul class="adminformlist">
								<?php foreach ($messages as $message) : ?>
									<li><?php echo $message; ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
	<div class="clr"></div>
	<br />
	<div>
		<?php echo JHtml::_('image', JURI::base().'components/com_csvi/assets/images/csvi_about_32.png', JText::_('COM_CSVI_ABOUT')); ?>
	</div>
	<table class="adminlist table table-condensed table-striped">
		<thead></thead>
		<tfoot></tfoot>
		<tbody>
			<tr>
				<th>Name:</th>
				<td>CSVI Pro</td>
			</tr>
			<tr>
				<th>Version:</th>
				<td>5.9.5</td>
			</tr>
			<tr>
				<th></th>
				<td><?php echo LiveUpdate::getIcon(); ?></td>
			</tr>
			<tr>
				<th>Coded by:</th>
				<td>RolandD Cyber Produksi</td>
			</tr>
			<tr>
				<th>Contact:</th>
				<td>contact@csvimproved.com</td>
			</tr>
			<tr>
				<th>Support:</th>
				<td><?php echo JHTML::_('link', 'http://www.csvimproved.com/', 'CSVI Homepage', 'target="_blank"'); ?>
				</td>
			</tr>
			<tr>
				<th>Copyright:</th>
				<td>Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.</td>
			</tr>
			<tr>
				<th>License:</th>
				<td><?php echo JHtml::_('link', 'http://www.gnu.org/licenses/gpl-3.0.html', 'GNU/GPL v3'); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<form name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_csvi'); ?>" method="post">
		<input type="hidden" name="task" value="" />
	</form>
</div>