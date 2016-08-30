<?php
/**
 * About model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 2395 2013-03-24 11:43:12Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

/**
 * About Model
 */
class CsviModelAbout extends JModelLegacy {

	/**
	* Check folder permissions
	*
	* @author RolandD
	* @since 2.3.10
	* @access public
	* @return array of folders and their permissions
	*/
	public function getFolderCheck() {
		jimport('joomla.filesystem.folder');
		$config = JFactory::getConfig();
		$tmp_path = JPath::clean($config->get('config.tmp_path'), '/');
		$folders = array();
		$root = JPath::clean(JPATH_ROOT, '/');
		$folders[$tmp_path] = JFolder::exists($tmp_path);
		$folders[CSVIPATH_TMP] = JFolder::exists(CSVIPATH_TMP);
		$folders[CSVIPATH_DEBUG] = JFolder::exists(CSVIPATH_DEBUG);

		return $folders;
	}

	/**
	 * Create missing folders
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function createFolder() {
		$app = JFactory::getApplication();
		jimport('joomla.filesystem.folder');
		$folder = str_ireplace(JPATH_ROOT, '', JRequest::getVar('folder'));
		return JFolder::create($folder);
	}

	/**
	 * Get database changeset
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return		Changeset class
	 * @since 		5.6
	 */
	public function getChangeSet() {
		$folder = JPATH_ADMINISTRATOR.'/components/com_csvi/sql/updates/';
		$changeSet = JSchemaChangeset::getInstance(JFactory::getDbo(), $folder);
		return $changeSet;
	}

	/**
	 * Get version from #__schemas table
	 *
	 * @copyright
	 * @author
	 * @todo
	 * @see
	 * @access
	 * @param
	 * @return  mixed  the return value from the query, or null if the query fails
	 * @throws 	Exception
	 * @since 	5.6
	 */
	public function getSchemaVersion() {
		$db = JFactory::getDbo();
		$version = false;

		// Get the extension id first
		$query = $db->getQuery(true);
		$query->select('extension_id')->from('#__extensions')->where($db->qn('type').'='.$db->q('component'))->where($db->qn('element').'='.$db->q('com_csvi'));
		$db->setQuery($query);
		$eid = $db->loadResult();

		if ($eid) {
			// Check if there is a version in the schemas table
			$query->clear();
			$query->select('version_id')->from('#__schemas')->where('extension_id = ' . $eid);
			$db->setQuery($query);
			$version = $db->loadResult();
		}

		return $version;
	}

	/**
	 * Get version from #__schemas table
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access		public
	 * @param
	 * @return  	true
	 * @since 		5.7
	 */
	public function fix() {
		$changeSet = $this->getChangeSet();
		$changeSet->fix();

		// Fix the combine field if needed
		$db = JFactory::getDbo();
		$fields = $db->getTableColumns('#__csvi_template_fields');
		if (array_key_exists('combine', $fields)) {
			// Check for the combine_char column
			$q = "ALTER TABLE `#__csvi_template_fields` CHANGE `combine` `combine_char` VARCHAR(5) NOT NULL DEFAULT '' COMMENT 'The character(s) to combine the fields' AFTER `process`;";
			$db->setQuery($q);
			$db->execute();
		}

		return true;
	}
}