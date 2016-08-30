<?php
/**
 * K2 config class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: com_virtuemart_config.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * The K2 Config Class
 */
class CsviCom_K2_Config {

	private $_k2cfg = array();

	public function __construct() {
		$this->_parse();
	}

	/**
	 * Finds a given EZ Realty setting
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param 		string $setting The config value to find
	 * @return 		mixed	value if found | false if not found
	 * @since		4.0
	 */
	public function get($setting) {
		return $this->_k2cfg->get($setting, false);
	}

	/**
	 * Parse the EZ Realty configuration
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _parse() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('element').' = '.$db->q('com_k2'))
			->where($db->qn('type').' = '.$db->q('component'));
		$db->setQuery($query);
		$params = $db->loadResult();

		$this->_k2cfg = new JRegistry();
		$this->_k2cfg->loadString($params);
	}
}