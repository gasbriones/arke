<?php
/**
 * User import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

class CsviModelUserimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the properties table */
	private $_user = null;

	// Public variables
	public $helper = null;
	public $id = null;
	public $group_id = null;
	public $usergroups = array();

	/**
	 * Constructor
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.4
	 */
	public function __construct() {
		parent::__construct();
		// Load the tables that will contain the data
		$this->_loadTables();
		$this->loadSettings();
    }

	/**
	 * Here starts the processing
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
	public function getStart() {
		// Load the data
		$this->loadData();

		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Load the helper
		$this->helper = new Com_Users();

		// Find the content id
		$this->id = $this->helper->getUserId();

		// Load the current content data
		$this->_user->load($this->id);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// There must be an alias and catid or category_path
		if (empty($this->email)) return false;

		// All good
		return true;
	}

	/**
	 * Process each record and store it in the database
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
	public function getProcessRecord() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();

		if ($this->id && !$template->get('overwrite_existing_data', 'general')) {
			$csvilog->addDebug(JText::sprintf('COM_CSVI_DATA_EXISTS_CONTENT', $this->alias));
			$csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_DATA_EXISTS_CONTENT', $this->alias));
		}
		else {
			$userdata = array();

			// If it is a new Joomla user but no username is set, we must set one
			if (!isset($this->username)) $userdata['username'] = $this->email;
			else $userdata['username'] = $this->username;

			// Check if we have an encrypted password
			if (isset($this->password_crypt)) {
				$userdata['password'] = $this->password_crypt;
				$this->password = true;
			}
			else if (isset($this->password)) {
				// Check if we have an encrypted password
				$salt		= JUserHelper::genRandomPassword(32);
				$crypt		= JUserHelper::getCryptedPassword($this->password, $salt);
				$password	= $crypt.':'.$salt;
				$userdata['password'] = $password;
			}

			// No user id, need to create a user if possible
			if (empty($this->_user->id)
					&& isset($this->email)
					&& isset($this->password)) {

				// Set the creation date
				$date = JFactory::getDate();
				$userdata['registerDate'] = $date->toMySQL();
			}
			else if (empty($this->_user->id)
					&& (!isset($this->email)
							|| !isset($this->password))) {
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_NEW_USER_PASSWORD_EMAIL'));
				return false;
			}

			// Only store the Joomla user if there is an e-mail address supplied
			if (isset($this->email)) {
				// Check if there is a fullname
				if (isset($this->fullname)) $userdata['name'] = $this->fullname;

				// Set the email
				$userdata['email'] = $this->email;

				// Set if the user is blocked
				if (isset($this->block)) $userdata['block'] = $this->block;

				// Set the sendEmail
				if (isset($this->sendemail)) $userdata['sendEmail'] = $this->sendemail;

				// Set the registerDate
				if (isset($this->registerdate)) $userdata['registerDate'] = $this->registerdate;

				// Set the lastvisitDate
				if (isset($this->lastvisitdate)) $userdata['lastvisitDate'] = $this->lastvisitdate;

				// Set the activation
				if (isset($this->activation)) $userdata['activation'] = $this->activation;

				// Set the params
				if (isset($this->params)) $userdata['params'] = $this->params;

				// Check if we have a group ID
				if (!isset($this->group_id) && empty($this->usergroup_name)) {
					$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_USERGROUP_NAME_FOUND'));
					return false;
				}
				else if (!isset($this->group_id)) {
					$groups = explode('|', $this->usergroup_name);
					foreach ($groups as $group) {
						$query = $db->getQuery(true);
						$query->select('id')->from('#__usergroups')->where($db->qn('title').' = '.$db->q($group));
						$db->setQuery($query);
						$this->usergroups[] = $db->loadResult();
					}

					if (empty($this->usergroups)) {
						$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_NO_USERGROUP_FOUND', $this->usergroup_name));
						return false;
					}
				}

				// Store/update the user
				if ($this->_user->save($userdata)) {
					$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_STORED'), true);
					if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_USERINFO'));
					else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_USERINFO'));

					// Empty the usergroup map table
					$query = $db->getQuery(true);
					$query->delete($db->qn('#__user_usergroup_map'));
					$query->where($db->qn('user_id').' = '.$this->_user->id);
					$db->setQuery($query);
					$db->execute();

					// Store the user in the usergroup map table
					$query = $db->getQuery(true);
					$query->insert($db->qn('#__user_usergroup_map'));
					if (!empty($this->usergroups)) {
						foreach ($this->usergroups as $group) {
							$query->values($this->_user->id.', '.$group);
						}
					}
					else $query->values($this->_user->id.', '.$this->group_id);
					$db->setQuery($query);
					// Store the map
					if ($db->execute()) {
						$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_MAP_STORED'), true);
					}
					else $csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_MAP_NOT_STORED'), true);
				}
				else {
					$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_NOT_STORED'), true);
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_USERINFO_NOT_ADDED', $this->_user->getError()));
				}
			}
			else $csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_SKIPPED'));
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the coupon related tables
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _loadTables() {
		$this->_user = $this->getTable('users');
	}

	/**
	 * Cleaning the coupon related tables
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		protected
	 * @param
	 * @return
	 * @since 		3.0
	 */
	protected function cleanTables() {
		$this->_user->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}