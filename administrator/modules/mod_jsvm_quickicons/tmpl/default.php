<?php
/**
 * @package		JJ jDownloads Quick Icons
 * @author		JoomJunk
 * @copyright	Copyright (c) 2011 - 2012 - JoomJunk
 * @license		http://www.gnu.org/licenses/gpl-3.0.html
 */

  /**
 * @modified to adapt for Virtuemart Quickicons by JoomSpot.net
 */
 
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<div id="icon-wrapper">
<div id="jsvmQuickIconsTitle">
	<a href="index.php?option=com_virtuemart" title="<?php echo JText::_('JSVM_CONTROLPANEL'); ?>">
		<span>My Virtuemart Settings</span>
	</a>
</div>

<div class="jsvmcpanel">

<?php include($quickIconsFile); ?>

</div>
</div>
