<?php
/**
 * Product import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: productimport.php 2104 2012-09-01 18:36:27Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for product details
 *
 * Main processor for handling product details.
 */
class CsviModelExpressProductimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_product table */
	private $_products = null;
	private $_products_lang = null;

	// Private variables
	private $_tablesexist = true;

	// Public variables
	/** @var integer product ID */
	public $virtuemart_product_id = null;
	/** @var integer vendor ID */
	public $virtuemart_vendor_id = null;
	/** @var string contains if the product should be deleted */
	public $product_delete = 'N';
	public $product_box = null;
	public $product_packaging = null;
	/** @var int number of products in stock */
	public $product_in_stock = null;
	/** @var int the shopper group id */
	public $shopper_group_id = null;
	/** @var string the parent SKU */
	public $product_parent_id = null;
	public $product_desc = null;
	public $product_params = null;
	public $published = null;
	public $min_order_level = null;
	public $max_order_level = null;

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

		// Set some initial values
		$this->date = JFactory::getDate();
		$this->user = JFactory::getUser();
    }

	/**
	 * Here starts the processing
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo 		test downloadable files
	 * @todo 		add data read in case of incorrect columns.
	 * @todo		remove message about incorrect column count as import now ignores those???
	 * @todo		Create a new convertdate function
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		3.0
	 */
	public function getStart() {
		// Get the logger
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Only continue if all tables exist
		if ($this->_tablesexist) {

			// Load the data
			$this->loadData();

			// Load the helper
			$this->helper = new Com_VirtueMart();

			$this->virtuemart_product_id = $this->helper->getProductId();

			// Load the current product data
			$this->_products->load($this->virtuemart_product_id);

			// Process data
			foreach ($this->csvi_data as $name => $fields) {
				foreach ($fields as $filefieldname => $details) {
					$value = $details['value'];
					// Check if the field needs extra treatment
					switch ($name) {
						case 'product_available_date':
							$this->_products->$name = $this->convertDate($value);
							break;
						case 'product_weight':
						case 'product_length':
						case 'product_width':
						case 'product_height':
							$this->_products->$name = $this->toPeriod($value);
							break;
						case 'published':
							switch ($value) {
								case 'n':
								case 'N':
								case '0':
									$value = 0;
									break;
								default:
									$value = 1;
									break;
							}
							$this->$name = $value;
							break;
						case 'product_name':
							$this->_products_lang->$name = $value;
							break;
						default:
							$this->$name = $value;
							break;
					}
				}
			}

			// Calculate product packaging
			if (!is_null($this->product_box) && !is_null($this->product_packaging)) $this->_productPackaging();

			// Set the record identifier
			$this->record_identity = (isset($this->product_sku)) ? $this->product_sku : $this->virtuemart_product_id;

			return true;
		}
		else {
			$template = $jinput->get('template', null, null);
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_LANG_TABLE_NOT_EXIST', $template->get('language', 'general')));
			return false;
		}
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
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		if ($this->virtuemart_product_id && !$template->get('overwrite_existing_data', 'general')) {
		   $csvilog->addDebug(JText::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->product_sku));
		   $csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_DATA_EXISTS_PRODUCT_SKU', $this->product_sku));
		}
		else {
			if (empty($this->product_sku) && empty($this->virtuemart_product_id)) {
				$csvilog->AddStats('incorrect', JText::_('COM_CSVI_DEBUG_NO_SKU'));
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_NO_SKU_OR_ID'));
				return false;
			}
			else {
				$csvilog->addDebug(JText::sprintf('COM_CSVI_DEBUG_PROCESS_SKU', $this->record_identity));
			}

			// User wants to delete the product
			if (isset($this->virtuemart_product_id) && $this->product_delete == "Y") {
				$this->_deleteProduct();
			}
			else if (!isset($this->virtuemart_product_id) && $this->product_delete == "Y") {
				$csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_NO_PRODUCT_ID_NO_DELETE', $this->record_identity));
			}
			else if (!isset($this->virtuemart_product_id) && $template->get('ignore_non_exist', 'general')) {
				// Do nothing for new products when user chooses to ignore new products
				$csvilog->AddStats('skipped', JText::sprintf('COM_CSVI_DATA_EXISTS_IGNORE_NEW', $this->record_identity));
			}
			// User wants to add or update the product
			else {
				// Process order levels
				if (!isset($this->product_params) && (isset($this->min_order_level) || isset($this->max_order_level))) {
					$this->product_params = 'min_order_level="';
					if (isset($this->min_order_level)) $this->product_params .= $this->min_order_level;
					else $this->product_params .= '0';
					$this->product_params .= '"|max_order_level="';
					if (isset($this->max_order_level)) $this->product_params .= $this->max_order_level;
					else $this->product_params .= '0';
					$this->product_params .= '"|';
				}

				// Process product info
				$this->_productQuery();
			}
			// Now that all is done, we need to clean the table objects
			$this->cleanTables();
		}
	}

	/**
	 * Load the product related tables
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
		$jinput = JFactory::getApplication()->input;
		$template = $jinput->get('template', null, null);

		// Load the main tables
		$this->_products = $this->getTable('products');

		// Check if the language tables exist
		$db = JFactory::getDbo();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix().'virtuemart_products_'.$template->get('language', 'general'), $tables)) {
			$this->_tablesexist = false;
		}
		else {
			$this->_tablesexist = true;
			// Load the language tables
			$this->_products_lang = $this->getTable('products_lang');
		}
	}

	/**
	 * Cleaning the product related tables
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
		// Clean the main tables
		$this->_products->reset();

		// Clean the language tables
		$this->_products_lang->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}

	/**
	 * Get the product packaging
	 *
	 * The number is calculated by hexnumbers
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
	private function _productPackaging() {
		$this->product_packaging = (($this->product_box<<16) | ($this->product_packaging & 0xFFFF));
	}

	/**
	 * Creates either an update or insert SQL query for a product.
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return 		bool true if the query executed successful|false if the query failed
	 * @since 		3.0
	 */
	private function _productQuery() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Check if we need to do a stock calculation
		if (!is_null($this->product_in_stock)) {
			// Split the modification
			$operation = substr($this->product_in_stock, 0, 1);
			$value = substr($this->product_in_stock, 1);

			// Get the database value
			$stock = $this->_products->product_in_stock;

			// Check what modification we need to do and apply it
			switch ($operation) {
				case '+':
					$stock += $value;
					break;
				case '-':
					$stock -= $value;
					break;
				case '/':
					$stock /= $value;
					break;
				case '*':
					$stock*= $value;
					break;
				default:
					// Assign the current price to prevent it being overwritten
					$stock = $this->product_in_stock;
					break;
			}
			$this->product_in_stock = $stock;
		}

		// Bind the initial data
		$this->_products->bind($this);

		// Set the modified date as we are modifying the product
		if (!isset($this->modified_on)) {
			$this->_products->modified_on = $this->date->toMySQL();
			$this->_products->modified_by = $this->user->id;
		}

		// Add a creating date if there is no product_id
		if (empty($this->virtuemart_product_id)) {
			$this->_products->created_on = $this->date->toMySQL();
			$this->_products->created_by = $this->user->id;
		}
		foreach ($this->_avfields as $id => $column) {
			// Only process the fields the user is uploading
			if (isset($this->$column)) {
				// Add a redirect for the product cdate
				if ($column == "product_cdate" && !empty($this->$column)) {
					$this->_products->created_on = $this->$column;
				}

				// Add a redirect for the product mdate
				if ($column == "product_mdate" && !empty($this->$column)) {
					$this->_products->modified_on = $this->$column;
				}
			}
		}

		// We have a succesful save, get the product_id
		if ($this->_products->store()) {
			if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCT_SKU'));
			else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCT_SKU'));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_PRODUCT_QUERY'), true);
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_NOT_ADDED', $this->_products->getError()));

			// Store the debug message
			$csvilog->addDebug(JText::_('COM_CSVI_PRODUCT_QUERY'), true);
			return false;
		}

		// Set the product ID
		$this->virtuemart_product_id = $this->_products->virtuemart_product_id;

		// Store the language fields
		$this->_products_lang->bind($this);
		$this->_products_lang->virtuemart_product_id = $this->virtuemart_product_id;

		if ($this->_products_lang->check()) {
			if ($this->_products_lang->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCT_LANG'));
				else if ($this->queryResult() == 'INSERT') $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCT_LANG'));
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_LANG_NOT_ADDED', $this->_products_lang->getError()));
				return false;
			}
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_LANG_NOT_ADDED', $this->_products_lang->getError()));
			return false;
		}

		// Store the debug message
		$csvilog->addDebug('COM_CSVI_PRODUCT_LANG_QUERY', true);

		// All good
		return true;
	}

	/**
	 * Delete a product and its references
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
	private function _deleteProduct() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		// Delete the product
		if ($this->_products->delete($this->virtuemart_product_id)) {
			$db = JFactory::getDbo();
			// Delete product translations
			jimport('joomla.language.helper');
			$languages = array_keys(JLanguageHelper::getLanguages('lang_code'));
			foreach ($languages as $language){
				$query = $db->getQuery(true);
				$query->delete('#__virtuemart_products_'.strtolower(str_replace('-', '_', $language)));
				$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
				$db->setQuery($query);
				$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_PRODUCT_LANG_XREF'), true);
				$db->query();
			}

			// Delete category reference
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_categories');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_CATEGORY_XREF'), true);
			$db->query();

			// Delete manufacturer reference
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_manufacturers');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_MANUFACTURER_XREF'), true);
			$db->query();

			// Reset child parent reference
			$query = $db->getQuery(true);
			$query->update('#__virtuemart_products');
			$query->set('product_parent_id = 0');
			$query->where('product_parent_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_PRODUCT_PARENT'), true);
			$db->query();

			// Delete prices
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_prices');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_PRICES_XREF'), true);
			$db->query();

			// Delete shopper groups
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_shoppergroups');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_SHOPPERGROUP_XREF'), true);
			$db->query();

			// Delete prices
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_prices');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_PRICES_XREF'), true);
			$db->query();

			// Delete custom fields
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_customfields');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_CUSTOMFIELDS_XREF'), true);
			$db->query();

			// Delete media
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_medias');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_MEDIA_XREF'), true);
			$db->query();

			// Delete ratings
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_ratings');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_RATINGS_XREF'), true);
			$db->query();

			// Delete rating reviews
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_rating_reviews');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_RATING_REVIEWS_XREF'), true);
			$db->query();

			// Delete rating votes
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_rating_votes');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug(JText::_('COM_CSVI_DEBUG_DELETE_RATING_VOTES_XREF'), true);
			$db->query();

			$csvilog->AddStats('deleted', JText::sprintf('COM_CSVI_PRODUCT_DELETED', $this->record_identity));
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_NOT_DELETED', $this->record_identity));
		}

		return true;
	}
}