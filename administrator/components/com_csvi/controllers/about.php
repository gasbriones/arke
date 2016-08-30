<?php
/**
 * About controller
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 2368 2013-03-08 14:17:15Z RolandD $
 */

defined( '_JEXEC' ) or die;

jimport('joomla.application.component.controller');

/**
 * About Controller
 */
class CsviControllerAbout extends JControllerLegacy {

  /**
	 * Tries to fix missing database updates
	 *
	 * @since	5.7
	 */
	public function fix()	{
		$model = $this->getModel('about');
		$model->fix();
		$this->setRedirect(JRoute::_('index.php?option=com_csvi&view=about', false));
	}
}