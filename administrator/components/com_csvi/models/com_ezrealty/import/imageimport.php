<?php
/**
 * Image import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: mediaimport.php 2273 2013-01-03 16:33:30Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Main processor for importing images
 */
class CsviModelImageimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the medias table */
	private $_images = null;

	// Public variables
	/** @var integer contains the product ID of a product */
	public $id = null;
	/** @var integer vendor ID */
	public $virtuemart_vendor_id = null;
	public $fname = null;
	public $title = null;
	public $description = null;
	public $path = null;
	public $rets_source = null;
	public $ordering = null;

	// Private variables
	/** @var bool contains whether or not the product file should be deleted */
	protected $image_delete = 'N';

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
	 * @todo		Redo the validateInput
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.8
	 */
	public function getStart() {
		// Get the logger
		$jinput = JFactory::getApplication()->input;

		// Load the data
		$this->loadData();

		// Load the helper
		$this->helper = new Com_EzRealty();

		// Get the logger
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'image_delete':
						$this->$name = strtoupper($value);
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
	 * @since 		5.8
	 */
	public function getProcessRecord() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		// Find the media ID, we only use the image name
		$this->_images->fname = basename($this->fname);
		$this->_images->check();
		$this->id = $this->_images->id;

		// Do we need to delete a media file?
		if ($this->image_delete == 'Y') {
			if ($this->id) $this->_deleteImage();
			else $csvilog->AddStats('information', JText::_('COM_CSVI_NO_IMAGE_ID'));
		}
		else {
			// Find the property ID
			$based_on = $template->get('update_based_on', 'property', 'id');

			if ($based_on != 'id') {
				// Find the property ID
				$db = JFactory::getDbo();
				$propid = $this->propid;
				$query = $db->getQuery(true)->select($db->qn('id'))->from($db->qn('#__ezrealty'))->where($db->qn($based_on).' = '.$db->q($propid));
				$db->setQuery($query);
				$this->propid = $db->loadResult();
				if (!$this->propid) {
					$csvilog->AddStats('error', JText::sprintf('COM_CSVI_CANNOT_FIND_PROPERTY', $propid));
					return false;
				}
			}

			// Process the image
			$this->_processMedia();

			// Bind all the data
			$this->_images->bind($this);

			// Store the data
			if ($this->_images->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_IMAGEFILE'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_IMAGEFILE'));

				// Watermark image if needed
				$imagehelper = new ImageHelper;
				if ($template->get('full_watermark','image')) $imagehelper->addWatermark(JPATH_SITE.'/'.$this->_images->fname);
			}
			else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MEDIAFILE_NOT_ADDED', $this->_images->getError()));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_IMAGEFILE_QUERY'), true);
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the product files related tables
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		5.8
	 */
	private function _loadTables() {
		$this->_images = $this->getTable('images');
	}

	/**
	 * Cleaning the product files related tables
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
		$this->_images->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Delete a media and its references
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		5.8
	 */
	private function _deleteImage() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		// Delete the product
		if ($this->_images->delete($this->id)) {
			$csvilog->AddStats('deleted', JText::sprintf('COM_CSVI_MEDIA_DELETED', $this->id));
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MEDIA_NOT_DELETED', $this->id));
		}

		return true;
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
	 * @since 		5.8
	 */
	private function _processMedia() {
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);
		$csvilog = $jinput->get('csvilog', null, null);
		// Check if any image handling needs to be done
		if ($template->get('process_image', 'image', false)) {
			if (!is_null($this->fname)) {
				$max_width = $template->get('resize_max_width', 'image', 1024);
				$max_height = $template->get('resize_max_height', 'image', 768);

				// Image handling
				$imagehelper = new ImageHelper;

				$image = trim($this->fname);
				if (!empty($image)) {
					// Verify the original image
					if ($imagehelper->isRemote($image)) {
						$original = $image;
						$remote = true;
						$full_path =  $template->get('file_location_property_images', 'path');
					}
					else {
						$original = $template->get('file_location_property_images', 'path').$image;
						$remote = false;

						// Get subfolders
						$path_parts = pathinfo($original);
						$full_path = $path_parts['dirname'].'/';
					}

					// Generate image names
					$file_details = $imagehelper->ProcessImage($original, $full_path);
					// Process the file details
					if ($file_details['exists'] && $file_details['isimage']) {
						// Check if the image is an external image
						$title = (isset($this->title)) ? $this->title : $file_details['output_name'];

						$data = array();
						$data['propid'] = $this->propid;
						$data['fname'] = $file_details['output_name'];
						$data['title'] = $title;
						$data['description'] = $this->description;
						$data['ordering'] = $this->ordering;

						if (substr($file_details['name'], 0, 4) == 'http') {
							if (is_null($this->_images)) $csvilog->AddStats('incorrect', 'COM_CSVI_EZREALTY_NOSUPPORT_URL');
							else {
								// External images are supported now but needs to be stored with separate data
								if (substr($file_details['output_path'], -1) == '/') $data['path'] = substr($file_details['output_path'], 0, -1);
								else $data['path'] = $file_details['output_path'];
							}
						}
						else {
							$this->fname = $file_details['output_name'];

							// Create the thumbnail
							if ($template->get('thumb_create', 'image')) {
								$imagehelper->createThumbnail($file_details['output_path'].$this->fname, $template->get('file_location_property_images', 'path').'th/', $this->fname);
							}
						}
					}
				}
			}
		}
	}
}