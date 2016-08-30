<?php
/**
 * Form override class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: csviform.php 2396 2013-03-24 11:55:23Z RolandD $
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form override class
 */
abstract class JFormFieldCsviForm extends JFormFieldList {

	protected $type = 'CsviForm';

}