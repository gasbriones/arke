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

class CsviModelItemimport extends CsviModelImportfile {

	// Private tables
	private $_item = null;
	private $_categorymodel = null;


	// Public variables
	public $helper = null;
	public $id = null;
	public $alias = null;
	public $catid = null;
	public $category_path = null;
	public $image = null;

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
		$this->helper = new Com_K2();

		// Find the content id
		$this->id = $this->helper->getItemId();

		// Load the current content data
		$this->_item->load($this->id);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'published':
						switch (strtoupper($value)) {
							case 'Y':
								$value = 1;
								break;
							case 'N':
								$value = 0;
								break;
						}
						$this->$name = $value;
						break;
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
			$csvilog->addDebug(JText::sprintf('COM_CSVI_DATA_EXISTS_ITEM', $this->alias));
			$csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_DATA_EXISTS_ITEM', $this->alias));
		}
		else {
			// Check if there is a category ID
			if (empty($this->catid) && $this->category_path) {
				// Find the category ID
				$this->catid = $this->helper->getCategoryIdByPath($this->category_path);
			}

			// Check if we have a title
			if (empty($this->_item->title)) $this->_item->title = $this->alias;

			// Bind the data
			if ($this->_item->save($this)) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_ITEM'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_ITEM'));

				// Store the debug message
				$csvilog->addDebug(JText::_('COM_CSVI_ITEM_QUERY'), true);

				// Add the image if needed
				if (!empty($this->image)) $this->_processMedia();
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_ITEM_NOT_ADDED', $this->_item->getError()));

				// Store the debug message
				$csvilog->addDebug(JText::_('COM_CSVI_ITEM_QUERY'), true);
				return false;
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
		$this->_item = $this->getTable('item');
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
		$this->_item->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Process media files
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
	private function _processMedia() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);
		// Check if any image handling needs to be done
		if ($template->get('process_image', 'image', false)) {

			// Load some helpers
			$this->config = new CsviCom_K2_Config();
			require_once (JPATH_ADMINISTRATOR.'/components/com_k2/lib/class.upload.php');

			// Set the maximum sizes
			$max_width = $template->get('resize_max_width', 'image', 1024);
			$max_height = $template->get('resize_max_height', 'image', 768);

			// Get the full path and name of the image
			$filename = md5('Image'.$this->_item->id);

			// Rename original image
			$image = JPATH_SITE.'/media/k2/items/src/'.$filename;
			JFile::move(JPATH_SITE.'/media/k2/items/src/'.trim($this->image), $image.'.jpg');


			// Check if the thumbnail is not too big
			$thumb_sizes = getimagesize($image.'.jpg');
			if ($thumb_sizes[0] < $max_width || $thumb_sizes[1] < $max_height) {
				$imagehelper = new Upload($image.'.jpg');

				// We need to create several thumbnails
				$sizes = array();
				$sizes['XS'] = $this->config->get('itemImageXS');
				$sizes['S'] = $this->config->get('itemImageS');
				$sizes['M'] = $this->config->get('itemImageM');
				$sizes['L'] = $this->config->get('itemImageL');
				$sizes['XL'] = $this->config->get('itemImageXL');
				$sizes['Generic'] = $this->config->get('itemImageGeneric');

				$savepath = JPATH_SITE.'/media/k2/items/cache';

				foreach ($sizes as $size => $width) {
					switch ($size) {
						default:
							$imagehelper->image_resize = true;
							$imagehelper->image_ratio_y = true;
							$imagehelper->image_convert = 'jpg';
							$imagehelper->jpeg_quality = $this->config->get('imagesQuality');
							$imagehelper->file_auto_rename = false;
							$imagehelper->file_overwrite = true;
							$imagehelper->file_new_name_body = $filename.'_'.$size;
							$imagehelper->image_x = $width;
							$imagehelper->Process($savepath);
							break;
					}
				}
			}
		}
	}
}