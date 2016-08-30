<?php
/**
 * Product import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: productimport.php 2398 2013-03-27 20:24:21Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for product details
 *
 * Main processor for handling product details.
 */
class CsviModelProductimport extends CsviModelImportfile {

	// Private tables
	/** @var object contains the vm_product table */
	private $_products = null;
	private $_products_lang = null;
	/** @var object contains the vm_media table */
	private $_medias = null;
	/** @var object contains the vm_price table */
	private $_product_prices = null;
	/** @var object contains the vm_product_discount table */
	private $_calcs = null;
	/** @var object contains the vm_product_relations table */
	private $_product_customfields = null;
	/** @var object contains the vm_manufacturer table */
	private $_manufacturers = null;
	private $_manufacturers_lang = null;
	private $_product_shoppergroups = null;

	// Private variables
	/** @var object category model */
	private $_categorymodel = null;
	private $_tablesexist = true;
	private $_customtitles = array();
	private $_customfields = array();

	// Public variables
	/** @var integer product ID */
	public $virtuemart_product_id = null;
	/** @var integer vendor ID */
	public $virtuemart_vendor_id = null;
	/** @var bool contains the setting if the discount is a percentage or absolute value */
	public $calc_value_mathop = 0;
	/** @var integer contains the discount amount */
	public $calc_value = null;
	/** @var string contains the discount value */
	public $product_discount = null;
	/** @var integer contains the discount start date */
	public $product_discount_date_start = null;
	/** @var integer contains the discount end date */
	public $product_discount_date_end = null;
	/** @var int contains the name of the full image */
	public $file_url = null;
	/** @var int contains the name of the thumbnail image */
	public $file_url_thumb = null;
	public $file_title = null;
	public $file_description = null;
	public $file_meta = null;
	public $file_ordering = null;
	/** @var int contains the number if items in a box */
	public $product_box = null;
	public $product_packaging = null;
	/** @var string contains the currency name */
	public $product_currency = null;
	/** @var string contains if the product should be deleted */
	public $product_delete = 'N';
	/** @var int number of products in stock */
	public $product_in_stock = null;
	/** @var string list of SKUs or IDs of related products */
	public $related_products = null;
	public $related_categories = null;
	/** @var bool set if the product is a child product */
	public $child_product = false;
	/** @var float holds the tax amount */
	public $product_tax = null;
	/** @var float holds the price including tax */
	public $product_price = null;
	public $price_with_tax = null;
	public $product_override_price = null;
	public $override = null;
	/** @var int the shopper group id */
	public $virtuemart_shoppergroup_id = 0;
	public $shopper_group_name = null;
	public $shopper_group_name_price = null;
	public $shopper_group_name_new = null;
	/** @var string the parent SKU */
	public $product_parent_sku = null;
	public $product_parent_id = null;
	/** @var int the category id */
	public $category_id = null;
	public $category_ids = null;
	public $custom_value = null;
	public $custom_price = null;
	public $custom_param = null;
	public $custom_title = null;
	public $custom_delete = null;
	public $custom_ordering = null;
	public $custom_multiple = null;
	public $product_desc = null;
	public $category_path = null;
	public $features = null;
	public $min_order_level = null;
	public $max_order_level = null;
	public $step_order_level = null;
	public $product_params = null;
	public $product_discount_id = null;
	public $published = null;
	public $product_tax_id = null;
	public $calc_kind = null;
	public $product_ordered = null;
	public $product_ordering = null;
	public $price_quantity_start = 0;
	public $price_quantity_end = 0;

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
			$this->vmconfig = new CsviCom_VirtueMart_Config();

