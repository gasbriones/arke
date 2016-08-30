<?php
/**
 * Category import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponimport.php 1924 2012-03-02 11:32:38Z RolandD $
 */

defined('_JEXEC') or die;

class CsviModelCategoryimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the properties table */
	private $_category = null;

	// Public variables
	public $id = null;
	public $alias = null;
	public $catid = null;
	public $path = null;
	public $extension = null;
	public $access = null;
	public $language = null;
	public $published = null;
	public $title = null;
	public $params = null;
	public $metadata = null;
	public $note = null;
	public $description = null;

	// Private variables
	private $_catsep = null;

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
		$this->helper = new Com_Categories();

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'category_path':
						$this->path = $value;
						break;
					default:
						$this->$name = $value;
						break;
				}
			}
		}

		// There must be an alias and catid or category_path
		if (empty($this->extension) && (empty($this->id) && empty($this->path))) return false;

		if (empty($this->id)) $this->id = $this->helper->getCategoryId($this->path, $this->extension);

		// Load the current content data
		$this->_category->load($this->id);

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
			if (!$this->id) {
				// Load the category separator
				if (is_null($this->_catsep)) {
					$this->_catsep = $template->get('category_separator', 'general', '/');
				}

				$paths = explode($this->_catsep, $this->path);
				$path = '';
				$parent_id = false;
				$lastkey = array_pop(array_keys($paths));
				foreach ($paths as $key => $category) {
					if ($key > 0) $path .= $this->_catsep.$category;
					else $path = $category;

					// Check if the path exists
					$path_id = $this->helper->getCategoryId($path, $this->extension);

					// Category doesn't exist
					if (!$path_id) {
						// Clean the table
						$this->_category->reset();

						// Bind the data
						$data = array();
						$data['published'] = (is_null($this->published)) ? 0 : $this->published;
						$data['access'] = (is_null($this->access)) ? 1 : $this->access;
						$data['params'] = '{"category_layout":"","image":""}';
						$data['metadata'] = '{"author":"","robots":""}';
						$data['language'] = (is_null($this->language)) ? '*' : $this->language;
						if ($parent_id) $data['parent_id'] = $parent_id;
						else $data['parent_id'] = 1;
						$data['path'] = $path;
						if ($lastkey == $key) {
							$data['title'] = (is_null($this->title)) ? $category : $this->title;
							$data['note'] = $this->note;
							$data['description'] = $this->description;
						}
						else $data['title'] = $category;
						$data['extension'] = $this->extension;

						// Set the category location
						$this->_category->setLocation($data['parent_id'], 'last-child');

						// Bind the data
						$this->_category->bind($data);

						// Check the data
						if (!$this->_category->check()) {
							$errors = $this->_category->getErrors();
							foreach ($errors as $error) {
								$csvilog->addDebug($error);
								$csvilog->AddStats('incorrect', $error);
							}
						}
						else {
							// Store the data
							if ($this->_category->store()) {
								$this->_category->rebuildPath($this->_category->id);
								$this->_category->rebuild($this->_category->id, $this->_category->lft, $this->_category->level, $this->_category->path);
								$parent_id = $this->_category->id;
								$csvilog->AddStats('added', JText::_('COM_CSVI_ADD_CATEGORY'));
							}
							else {
								$csvilog->AddStats('incorrect', JText::_('COM_CSVI_CATEGORY_NOT_ADDED'));
								$errors = $this->_category->getErrors();
								foreach ($errors as $error) {
									$csvilog->addDebug($error);
									$csvilog->AddStats('incorrect', $error);
								}
							}
						}
					}
					else $parent_id = $path_id;
				}
			}
			// Category already exist, just update it
			else {
				// Remove the alias, so it can be created again
				$this->_category->alias = null;

				// Bind the data
				$data = array();
				$data['published'] = $this->published;
				$data['access'] = $this->access;
				$data['params'] = $this->params;
				$data['metadata'] = $this->metadata;
				$data['language'] = $this->language;
				$data['path'] = $this->path;
				$data['title'] = $this->title;
				$data['extension'] = $this->extension;
				$data['note'] = $this->note;
				$data['description'] = $this->description;
				$this->_category->bind($data);

				// Check the data
				if (!$this->_category->check()) {
					$errors = $this->_category->getErrors();
					foreach ($errors as $error) {
						$csvilog->addDebug($error);
						$csvilog->AddStats('incorrect', $error);
					}
				}
				else {
					// Save the data
					if ($this->_category->store()) {
						$csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_CATEGORY'));
					}
					else {
						$csvilog->AddStats('incorrect', JText::_('COM_CSVI_CATEGORY_NOT_UPDATED'));
						$errors = $this->_category->getErrors();
						foreach ($errors as $error) {
							$csvilog->AddStats('incorrect', $error);
						}
					}
				}
			}
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
		$this->_category = $this->getTable('category');
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
		$this->_category->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}