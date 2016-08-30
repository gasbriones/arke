<?php
/**
 * @version   1.1
 * @author    emkt.mx Fernando Martínez
 * @copyright Copyright (C) 2009 - 2014 emkt.mx
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted index access');

class plgSystemJBrandRemover extends JPlugin {
    public function onAfterInitialise() {
    	$app =& JFactory::getApplication();
    	$doc =& JFactory::getDocument();
    	if ($app->isAdmin()) {
        	$doc->addStyleDeclaration( $this->params->get('admincss',''));
        } else {
			$doc->addStyleDeclaration( $this->params->get('sitecss',''));
        }
    }
}