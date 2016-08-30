<?php
/**
 * Akeeba Subscriptions User import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: userinfoimport.php 2114 2012-09-09 11:03:51Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for user info
 *
 * @todo 	format registerdate
 */
class CsviModelUserimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the user table */
	private $_user = null;
	/** @var object contains the vmuser table */
	private $_aksubsusers = null;

	// Public variables
	/** @var integer contains the unique Joomla user ID */
	public $akeebasubs_user_id = null;
	public $user_id = null;
	public $email = null;
	public $usergroup_name = null;
	public $state_code = null;

	// Private variables

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
		$this->_loadCustomFields();
		$this->loadSettings();
		// Set some initial values
		$this->date = JFactory::getDate();
		$this->user = JFactory::getUser();
    }

	/**
	 * Here starts the processing
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo		change cdate/mdate to use JDate
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getStart() {
		$jinput = JFactory::getApplication()->input;

		// Load the data
		$this->loadData();

		// Load the helper
		$this->helper = new Com_Akeebasubs();

		// Get the logger
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'state':
						$this->state_code = $value;
						break;
					default:
						$this->$name = $value;
						break;
				}
			}
		}

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
	 * @since 		5.4
	 */
	public function getProcessRecord() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
	   	$userdata = array();
	   	jimport('joomla.user.helper');

		// See if we have the required fields
		if (empty($this->email) && empty($this->username)) {
			// No way to identify what needs to be updated, set error and return
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_MISSING_REQUIRED_FIELDS'));
			return false;
		}
		// Get the user ID if it is empty
		else if (empty($this->user_id)) {
			$query = $db->getQuery(true);
			$query->select($db->qn('id'))->from($db->qn('#__users'))->where($db->qn('email').' = '.$db->q($this->email));
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_FIND_USER_ID'), true);
			$this->user_id = $db->loadResult();
		}

		// Check for the akeebasubs_user_id
		if ($this->user_id && empty($this->akeebasubs_user_id)) {
			// if we have a user_id we can get the user_info_id
			$query = $db->getQuery(true);
			$query->select($db->qn('akeebasubs_user_id'))
				->from($db->qn('#__akeebasubs_users'))
				->where($db->qn('user_id').' = '.$db->q($this->user_id));
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_FIND_AKSUBS_USER'), true);
			$this->akeebasubs_user_id = $db->loadResult();
		}

		// If it is a new Joomla user but no username is set, we must set one
		if (empty($this->user_id) && !isset($this->username)) {
			$userdata['username'] = $this->email;
		}
		// Set the username
		else if (isset($this->username)) $userdata['username'] = $this->username;

		// Check if we have an encrypted password
		if (isset($this->password_crypt)) {
			$userdata['password'] = $this->password_crypt;
		}
		else if (isset($this->password)) {
			// Check if we have an encrypted password
			$salt		= JUserHelper::genRandomPassword(32);
			$crypt		= JUserHelper::getCryptedPassword($this->password, $salt);
			$password	= $crypt.':'.$salt;
			$userdata['password'] = $password;
		}

		// No user id, need to create a user if possible
		if (empty($this->user_id)
			&& !empty($this->email)
			&& isset($this->password)) {

			// Set the creation date
			$date = JFactory::getDate();
			$userdata['registerDate'] = $date->toSql();
		}
		else {
			// Set the id
			$userdata['id'] = $this->user_id;
		}

		// Set the name
		if (isset($this->name)) $userdata['name'] = $this->name;
		else {
			$fullname = false;
			if (isset($this->first_name)) $fullname .= $this->first_name.' ';
			if (isset($this->last_name)) $fullname .= $this->last_name;
			if (!$fullname) $fullname = $this->email;
			$userdata['name'] = trim($fullname);
		}

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
		if (empty($this->usergroup_name)) {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_USERGROUP_NAME_FOUND'));
			return false;
		}
		else {
			// Check for multiple user groups
			$groups = explode('|', $this->usergroup_name);
			$query = $db->getQuery(true);
			$query->select($db->qn('id'))->from($db->qn('#__usergroups'));
			foreach ($groups as $key => $groupname) {
				$groups[$key] = $db->q($groupname);
			}
			$query->where($db->qn('title').' IN ('.implode(',', $groups).')');
			$db->setQuery($query);
			$userdata['groups'] = $db->loadAssocList('id', 'id');

			if (empty($userdata['groups'])) {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_NO_USERGROUP_FOUND', $this->usergroup_name));
				return false;
			}
		}

		// Bind the data
		$this->_user->bind($userdata);

		// Store/update the user
		if ($this->_user->store()) {
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_STORED'), true);
			// Set the user ID
			$this->user_id = $this->_user->id;

			// Check if the field is a custom field used as an available field
			$this->_processCustomAvailableFields();

			// Bind the VirtueMart user data
			$this->_aksubsusers->bind($this);

			// Add the state
			if (!is_null($this->state_code)) $this->_aksubsusers->state = $this->state_code;

			// Store the Akeeba Subscriptions user info
			if ($this->_aksubsusers->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_USERINFO'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_USERINFO'));
			}
			else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_USERINFO_NOT_ADDED', $this->_aksubsusers->getError()));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_USERINFO_QUERY'), true);
		}
		else $csvilog->addDebug(JText::_('COM_CSVI_DEBUG_JOOMLA_USER_NOT_STORED'), true);

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the user info related tables
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
		$this->_aksubsusers = $this->getTable('aksubsusers');

		// Load the JUser table
		require_once(JPATH_LIBRARIES.'/joomla/database/table/user.php');
		$this->_user = new JTableUser(JFactory::getDbo());
	}

	/**
	 * Cleaning the user info related tables
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
		$this->_aksubsusers->reset();
		$this->_user->reset();
		$this->_user->id = null;

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Get a list of custom fields that can be used as available field
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.4.1
	 */
	private function _loadCustomFields() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->qn('slug'));
		$query->from('#__akeebasubs_customfields');
		$db->setQuery($query);
		$this->_customfields = $db->loadObjectlist();
		$csvilog->addDebug('Load custom fields', true);
	}

	/**
	 * Process custom fields that are used as available field
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.4.1
	 */
	private function _processCustomAvailableFields() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();

		// Create the queries
		if (!empty($this->_customfields)) {
			$fields = array();
			foreach ($this->_customfields as $field) {
				$title = $field->slug;
				$csvilog->addDebug('Processing custom available field: '.$title);
				if (isset($this->$title)) {
					$fields[$title] = $this->$title;
				}
			}
			// Convert the data
			$this->params = json_encode($fields);
		}
		else $csvilog->addDebug('No custom available fields found');
	}
}