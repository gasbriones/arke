<?php
/**
 * Content import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

class CsviModelContentimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the properties table */
	private $_content = null;
	private $_categorymodel = null;


	// Public variables
	public $helper = null;
	public $id = null;
	public $alias = null;
	public $catid = null;
	public $category_path = null;

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
		$this->helper = new Com_Content();

		// Find the content id
		$this->id = $this->helper->getContentId();

		// Load the current content data
		$this->_content->load($this->id);

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
		if (empty($this->alias) && (empty($this->catid) && empty($this->category_path))) return false;

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

		if ($this->id && !$template->get('overwrite_existing_data', 'general')) {
			$csvilog->addDebug(JText::sprintf('COM_CSVI_DATA_EXISTS_CONTENT', $this->alias));
			$csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_DATA_EXISTS_CONTENT', $this->alias));
		}
		else {
			// Check if there is a category ID
			if (empty($this->catid) && $this->category_path) {
				// Find the category ID
				$this->catid = $this->helper->getCategoryId($this->category_path);
			}

			// Check if we have a title
			if (empty($this->_content->title)) $this->_content->title = $this->alias;

			// Bind the data
			$this->_content->save($this);
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
		$this->_content = $this->getTable('content');
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
		$this->_content->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}