			$this->virtuemart_product_id = $this->helper->getProductId();
			$this->virtuemart_vendor_id = $this->helper->getVendorId();

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
						case 'product_discount_date_start':
							$this->$name = $this->convertDate($value);
							break;
						case 'product_discount_date_end':
							$this->$name = $this->convertDate($value);
							break;
						case 'product_price':
						case 'product_override_price':
							// Cannot clean price otherwise we lose calculations
							$this->$name = $this->toPeriod($value);
							break;
						case 'product_weight':
						case 'product_length':
						case 'product_width':
						case 'product_height':
							$this->_products->$name = $this->toPeriod($value);
							break;
						case 'related_products':
							if (substr($value, -1, 1) == "|") $this->related_products = substr($value, 0, -1);
							else $this->related_products = $value;
							break;
						case 'category_id':
						case 'category_path':
							if (strlen(trim($value)) > 0) {
								if (stripos($value, '|') > 0) $category_ids[$name] = explode("|", $value);
								else $category_ids[$name][] = $value;
								$this->category_ids = $category_ids;
							}
							$this->$name = $value;
							break;
						case 'manufacturer_name':
							$this->_manufacturers_lang->mf_name = $value;
							break;
						case 'manufacturer_id':
							$this->_manufacturers_lang->virtuemart_manufacturer_id = $value;
							break;
						case 'price_with_tax':
							$this->$name = $this->cleanPrice($value);
							break;
						case 'published':
							switch ($value) {
								case 'n':
								case 'no':
								case 'N':
								case 'NO':
								case '0':
									$value = 0;
									break;
								default:
									$value = 1;
									break;
							}
							$this->$name = $value;
							break;
						case 'override':
						case 'product_special':
							switch ($value) {
								case 'y':
								case 'yes':
								case 'Y':
								case 'YES':
								case '1':
									$value = 1;
									break;
								case '-1':
									$value = '-1';
									break;
								default:
									$value = 0;
									break;
							}
							$this->$name = $value;
							break;
						case 'product_currency':
							$this->$name = $this->helper->getCurrencyId(strtoupper($value), $this->virtuemart_vendor_id);
							break;
						case 'calc_value':
						case 'calc_value_mathop':
							$this->_calcs->$name = $value;
							break;
						case 'product_name':
							$this->_products_lang->$name = $value;
							break;
						case 'product_tax':
							$this->$name = $this->cleanPrice($value);
							break;
						default:
							$this->$name = $value;
							break;
					}
				}
			}

			// Calculate product packaging
			if (version_compare($this->vmconfig->get('release'), '2.0.10', 'lt')) {
				if (!is_null($this->product_box) && !is_null($this->product_packaging)) $this->_productPackaging();
			}

			// We need the currency
			if (is_null($this->product_currency) && (isset($this->product_price) || isset($this->price_with_tax))) {
				$this->_product_prices->product_currency = $this->productCurrency($this->virtuemart_vendor_id);
			}

			// Check for child product and get parent SKU if it is
			if (!is_null($this->product_parent_sku)) {
				$this->_productParentSku();
			}

			// Set the record identifier
			$this->record_identity = (isset($this->product_sku)) ? $this->product_sku : $this->virtuemart_product_id;

			return true;
		}
		else {
			$template = $jinput->get('template', null, null);
			$db = JFactory::getDbo();
			if ($template->get('language', 'general') == $template->get('target_language', 'general')) $lang = $template->get('language', 'general');
			$tblname = $db->getPrefix().'virtuemart_categories_'.$lang;
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_LANG_TABLE_NOT_EXIST', $lang));
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
				$csvilog->addDebug('COM_CSVI_DEBUG_NO_SKU_OR_ID');
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
				if (!isset($this->product_params)
						&& (!is_null($this->min_order_level)
								|| !is_null($this->max_order_level)
								|| !is_null($this->product_box)
								|| !is_null($this->step_order_level))) {
					$this->product_params = 'min_order_level="';
					if (isset($this->min_order_level)) $this->product_params .= $this->min_order_level;
					else $this->product_params .= '0';
					$this->product_params .= '"|max_order_level="';
					if (isset($this->max_order_level)) $this->product_params .= $this->max_order_level;
					else $this->product_params .= '0';
					$this->product_params .= '"|step_order_level="';
					if (isset($this->step_order_level)) $this->product_params .= $this->step_order_level;
					else $this->product_params .= '';
					if (version_compare($this->vmconfig->get('release'), '2.0.10', 'ge')) {
						$this->product_params .= '"|product_box="';
						if (isset($this->product_box)) $this->product_params .= $this->product_box;
						else $this->product_params .= '0';
					}
					$this->product_params .= '"|';
				}

				// Process discount
				if (isset($this->product_discount)) $this->_processDiscount();

				// Process tax
				$csvilog->addDebug('Product tax'.$this->product_tax);
				if (!empty($this->product_tax)) $this->_processTax();

				// Process manufacturer
				$this->_manufacturerImport();

				// Process the ICEcat product features
				$csvifields = $jinput->get('csvifields', null, null);
				$this->features = $csvifields->get('features');
				if ($template->get('use_icecat', 'product', false, 'bool') && !empty($this->features)) {
					$this->_icecatFeatures();
				}

				// Process product info
				if ($this->_productQuery()) {
					// Handle the shopper group(s)
					$this->_processShopperGroup();

					// Handle the images
					$this->_processMedia();

					// Check if the price is to be updated
					if (!empty($this->product_price) || !empty($this->price_with_tax) || !empty($this->product_override_price)) $this->_priceQuery();

					// Add a product <--> manufacturer cross reference
					if ((isset($this->_manufacturers_lang->virtuemart_manufacturer_id) && $this->_manufacturers_lang->virtuemart_manufacturer_id)) {
						$this->_manufacturerCrossReference();
					}

					// Process custom fields
					if (isset($this->custom_title) && !empty($this->custom_title)) $this->_processCustomFields();

					// Check if the field is a custom field used as an available field
					$this->_processCustomAvailableFields();

					// Force an update of stockable variant parent products
					if ($template->get('update_stockable_parent', 'product', false)) $this->_processParentValues(false);

					// Process related products/categories
					// Related products are first input in the database as SKU
					// At the end of the import, this is converted to product ID
					if ($this->related_products) $this->_processRelatedProducts();
					if ($this->related_categories) $this->_processRelatedCategories();


					// Process category path
					if (isset($this->category_path) || isset($this->category_id)) {
						if ($this->category_ids || $this->category_id) {
							if (is_null($this->_categorymodel)) $this->_categorymodel = new CsviModelCategory();
							$this->_categorymodel->getStart();
							// Check the categories
							// Do we have IDs
							if (array_key_exists('category_id', $this->category_ids)) {
								$this->_categorymodel->CheckCategoryPath($this->virtuemart_product_id, false, $this->category_ids['category_id'], $this->product_ordering);
							}
							else if (array_key_exists('category_path', $this->category_ids)) {
								$this->_categorymodel->CheckCategoryPath($this->virtuemart_product_id, $this->category_ids['category_path'], false, $this->product_ordering);
							}
						}
					}
				}
			}
		}

		// Now that all is done, we need to clean the table objects
		$this->cleanTables();
	}

	/**
   	 * Execute any processes to finalize the import
   	 *
   	 * @copyright
   	 * @author 		RolandD
   	 * @todo
   	 * @see
   	 * @access 		public
   	 * @param 		array	$fields	list of fields used for import
   	 * @return
   	 * @since 		3.0
   	 */
	public function getPostProcessing($fields=array()) {
		// Related products
		if (in_array('related_products', $fields)) $this->_postProcessRelatedProducts();
		if (in_array('related_categories', $fields)) $this->_postProcessRelatedCategories();
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
		$this->_medias = $this->getTable('medias');
		$this->_product_medias = $this->getTable('product_medias');
		$this->_product_prices = $this->getTable('product_prices');
		$this->_calcs = $this->getTable('calcs');
		$this->_product_customfields = $this->getTable('product_customfields');
		$this->_manufacturers = $this->getTable('manufacturers');
		$this->_product_manufacturers = $this->getTable('product_manufacturers');
		$this->_product_shoppergroups = $this->getTable('product_shoppergroups');

		// Check if the language tables exist
		$db = JFactory::getDbo();
		$tables = $db->getTableList();
		if (!in_array($db->getPrefix().'virtuemart_products_'.$template->get('language', 'general'), $tables)) {
			$this->_tablesexist = false;
		}
		else if (!in_array($db->getPrefix().'virtuemart_manufacturers_'.$template->get('language', 'general'), $tables)) {
			$this->_tablesexist = false;
		}
		else {
			$this->_tablesexist = true;
			// Load the language tables
			$this->_products_lang = $this->getTable('products_lang');
			$this->_manufacturers_lang = $this->getTable('manufacturers_lang');
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
		$query->select("virtuemart_custom_id AS id, field_type, TRIM(custom_title) AS title");
		$query->from($db->qn('#__virtuemart_customs'));
		$query->where($db->qn('field_type').' IN ('.$db->q('S').','.$db->q('I').','.$db->q('B').','.$db->q('D').','.$db->q('T').','.$db->q('M').')');
		$db->setQuery($query);
		$this->_customfields = $db->loadObjectlist();
		$csvilog->addDebug('Load custom fields', true);
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
		$this->_medias->reset();
		$this->_product_medias->reset();
		$this->_product_prices->reset();
		$this->_calcs->reset();
		$this->_product_customfields->reset();
		$this->_manufacturers->reset();
		$this->_product_manufacturers->reset();
		$this->_product_shoppergroups->reset();

		// Clean the language tables
		$this->_products_lang->reset();
		$this->_manufacturers_lang->reset();

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
	 * Get the product parent sku if it is a child product
	 *
	 * The parent product MUST be imported before the child product
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
	private function _productParentSku() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$csvilog->addDebug('COM_CSVI_DEBUG_PRODUCT_PARENT_SKU');
		if (isset($this->product_sku)) {
			// Check if we are dealing with a child product
			if ($this->product_parent_sku !== $this->product_sku) {
				$this->child_product = true;
				// Get the parent id first
				$query = $db->getQuery(true);
				$query->select('virtuemart_product_id');
				$query->from('#__virtuemart_products');
				$query->where('product_sku = '.$db->q($this->product_parent_sku));
				$db->setQuery($query);
				$this->product_parent_id = $db->loadResult();
				$csvilog->addDebug('COM_CSVI_DEBUG_PRODUCT_PARENT_SKU', true);
			}
			else {
				$this->product_parent_id = 0;
				$this->child_product = false;
			}
		}
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
		$template = $jinput->get('template', null, null);

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
			$this->_products->modified_on = $this->date->toSql();
			$this->_products->modified_by = $this->user->id;
		}

		// Add a creating date if there is no product_id
		if (empty($this->virtuemart_product_id)) {
			$this->_products->created_on = $this->date->toSql();
			$this->_products->created_by = $this->user->id;

			// Process default values
			$defaults = array('product_weight', 'product_weight_uom', 'product_length', 'product_width', 'product_height',
						'product_lwh_uom', 'product_url', 'product_in_stock', 'product_ordered', 'low_stock_notification',
						'product_availability', 'product_special', 'product_sales', 'product_unit', 'product_packaging',
						'product_params', 'hits', 'intnotes', 'metarobot', 'metaauthor', 'layout', 'published');
			foreach ($defaults as $key => $field) {
				switch ($field) {
					case 'product_weight':
					case 'product_length':
					case 'product_width':
					case 'product_height':
					case 'product_in_stock':
					case 'product_ordered':
					case 'low_stock_notification':
					case 'product_special':
					case 'product_sales':
					case 'product_packaging':
					case 'hits':
					case 'layout':
					case 'published':
						if (!isset($this->$field)) $this->_products->$field = 0;
						break;
					case 'product_weight_uom':
					case 'product_unit':
						if (!isset($this->$field)) $this->_products->$field = 'KG';
						break;
					case 'product_lwh_uom':
						if (!isset($this->$field)) $this->_products->$field = 'M';
						break;
					case 'product_url':
					case 'product_availability':
					case 'intnotes':
					case 'metarobot':
					case 'metaauthor':
						if (!isset($this->$field)) $this->_products->$field = '';
						break;
					case 'product_params':
						if (!isset($this->$field)) $this->_products->$field = 'min_order_level=""|max_order_level=""|step_order_level=""|product_box=""|';
						break;
				}
			}
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
			$csvilog->addDebug('COM_CSVI_PRODUCT_QUERY', true);

			// If this is a child product, check if we need to update the custom field
			if ($this->child_product) $this->_processParentValues();
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_NOT_ADDED', $this->_products->getError()));

			// Store the debug message
			$csvilog->addDebug('COM_CSVI_PRODUCT_QUERY', true);
			return false;
		}

		// Set the product ID
		$this->virtuemart_product_id = $this->_products->virtuemart_product_id;

		// Store the language fields
		$this->_products_lang->bind($this);

		if ($this->_products_lang->check()) {
			// Recreate the slug
			if ($template->get('recreate_alias', 'product', false)) $this->_products_lang->createSlug();

			if ($this->_products_lang->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCT_LANG'));
				else if ($this->queryResult() == 'INSERT') $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCT_LANG'));
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_LANG_NOT_ADDED', $this->_products_lang->getError()));
				$csvilog->addDebug('COM_CSVI_PRODUCT_LANG_QUERY', true);
				return false;
			}
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_LANG_NOT_ADDED', $this->_products_lang->getError()));
			$csvilog->addDebug('COM_CSVI_PRODUCT_LANG_QUERY', true);
			return false;
		}

		// Store the debug message
		$csvilog->addDebug('COM_CSVI_PRODUCT_LANG_QUERY', true);

		// All good
		return true;
	}

    /**
     * Process Related Products
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
	private function _processRelatedProducts() {
		$db = JFactory::getDbo();
		$relatedproducts = explode("|", $this->related_products);

		$query = $db->getQuery(true);
		$query = "INSERT IGNORE INTO `#__csvi_related_products` VALUES ";
		$entries = array();
		foreach ($relatedproducts AS $key => $relatedproduct) {
			$entries[] = "(".$db->Quote($this->product_sku).", ".$db->q($relatedproduct).")";
		}
		$query .= implode(',', $entries);
		$db->setQuery($query);
		$db->query();

		// Remove any existing product relations
		$this->_product_customfields->deleteRelated($this->virtuemart_product_id, $this->virtuemart_vendor_id, $this->helper->getRelatedId('R'));
   }

	/**
	 * Post Process Related Products
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _postProcessRelatedProducts() {
   		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$relations = array();
		// Get the related products
		$query = $db->getQuery(true);
		$query->select('p1.virtuemart_product_id AS virtuemart_product_id, p2.virtuemart_product_id AS custom_value');
		$query->from('#__csvi_related_products r');
		$query->leftJoin('#__virtuemart_products p1 ON r.product_sku = p1.product_sku');
		$query->leftJoin('#__virtuemart_products p2 ON r.related_sku = p2.product_sku');
		$db->setQuery($query);
		$csvilog->addDebug('COM_CSVI_PROCESS_RELATED_PRODUCTS', true);
		$relations = $db->loadObjectList();
		if (!empty($relations)) {
			// Store the new relations
			foreach ($relations as $key => $related) {
				// Build the object to store
				$related->virtuemart_custom_id = $this->helper->getRelatedId('R');
				$related->published = 0;
				$related->created_on = $this->date->toMySQL();
				$related->created_by = $this->user->id;
				$related->modified_on = $this->date->toMySQL();
				$related->modified_by = $this->user->id;
				$related->custom_param = '';

				// Bind the data
				$this->_product_customfields->bind($related);

				// Store the data
				if ($this->_product_customfields->store()) {
					$csvilog->addDebug('COM_CSVI_PROCESS_RELATED_PRODUCTS', true);
				}
				else {
					$csvilog->addDebug('COM_CSVI_DEBUG_RELATED_PRODUCTS', true);
				}

				// Clean the table object for next insert
				$this->_product_customfields->reset();
			}

			// Empty the relations table
			$db->setQuery("TRUNCATE ".$db->qn('#__csvi_related_products'));
			$db->query();
		}
		else {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_RELATED_PRODUCTS_FOUND'), true);
		}
	}

	/**
	 * Process Related Products
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
	private function _processRelatedCategories() {
		$db = JFactory::getDbo();
		if (is_null($this->_categorymodel)) $this->_categorymodel = new CsviModelCategory();
		$this->_categorymodel->getStart();
		$relatedcategories = explode("|", $this->related_categories);

		$query = $db->getQuery(true)->insert($db->qn('#__csvi_related_categories'));
		foreach ($relatedcategories AS $key => $relatedcategory) {
			$query->values($db->q($this->product_sku).', '.$db->q($relatedcategory));
		}
		$db->setQuery($query);
		$db->query();

		// Remove any existing product relations
		$this->_product_customfields->deleteRelated($this->virtuemart_product_id, $this->virtuemart_vendor_id, $this->helper->getRelatedId('Z'));
	}

	/**
	 * Post Process Related Products
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _postProcessRelatedCategories() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);
		$relations = array();
		// Get the related categories
		$query = $db->getQuery(true);
		$query->select('p.virtuemart_product_id AS virtuemart_product_id, r.related_cat');
		$query->from('#__csvi_related_categories r');
		$query->leftJoin('#__virtuemart_products p ON r.product_sku = p.product_sku');
		$db->setQuery($query);
		$csvilog->addDebug('COM_CSVI_PROCESS_RELATED_CATEGORIES', true);
		$relations = $db->loadObjectList();
		if (!empty($relations)) {
			// Store the new relations
			foreach ($relations as $key => $related) {
				// Find the category ID
				$ids = $this->_categorymodel->getCategoryIdFromPath($related->related_cat);
				if ($ids['category_id']) $related->custom_value = $ids['category_id'];

				// Build the object to store
				$related->virtuemart_custom_id = $this->helper->getRelatedId('Z');
				$related->published = 0;
				$related->created_on = $this->date->toSql();
				$related->created_by = $this->user->id;
				$related->modified_on = $this->date->toSql();
				$related->modified_by = $this->user->id;
				$related->custom_param = '';

				// Bind the data
				$this->_product_customfields->bind($related);

				// Store the data
				if ($this->_product_customfields->store()) {
					$csvilog->addDebug('COM_CSVI_PROCESS_RELATED_CATEGORIES', true);
				}
				else {
					$csvilog->addDebug('COM_CSVI_DEBUG_RELATED_CATEGORIES', true);
				}

				// Clean the table object for next insert
				$this->_product_customfields->reset();
			}

			// Empty the relations table
			$db->setQuery("TRUNCATE ".$db->qn('#__csvi_related_categories'));
			$db->query();
		}
		else {
			$csvilog->AddStats('incorrect', JText::_('COM_CSVI_NO_RELATED_CATEGORIES_FOUND'), true);
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
		$generate_image = $template->get('auto_generate_image_name', 'image', false);
		// Check if any image handling needs to be done
		if (!is_null($this->file_url) || $generate_image) {
			// Check if we have any images
			if (is_null($this->file_url) && $generate_image) {
				$this->_createImageName();
			}

			// Create an array of images to process
			$images = explode('|', $this->file_url);
			$thumbs = explode('|', $this->file_url_thumb);
			$titles = explode('|', $this->file_title);
			$descriptions = explode('|', $this->file_description);
			$metas = explode('|', $this->file_meta);
			$order = explode('|', $this->file_ordering);
			$ordering = 1;
			$max_width = $template->get('resize_max_width', 'image', 1024);
			$max_height = $template->get('resize_max_height', 'image', 768);

			// Image handling
			$imagehelper = new ImageHelper;

			// Delete existing image links
			if ($template->get('delete_product_images', 'image', false)) {
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->delete($db->qn('#__virtuemart_product_medias'))
					->where($db->qn('virtuemart_product_id').'='.$this->virtuemart_product_id);
				$db->setQuery($query);
				$db->query();
				$csvilog->addDebug('Delete images', true);
			}

			foreach ($images as $key => $image) {
				$image = trim($image);
				// Create image name if needed
				if (count($images) == 1) $img_counter = 0;
				else $img_counter = $key + 1;
				if ($generate_image) {
					$name = null;
					$name = $this->_createImageName($img_counter);
					if (!empty($name)) $image = $name;
				}

				if (!empty($image)) {
					// Get the image path
					$imgpath = $template->get('file_location_product_files', 'path');

					// Verify the original image
					if ($imagehelper->isRemote($image)) {
						$original = $image;
						$remote = true;
						$full_path = $imgpath;
					}
					else {
						// Check if the image contains the image path
						$dirname = dirname($image);
						if (strpos($imgpath, $dirname) !== false) {
							$image = basename($image);
						}
						$original = $imgpath.$image;
						$remote = false;

						// Get subfolders
						$path_parts = pathinfo($original);
						$full_path = $path_parts['dirname'].'/';
					}

					// Generate image names
					if ($template->get('process_image', 'image', false)) {
						if ($generate_image) {
							$file_details = $imagehelper->ProcessImage($original, $full_path, $this->product_full_image_output);
						}
						else {
							$file_details = $imagehelper->ProcessImage($original, $full_path);
						}
					}
					else {
						$file_details['exists'] = true;
						$file_details['isimage'] = $imagehelper->isImage(JPATH_SITE.'/'.$image);
						$file_details['name'] = $image;
						$file_details['output_name'] = basename($image);
						if (file_exists(JPATH_SITE.'/'.$image)) $file_details['mime_type'] = $imagehelper->findMimeType($image);
						else $file_details['mime_type'] = '';
						$file_details['output_path'] = $full_path;
					}

					// Process the file details
					if ($file_details['exists']) {
						// Check if the image is an external image
						if (substr($file_details['name'], 0, 4) == 'http') {
							$csvilog->AddStats('incorrect', 'COM_CSVI_VM_NOSUPPORT_URL');
						}
						else {
							$title = (isset($titles[$key])) ? $titles[$key] : $file_details['output_name'];
							$description = (isset($descriptions[$key])) ? $descriptions[$key] : '';
							$meta = (isset($metas[$key])) ? $metas[$key] : '';
							$media = array();
							$media['virtuemart_vendor_id'] = $this->virtuemart_vendor_id;
							if ($template->get('autofill', 'image')) {
								$media['file_title'] = $file_details['output_name'];
								$media['file_description'] = $file_details['output_name'];
								$media['file_meta'] = $file_details['output_name'];
							}
							else {
								$media['file_title'] = $title;
								$media['file_description'] = $description;
								$media['file_meta'] = $meta;
							}
							$media['file_mimetype'] = $file_details['mime_type'];
							$media['file_type'] = 'product';
							$media['file_is_product_image'] = 1;
							$media['file_is_downloadable'] = ($file_details['isimage']) ? 0 : 1;
							$media['file_is_forSale'] = 0;
							$media['file_url'] = (empty($file_details['output_path'])) ? $file_details['output_name'] : $file_details['output_path'].$file_details['output_name'];
							$media['published'] = $this->published;

							// Create the thumbnail
							if ($file_details['isimage']) {
								$thumb = (isset($thumbs[$key])) ? $thumbs[$key] : null;
								if ($template->get('thumb_create', 'image')) {
									$thumb_sizes = getimagesize(JPATH_SITE.'/'.$media['file_url']);
									if (empty($thumb) || $generate_image) {
										// Get the subfolder structure
										$thumb_path = str_ireplace($imgpath, '', $full_path);
										if (empty($this->file_url_thumb)) $thumb = 'resized/'.$thumb_path.basename($media['file_url']);
									}
									else {
										// Check if we are not overwriting any large images
										$thumb_path_parts = pathinfo($thumb);
										if ($thumb_path_parts['dirname'] == '.') {
											$csvilog->AddStats('incorrect', 'COM_CSVI_THUMB_OVERWRITE_FULL');
											$thumb = false;
										}
									}

									if ($thumb && ($thumb_sizes[0] < $max_width || $thumb_sizes[1] < $max_height)) {
										$media['file_url_thumb'] = $imagehelper->createThumbnail($media['file_url'], $imgpath, $thumb);
									}
									else $media['file_url_thumb'] = '';
								}
								else {
									$media['file_url_thumb'] = (empty($thumb)) ? $media['file_url'] : $file_details['output_path'].$thumb;
									if (substr($media['file_url_thumb'], 0, 4) == 'http') {
										$csvilog->addDebug(JText::sprintf('COM_CSVI_RESET_THUMB_NOHTTP', $media['file_url_thumb']));
										$media['file_url_thumb'] = '';
									}
								}
							}
							else {
								$media['file_is_product_image'] = 0;
								$media['file_url_thumb'] = '';
							}

							// Bind the media data
							$this->_medias->bind($media);

							// Check if the media image already exists
							$this->_medias->check();

							// Store the media data
							if ($this->_medias->store()) {
								if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_MEDIA'));
								else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_MEDIA'));

								// Store the debug message
								$csvilog->addDebug('COM_CSVI_MEDIA_QUERY', true);

								// Watermark the image
								if ($template->get('full_watermark','image') && $file_details['isimage']) $imagehelper->addWatermark(JPATH_SITE.'/'.$media['file_url']);

								// Store the product image relation
								$data = array();
								$data['virtuemart_product_id'] = $this->virtuemart_product_id;
								$data['virtuemart_media_id'] = $this->_medias->virtuemart_media_id;
								$data['ordering'] = (isset($order[$key]) && !empty($order[$key])) ? $order[$key] : $ordering;
								$this->_product_medias->bind($data);
								if (!$this->_product_medias->check()) {
									if ($this->_product_medias->store()) {
										$csvilog->addDebug('COM_CSVI_STORE_PRODUCT_IMAGE_RELATION', true);
										$ordering++;
									}
								}
								else {
									$csvilog->addDebug('COM_CSVI_PRODUCT_IMAGE_RELATION_EXISTS');
								}
							}
							else {
								$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MEDIA_NOT_ADDED', $this->_medias->getError()));
								return false;
							}
							// Reset the product media table
							$this->_medias->reset();
							$this->_product_medias->reset();
						} // else
					} // if
				} // if
			} // foreach
		} // if
	}

	/**
	 * Manufacturer Importer
	 *
	 * Adds or updates a manufacturer and adds a reference to the product
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
	private function _manufacturerImport() {

		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$csvilog->addDebug('COM_CSVI_DEBUG_MANUFACTURER_IMPORT');

		if (!isset($this->_manufacturers_lang->mf_name) && !isset($this->_manufacturers_lang->virtuemart_manufacturer_id)) {
			// User is not importing manufacturer data but we need a default manufacturer associated with the product
			$this->_getDefaultManufacturerID();
		}

		// Check for existing manufacturer
		if ($this->_manufacturers_lang->check()) {
			// Store the manufacturers language details
			if ($this->_manufacturers_lang->store()) {
				if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_MANUFACTURER_LANG'));
				else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_MANUFACTURER_LANG'));
			}
			else {
				$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MANUFACTURER_LANG_NOT_ADDED', $this->_manufacturers_lang->getError()));
				return false;
			}

			// Store the debug message
			$csvilog->addDebug('COM_CSVI_MANUFACTURER_LANG_QUERY', true);

			// Set the manufacturer ID
			$this->_manufacturers->virtuemart_manufacturer_id = $this->_manufacturers_lang->virtuemart_manufacturer_id;

			// Check if a manufacturer exists
			if (!$this->_manufacturers->check()) {
				// Store the manufacturer data
				if ($this->_manufacturers->store()) {
					if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_MANUFACTURER'));
					else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_MANUFACTURER'));
				}
				else {
					$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MANUFACTURER_NOT_ADDED', $this->_manufacturers->getError()));
					return false;
				}

				// Store the debug message
				$csvilog->addDebug('COM_CSVI_MANUFACTURER_QUERY', true);
			}
		}
	}

	/**
	 * Adds a reference between manufacturer and product
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
	private function _manufacturerCrossReference() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$this->_product_manufacturers->virtuemart_product_id = $this->virtuemart_product_id;
		$this->_product_manufacturers->virtuemart_manufacturer_id = $this->_manufacturers_lang->virtuemart_manufacturer_id;
		if (!$this->_product_manufacturers->check()) {
			$this->_product_manufacturers->store();
			$csvilog->addDebug('COM_CSVI_DEBUG_PROCESS_MANUFACTURER_PRODUCT', true);
		}
	}

	/**
	 * Creates either an update or insert SQL query for a product price.
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo 		add price calculations
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _priceQuery() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);

		// Check if we have a child product with an empty price (will use parents price)
		if ($this->child_product && ($this->product_price == 0 && (is_null($this->price_with_tax) && is_null($this->product_tax)))) {
			$csvilog->addDebug('COM_CSVI_DEBUG_CHILD_NO_PRICE');
		}
		else {
			// Check if we have an override price, this is always excluding tax
			if ($this->product_override_price) {
				if (is_null($this->override)) $this->override = 1;
			}

			// Check if the price is including or excluding tax
			if (($this->product_tax_id || $this->product_tax) && $this->price_with_tax && is_null($this->product_price)) {
				if (strlen($this->price_with_tax) == 0) $this->product_price = null;
				else {
					// Check if we have an ID or a value
					if ($this->product_tax_id) {
						// Find the value
						$this->_calcs->load($this->product_tax_id);
						$this->product_tax = $this->_calcs->calc_value;
					}
					$this->product_price = $this->price_with_tax / (1+($this->product_tax/100));
				}
			}
			else if (strlen($this->product_price) == 0) $this->product_price = null;

			// Check if we need to assign a shopper group
			if (!is_null($this->shopper_group_name_price)) {
				if ($this->shopper_group_name_price == '*') $this->virtuemart_shoppergroup_id = 0;
				else $this->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($this->shopper_group_name_price);
			}

			// Bind the fields to check for an existing price
			$this->_product_prices->bind($this);

			// Check if the price already exists
			if (!$this->_product_prices->check()) {
				// Price doesn't exist
				if (!$this->_product_prices->get('price_quantity_start')) $this->_product_prices->price_quantity_start = 0;
				if (!$this->_product_prices->get('price_quantity_end')) $this->_product_prices->price_quantity_end = 0;
				if (!$this->_product_prices->get('override')) $this->_product_prices->override = 0;
				if (!$this->_product_prices->get('product_price_publish_up')) $this->_product_prices->product_price_publish_up = '0000-00-00 00:00:00';
				if (!$this->_product_prices->get('product_price_publish_down')) $this->_product_prices->product_price_publish_down = '0000-00-00 00:00:00';

				// Set the create date if the user has not done so and there is no product_price_id
				if (!$this->_product_prices->get('created_on')) {
					$this->_product_prices->created_on = $this->date->toSql();
					$this->_product_prices->created_by = $this->user->id;
				}
			}

			// Bind the data
			$this->_product_prices->bind($this);

			// Check if we need to change the shopper group name
			if (!is_null($this->shopper_group_name_new)) {
				if ($this->shopper_group_name_new == '*') $this->_product_prices->virtuemart_shoppergroup_id = 0;
				else {
					$this->_product_prices->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($this->shopper_group_name_new);
				}
			}

			// Calculate the new price
			$this->_product_prices->CalculatePrice();

			// Store the price
			// Add some variables if needed
			// Set the modified date if the user has not done so
			if (!$this->_product_prices->get('modified_on')) {
				$this->_product_prices->set('modified_on', $this->date->toSql());
				$this->_product_prices->set('modified_by', $this->user->id);
			}

			// Store the price
			$this->_product_prices->store();

			$csvilog->addDebug('COM_CSVI_DEBUG_PRICE_QUERY', true);
		}
	}

	/**
	 * Stores the discount for a product
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo 		Add logging
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		3.0
	 */
	private function _processDiscount() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$csvilog->addDebug('COM_CSVI_DEBUG_PROCESSING_DISCOUNT');

		// Clear the calcs from any data
		$this->_calcs->reset();

		// Determine if the discount field is a percentage
		if ($this->product_discount) {
			if (substr($this->product_discount,-1,1) == "%") {
				$this->_calcs->calc_value_mathop = '-%';
				$this->_calcs->calc_value = substr($this->toPeriod($this->product_discount), 0, -1);
			}
			else {
				$this->_calcs->calc_value_mathop = '-';
				$this->_calcs->calc_value = $this->cleanPrice($this->product_discount);
			}
		}

		if (!is_null($this->_calcs->calc_value) && $this->_calcs->calc_value > 0) {
			// Add the discount fields
			$this->_calcs->publish_up = $this->product_discount_date_start;
			$this->_calcs->publish_down = $this->product_discount_date_end;

			// Add a description to the discount
			$this->_calcs->calc_name = $this->product_discount;
			$this->_calcs->calc_descr = $this->product_discount;
			$this->_calcs->calc_shopper_published = 1;
			$this->_calcs->calc_vendor_published = 1;
			$this->_calcs->calc_currency = $this->_product_prices->product_currency;
			if (empty($this->calc_kind)) $this->_calcs->calc_kind = 'DBTax';
			else $this->_calcs->calc_kind = $this->calc_kind;

			// Check if a discount already exists
			$this->_calcs->check();

			// Store the discount
			if (!$this->_calcs->store()) {
				$csvilog->addDebug('COM_CSVI_DEBUG_ADD_DISCOUNT', true);
				return false;
			}
			$csvilog->addDebug('COM_CSVI_DEBUG_ADD_DISCOUNT', true);
			// Fill the product information with the discount ID
			$this->product_discount_id = $this->_calcs->virtuemart_calc_id;
		}
		else $csvilog->addDebug('COM_CSVI_DEBUG_NO_DISCOUNT');
	}

	/**
	* Process a tax rate
	*
	* @copyright
	* @author 		RolandD
	* @todo
	* @see
	* @access 		public
	* @param
	* @return
	* @since 		4.0
	*/
	private function _processTax() {
		if ($this->product_tax > 0) {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			$csvilog->addDebug('COM_CSVI_DEBUG_PROCESSING_TAX');

			// Clear the calcs from any data
			$this->_calcs->reset();

			// Add some data
			$this->_calcs->calc_kind = 'Tax';
			$this->_calcs->calc_value = $this->product_tax;
			$this->_calcs->calc_value_mathop = '+%';

			// Check if the tax rate already exists
			if (!$this->_calcs->check()) {
				$this->_calcs->virtuemart_vendor_id = $this->virtuemart_vendor_id;
				$this->_calcs->calc_name = JText::_('COM_CSVI_AUTO_TAX_RATE');
				$this->_calcs->calc_descr = JText::_('COM_CSVI_AUTO_TAX_RATE_DESC');
				$this->_calcs->calc_currency = $this->helper->getVendorCurrency($this->virtuemart_vendor_id);
				$this->_calcs->calc_shopper_published = 1;
				$this->_calcs->calc_vendor_published = 1;
				$this->_calcs->publish_up = $this->date->toMySQL();
				$this->_calcs->created_on = $this->date->toMySQL();
				$this->_calcs->created_by = $this->user->id;
				$this->_calcs->modified_on = $this->date->toMySQL();
				$this->_calcs->modified_by = $this->user->id;
				$this->_calcs->store();

				$csvilog->addDebug('COM_CSVI_ADD_TAX_RATE', true);
			}

			$this->product_tax_id = $this->_calcs->virtuemart_calc_id;
		}
	}

	/**
  	 * Gets the default manufacturer ID
  	 * As there is no default manufacturer, we take the first one
  	 *
  	 * @copyright
  	 * @author		RolandD
  	 * @todo
  	 * @see
  	 * @access		private
  	 * @param
  	 * @return 		integer	database ID of the default manufacturer
  	 * @since		4.0
  	 */
	private function _getDefaultManufacturerID() {
		$jinput = JFactory::getApplication()->input;
		$db = JFactory::getDbo();
		$csvilog = $jinput->get('csvilog', null, null);

		// Check if product already has a manufacturer link
		if (isset($this->product_sku)) {
			$query = $db->getQuery(true);
			$query->select($db->qn('m.virtuemart_manufacturer_id'));
			$query->from($db->qn('#__virtuemart_product_manufacturers', 'm'));
			$query->leftJoin($db->qn('#__virtuemart_products', 'p').' ON '.$db->qn('m.virtuemart_product_id').' = '.$db->qn('p.virtuemart_product_id'));
			$query->where($db->qn('p.product_sku').' = '.$db->q($this->product_sku));
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_GET_MANUFACTURER_ID_SKU', true);
			$mf_id = $db->loadResult();
		}
		else if (isset($this->virtuemart_product_id)) {
			$query = $db->getQuery(true);
			$query->select($db->qn('virtuemart_manufacturer_id'));
			$query->from($db->qn('#__virtuemart_product_manufacturers'));
			$query->where($db->qn('virtuemart_product_id').' = '.$db->q($this->virtuemart_product_id));
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_GET_MANUFACTURER_ID_ID', true);
			$mf_id = $db->loadResult();
		}

		// Check if we have a result
		if (!$mf_id) {
			$query = $db->getQuery(true);
			$query->select('MIN(virtuemart_manufacturer_id)');
			$query->from($db->qn('#__virtuemart_manufacturers'));
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_GET_DEFAULT_MANUFACTURER_ID', true);
			$mf_id = $db->loadResult();
		}

		$this->_manufacturers_lang->virtuemart_manufacturer_id = $mf_id;
	}

	/**
	* Create image name
	*
	* Check if the user wants to have CSVI VirtueMart create the image names if so
	* create the image names without path
	*
	* @copyright
	* @author		RolandD
	* @todo
	* @see 			processImage()
	* @access 		private
	* @param		int	$ordering	The number to apply to a generated image name
	* @return
	* @since 		3.0
	*/
	private function _createImageName($ordering = 0) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		$csvilog->addDebug('COM_CSVI_GENERATE_IMAGE_NAME');

		// Create extension
		$ext = $template->get('autogenerateext', 'image');

		// Check if the user wants to convert the images to a different type
		switch ($template->get('type_generate_image_name', 'image')) {
			case 'product_sku':
				$csvilog->addDebug('COM_CSVI_CREATE_PRODUCT_SKU_NAME');
				if (!is_null($this->product_sku)) $name = $this->product_sku;
				else {
					$csvilog->AddStats('error', JText::_('COM_CSVI_CANNOT_FIND_PRODUCT_SKU'));
					return false;
				}
				break;
			case 'product_name':
				$csvilog->addDebug('COM_CSVI_CREATE_PRODUCT_NAME_NAME');
				if (!is_null($this->_products_lang->product_name)) $name = $this->_products_lang->product_name;
				else {
					$csvilog->AddStats('error', JText::_('COM_CSVI_CANNOT_FIND_PRODUCT_NAME'));
					return false;
				}
				break;
			case 'product_id':
				$csvilog->addDebug('COM_CSVI_CREATE_PRODUCT_ID_NAME');
				if (!is_null($this->virtuemart_product_id)) $name = $this->virtuemart_product_id;
				else {
					$csvilog->AddStats('error', JText::_('COM_CSVI_CANNOT_FIND_PRODUCT_ID'));
					return false;
				}
				break;
			case 'random':
				$csvilog->addDebug('COM_CSVI_CREATE_RANDOM_NAME');
				$name = mt_rand();
				break;
		}

		// Build the new name
		if ($ordering > 0) $image_name = $name.'_'.$ordering.'.'.$ext;
		else $image_name = $name.'.'.$ext;

		$csvilog->addDebug(JText::sprintf('COM_CSVI_CREATED_IMAGE_NAME', $image_name));
		$this->product_full_image_output = $image_name;

		// Check if the user is supplying image data
		if (is_null($this->file_url)) {
			$this->file_url = $this->product_full_image_output;
			return $this->file_url;
		}
	}

	/**
	 * Process custom fields
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		4.0
	 */
	private function _processCustomFields() {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();

		// Get the values
		$values = explode('~', $this->custom_value);
		$prices = explode('~', $this->custom_price);
		$params = explode('~', $this->custom_param);
		$titles = explode('~', $this->custom_title);
		$ordering = explode('~', $this->custom_ordering);
		$multiples = explode('~', $this->custom_multiple);
		$deletes = explode('~', $this->custom_delete);

		// Delete all custom fields
		if (!empty($deletes)) {
			foreach ($deletes as $key => $value) {
				if ($value == 'Y' && isset($titles[$key])) {
					// Find the custom details
					$query = $db->getQuery(true);
					$query->select('virtuemart_custom_id')->from('#__virtuemart_customs')->where($db->qn('custom_title').' = '.$db->q($titles[$key]));
					$db->setQuery($query);
					$virtuemart_custom_id = $db->loadResult();

					// Delete the custom entry
					$query = $db->getQuery(true);
					$query->delete($db->qn('#__virtuemart_product_customfields'));
					$query->where($db->qn('virtuemart_product_id').' = '.$db->q($this->virtuemart_product_id));
					$query->where($db->qn('virtuemart_custom_id').' = '.$virtuemart_custom_id);
					$db->setQuery($query);
					$db->query();
					$csvilog->addDebug('COM_CSVI_REMOVE_EXISTING_CUSTOM_VALUES', true);
				}
			}
		}


		// Process all fields
		if (count($values) == count($titles)) {
			// We need to clean the custom titles otherwise values are not cleaned
			if (strtoupper($multiples[$key]) == 'N') $this->_customtitles = array();

			foreach ($values as $key => $value) {
				// Check if the value is not deleted
				if (is_null($this->custom_delete) || (isset($deletes[$key]) && $deletes[$key] !== 'Y')) {
					// Get the custom ID
					if (!isset($this->_customtitles[$titles[$key]])) {
						$query = $db->getQuery(true);
						$query->select('custom_parent_id, virtuemart_custom_id')->from('#__virtuemart_customs')->where($db->qn('custom_title').' = '.$db->q($titles[$key]));
						$db->setQuery($query);
						$virtuemart_custom = $db->loadObject();
						$csvilog->addDebug('COM_CSVI_DEBUG_CUSTOMFIELD_QUERY', true);
						if ($virtuemart_custom) {
							$virtuemart_custom_id = $virtuemart_custom->virtuemart_custom_id;
							$this->_customtitles[$titles[$key]] = $virtuemart_custom;

							// Empty out any existing values
							$query = $db->getQuery(true);
							$query->delete($db->qn('#__virtuemart_product_customfields'));
							$query->where($db->qn('virtuemart_product_id').' = '.$db->q($this->virtuemart_product_id));
							$query->where($db->qn('virtuemart_custom_id').' = '.$virtuemart_custom_id);
							$db->setQuery($query);
							$db->query();
							$csvilog->addDebug('COM_CSVI_REMOVE_EXISTING_CUSTOM_VALUES', true);
						}
						else {
							$csvilog->addDebug('COM_CSVI_NO_CUSTOM_ID_FOUND');
							$virtuemart_custom = false;
						}
					}
					else {
						$virtuemart_custom = $this->_customtitles[$titles[$key]];
					}

					if ($virtuemart_custom) {
						// Set the product ID
						$this->_product_customfields->virtuemart_product_id = $this->virtuemart_product_id;
						$this->_product_customfields->virtuemart_custom_id = $virtuemart_custom->virtuemart_custom_id;
						$this->_product_customfields->custom_value = $value;
						if (isset($prices[$key])) $this->_product_customfields->custom_price = $prices[$key];
						if (isset($ordering[$key])) $this->_product_customfields->ordering = $ordering[$key];
						if (isset($params[$key])) {
							// See if we are dealing with a stockable variant
							if ($value == 'stockable') {
								// We need to create a new object
								$param_value = new stdClass();
								$param_value->child = new stdClass();

								// Data is received in the format:
								// product_sku[option1#option2[price;product_sku[option1#option2[price

								// Get all the products
								$param_entries = explode(';', $params[$key]);

								foreach ($param_entries as $entry) {
									if (!empty($entry)) {
										$param_sku = false;
										$entry_parts = explode('[', $entry);

										// Create the new class
										$sku = new stdClass();
										$sku->is_variant = 1;

										if (isset($entry_parts[0]) && !empty($entry_parts[0])) {
											// Find the product ID
											$param_sku = $entry_parts[0];
											$params_options = explode('#', $entry_parts[1]);
											foreach ($params_options as $pkey => $param_option) {
												$name = 'selectoptions'.($pkey+1);
												$sku->$name = $param_option;
											}
											if (isset($entry_parts[2]) && !empty($entry_parts[2])) $sku->custom_price = $entry_parts[2];
											else $sku->custom_price = '';
										}

										if ($param_sku) $param_value->child->$param_sku = $sku;
									}
								}
								$this->_product_customfields->custom_param = json_encode($param_value);
							}
							else if ($value == 'param') {
								// Data is received in the format:
								// value1#value2;value1#value2

								// Get all the values
								$param_entries = explode('#', $params[$key]);

								// Remove existing values for this parameter
								$query = $db->getQuery(true)->delete($db->qn('#__virtuemart_product_custom_plg_param_ref'))->where($db->qn('virtuemart_product_id').'='.$this->virtuemart_product_id)->where($db->qn('virtuemart_custom_id').'='.$virtuemart_custom->virtuemart_custom_id);
								$db->setQuery($query);
								if ($db->query()) {
									// Load the custom field parameters
									$query = $db->getQuery(true)->select($db->qn('custom_params'))->from($db->qn('#__virtuemart_customs'))->where($db->qn('virtuemart_custom_id').' = '.$virtuemart_custom_id);
									$db->setQuery($query);
									$result = $db->loadResult();
									$cparams = explode('|', $result);
									$ft = array();
									foreach ($cparams as $cparam) {
										if (!empty($cparam)) {
											$statement = preg_match("/^(?!;)(?P<key>[\w+\.\-]+?)\s*=\s*(?P<value>.+?)\s*$/", $cparam, $match );
											$fparams[$match['key']] = str_ireplace('"', '', $match['value']);
										}
									}

									// Load the IDs
									$qentries = array();
									foreach ($param_entries as $entry) {
										$qentries[] = $db->q($entry);
									}

									// Check _values and add if !isset
									$query = $db->getQuery(true)->select($db->qn('value'))->from($db->qn('#__virtuemart_product_custom_plg_param_values'))->where($db->qn('virtuemart_custom_id').' = '.$virtuemart_custom->virtuemart_custom_id)->order($db->qn('id'));
									$db->setQuery($query);
									$values = $db->loadResultArray();
									$query = $db->getQuery(true)->insert($db->qn('#__virtuemart_product_custom_plg_param_values'))->columns(array('virtuemart_custom_id', 'value','status', 'published', 'ordering'));
									foreach($param_entries as $entry){
										if(!in_array($entry,$values)){
											$query->values($db->q($virtuemart_custom->virtuemart_custom_id).','.$db->q($entry).',0,1,0');
										}
									}
									$db->setQuery($query);
									$db->execute();

									$query = $db->getQuery(true)->select($db->qn('id'))->from($db->qn('#__virtuemart_product_custom_plg_param_values'))->where($db->qn('value').' IN ('.implode(',', $qentries).')')->order($db->qn('id'));
									$db->setQuery($query);
									$pids = $db->loadColumn();

									// Add all the new values to the database
									$query = $db->getQuery(true)->insert($db->qn('#__virtuemart_product_custom_plg_param_ref'))->columns(array('virtuemart_product_id', 'virtuemart_custom_id','val', 'intval'));
									foreach ($pids as $pid) {
										if ($fparams['ft'] == "int") {
											$intval = reset($param_entries);
											$intval = (float)$intval;
											$pid = 0;
										}
										else $intval = 0;
										$query->values($this->virtuemart_product_id.','.$virtuemart_custom_id.','.$pid.','.$intval);
									}
									$db->setQuery($query);
									$db->execute();
									$csvilog->addDebug('COM_CSVI_DEBUG_CUSTOMFIELD_PARAM_QUERY', true);
								}
								$this->_product_customfields->custom_param = '';
							}
							else $this->_product_customfields->custom_param = $params[$key];
						}

						// Check for an existing entry
						if (!$this->_product_customfields->check()) {
							$this->_product_customfields->created_on = $this->date->toSql();
							$this->_product_customfields->created_by = $this->user->id;
						}

						// Set a modified date
						if (!isset($this->modified_on)) {
							$this->_product_customfields->modified_on = $this->date->toSql();
							$this->_product_customfields->modified_by = $this->user->id;
						}
						else {
							$this->_product_customfields->modified_on = $this->modified_on;
							$this->_product_customfields->modified_by = $this->user->id;
						}

						// Store the custom field
						if ($this->_product_customfields->store()) {
							$csvilog->addDebug('COM_CSVI_DEBUG_CUSTOMFIELD_QUERY', true);

							// Check if we need to add the parent field
							if ($virtuemart_custom->custom_parent_id > 0) {
								// Check if the custom parent is already set
								$query = $db->getQuery(true);
								$query->select($db->qn('virtuemart_customfield_id'))->from($db->qn('#__virtuemart_product_customfields'))->where($db->qn('virtuemart_custom_id').'='.$virtuemart_custom->custom_parent_id)->where($db->qn('virtuemart_product_id').'='.$db->q($this->virtuemart_product_id));
								$db->setQuery($query);
								$cid = $db->loadResult();
								if (empty($cid)) {
									// Add the parent
									$query->clear();
									$query->select($db->qn('custom_value'))->from($db->qn('#__virtuemart_customs'))->where($db->qn('virtuemart_custom_id').'='.$virtuemart_custom->custom_parent_id);
									$db->setQuery($query);
									$parent_name = $db->loadResult();
									$this->_product_customfields->virtuemart_customfield_id = null;
									$this->_product_customfields->custom_price = null;
									$this->_product_customfields->virtuemart_custom_id = $virtuemart_custom->custom_parent_id;
									$this->_product_customfields->custom_value = $parent_name;
									if ($this->_product_customfields->store()) {
										$csvilog->addDebug('COM_CSVI_DEBUG_CUSTOMFIELD_PARENT_QUERY', true);
									}
								}
							}
						}
					}
				} // if

				// Reset the field
				$this->_product_customfields->reset();
			} // foreach
		} // if
	}

	/**
	 * Combine the ICEcat features
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.3
	 */
	private function _icecatFeatures() {
		$table = '<table id="prod_features">';
		$table .= '<thead></thead>';
		$table .= '<tfoot></tfoot>';
		$table .= '<tbody>';
		foreach ($this->features as $featureid => $details) {
			foreach ($details as $feature => $values) {
				$table .= '<tr><td colspan="2" class="feature">'.$feature.'</td></tr>';
				foreach ($values as $name => $value) {
					$table .= '<tr><td>'.$name.'</td><td>'.$value.'</td></tr>';
				}
			}
		}
		$table .= '</tbody>';
		$table .= '</table>';

		// Add the table to the product desc
		$this->product_desc .= $table;
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
				$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_PRODUCT_LANG_XREF', true);
				$db->query();
			}

			// Delete category reference
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_categories');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_CATEGORY_XREF', true);
			$db->query();

			// Delete manufacturer reference
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_manufacturers');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_MANUFACTURER_XREF', true);
			$db->query();

			// Reset child parent reference
			$query = $db->getQuery(true);
			$query->update('#__virtuemart_products');
			$query->set('product_parent_id = 0');
			$query->where('product_parent_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_PRODUCT_PARENT', true);
			$db->query();

			// Delete prices
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_prices');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_PRICES_XREF', true);
			$db->query();

			// Delete shopper groups
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_shoppergroups');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_SHOPPERGROUP_XREF', true);
			$db->query();

			// Delete prices
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_prices');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_PRICES_XREF', true);
			$db->query();

			// Delete custom fields
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_customfields');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_CUSTOMFIELDS_XREF', true);
			$db->query();

			// Delete media
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_medias');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_MEDIA_XREF', true);
			$db->query();

			// Delete ratings
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_ratings');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_RATINGS_XREF', true);
			$db->query();

			// Delete rating reviews
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_rating_reviews');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_RATING_REVIEWS_XREF', true);
			$db->query();

			// Delete rating votes
			$query = $db->getQuery(true);
			$query->delete('#__virtuemart_product_rating_votes');
			$query->where('virtuemart_product_id = '.$this->virtuemart_product_id);
			$db->setQuery($query);
			$csvilog->addDebug('COM_CSVI_DEBUG_DELETE_RATING_VOTES_XREF', true);
			$db->query();

			$csvilog->AddStats('deleted', JText::sprintf('COM_CSVI_PRODUCT_DELETED', $this->record_identity));
		}
		else {
			$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_NOT_DELETED', $this->record_identity));
		}

		return true;
	}

	/**
	 * Convert the product SKU to product ID in the parent properties
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		1.0
	 */
	private function _processParentValues($child=true) {
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$db = JFactory::getDbo();

		if ($child && isset($this->product_sku) && !is_null($this->product_parent_id)) {
			$param_sku = $this->_products->virtuemart_product_id;
			$sku = $this->product_sku;

			// Load the values
			$query = $db->getQuery(true);
			$query->select('custom_param');
			$query->from('#__virtuemart_product_customfields');
			$query->where('virtuemart_product_id = '.$this->product_parent_id);
			$query->where('custom_value = '.$db->q('stockable'));
			$db->setQuery($query);
			$params = $db->loadResult();
			$values = json_decode($params);

			// Replace the key if it exists
			if (isset($values->child->$sku)) {
				$values->child->$param_sku = $values->child->$sku;
				unset($values->child->$sku);

				// Store the values
				$query = $db->getQuery(true);
				$query->update('#__virtuemart_product_customfields');
				$query->set('custom_param = '.$db->q(json_encode($values)));
				$query->where('virtuemart_product_id = '.$this->product_parent_id);
				$query->where('custom_value = '.$db->q('stockable'));
				$db->setQuery($query);
				$db->query();

				$csvilog->addDebug('COM_CSVI_DEBUG_STORE_PARENT_VALUE', true);
			}
			else {
				$csvilog->addDebug('COM_CSVI_DEBUG_NO_PARENT_VALUE_FOUND', true);
			}
		}
		// Only parents are imported
		else if (!$child) {
			// Get all the parameters
			$query = $db->getQuery(true);
			$query->select('custom_param');
			$query->from('#__virtuemart_product_customfields');
			$query->where('virtuemart_product_id = '.$this->_products->virtuemart_product_id);
			$query->where('custom_value = '.$db->quote('stockable'));
			$db->setQuery($query);
			$params = $db->loadResult();
			$values = json_decode($params);

			if (is_object($values)) {
				// Replace the key if it exists
				foreach ($values->child as $child_sku => $details) {
					// Get the product ID of the child
					$query = $db->getQuery(true);
					$query->select('virtuemart_product_id');
					$query->from('#__virtuemart_products');
					$query->where('product_sku = '.$db->quote($child_sku));
					$db->setQuery($query);
					$child_id = $db->loadResult();
					if ($child_id) {
						$values->child->$child_id = $details;
						unset($values->child->$child_sku);
					}
					else $csvilog->addDebug('COM_CSVI_DEBUG_NO_CHILD_VALUE_FOUND', true);
				}

				// Store the values
				$query = $db->getQuery(true);
				$query->update('#__virtuemart_product_customfields');
				$query->set('custom_param = '.$db->quote(json_encode($values)));
				$query->where('virtuemart_product_id = '.$this->_products->virtuemart_product_id);
				$query->where('custom_value = '.$db->quote('stockable'));
				$db->setQuery($query);
				$db->query();
				$csvilog->addDebug('COM_CSVI_DEBUG_STORE_PARENT_VALUE', true);
			}
			else {
				$csvilog->addDebug('COM_CSVI_DEBUG_NO_PARENT_VALUE_FOUND', true);
			}
		}

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
			foreach ($this->_customfields as $field) {
				$title = $field->title;
				$csvilog->addDebug('Processing custom available field: '.$title);
				if (isset($this->$title)) {
					// Delete the existing value
					$query = $db->getQuery(true)
						->delete('#__virtuemart_product_customfields')
						->where('virtuemart_product_id = '.$this->virtuemart_product_id)
						->where('virtuemart_custom_id = '.$field->id);
					$db->setQuery($query);
					$db->query();

					// Check if we need to do any formatting
					switch ($field->field_type) {
						case 'D':
							// Date format needs to be YYYY/MM/DD
							$value = $this->convertDate($this->$title, 'date');
							break;
						case 'M':
							// The media field uses a name and we need an ID
							$query = $db->getQuery(true)
								->select('virtuemart_media_id')
								->from('#__virtuemart_medias')
								->where('file_url = '.$db->q($this->$title));
							$db->setQuery($query);
							$value = $db->loadResult();
							break;
						default:
							$value = $this->$title;
							break;
					}

					// Insert query if it is not empty
					if (!empty($value)) {
						$query = $db->getQuery(true)
							->insert('#__virtuemart_product_customfields')
							->columns(array('virtuemart_product_id', 'virtuemart_custom_id', 'custom_value'))
							->values($this->virtuemart_product_id.', '.$field->id.','.$db->q($value));
						$db->setQuery($query);
						$db->query();
						$csvilog->addDebug('Store custom available field', true);
					}
				}
			}
		}
		else $csvilog->addDebug('No custom available fields found');
	}

	/**
	 * Process the shopper groups
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		private
	 * @param
	 * @return
	 * @since 		4.5.2
	 */
	private function _processShopperGroup() {
		if (!empty($this->shopper_group_name)) {
			$jinput = JFactory::getApplication()->input;
			$csvilog = $jinput->get('csvilog', null, null);
			// Get the shopper group names
			$names = explode('|', $this->shopper_group_name);
			foreach ($names as $name) {
				$data = array();
				$data['virtuemart_shoppergroup_id'] = $this->helper->getShopperGroupId($name);
				$data['virtuemart_product_id'] = $this->virtuemart_product_id;

				// Set the shopper group ID for other updates
				$this->virtuemart_shoppergroup_id = $data['virtuemart_shoppergroup_id'];

				// Bind the data to check
				$this->_product_shoppergroups->bind($data);

				// Check if a product - shopper group relation exists
				if(!$this->_product_shoppergroups->check()) {
					if ($this->_product_shoppergroups->store()) {
						if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_PRODUCT_SHOPPERGROUP'));
						else if ($this->queryResult() == 'INSERT') $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_PRODUCT_SHOPPERGROUP'));
					}
					else {
						$csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_PRODUCT_SHOPPERGROUP_NOT_ADDED', $this->_product_shoppergroups->getError()));
						return false;
					}
				}
				// Clean up
				$this->_product_shoppergroups->reset();
			}
		}
	}
}