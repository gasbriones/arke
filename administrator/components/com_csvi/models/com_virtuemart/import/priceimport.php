<?php
/**
 * Price import
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: mediaimport.php 2048 2012-07-28 16:27:43Z RolandD $
 */

defined( '_JEXEC' ) or die;

/**
 * Processor for price details
 *
 * Main processor for importing prices.
 */
class CsviModelPriceimport extends CsviModelImportfile {

// Private tables
	/** @var object contains the vm_manufacturer table */
	private $_product_prices = null;

	// Public variables
	/** @var integer contains the product ID for a price */
	public $virtuemart_product_id = null;
	/** @var integer contains the ID for a price */
	public $virtuemart_product_price_id = null;
	/** @var integer contains the value if the price needs to be deleted */
	public $price_delete = null;
	/** @var integer contains the id value of the shopper group */
	public $virtuemart_shoppergroup_id = null;
	/** @var string contains the name value of the shopper group */
	public $shopper_group_name = null;
	public $shopper_group_name_new = null;
	/** @var int the price start value */
	public $price_quantity_start = 0;
	/** @var int the pice end value */
	public $price_quantity_end = 0;
	public $product_price_new = null;
	public $product_price_publish_up = null;
	public $product_price_publish_down = null;

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
		// Get the logger
		$jinput = JFactory::getApplication()->input;

		// Load the data
		$this->loadData();

		// Load the helper
		$this->helper = new Com_VirtueMart();

		$this->virtuemart_product_id = $this->helper->getProductId();
		$this->virtuemart_vendor_id = $this->helper->getVendorId();

		// Get the logger
		$csvilog = $jinput->get('csvilog', null, null);

		// Process data
		foreach ($this->csvi_data as $name => $fields) {
			foreach ($fields as $filefieldname => $details) {
				$value = $details['value'];
				// Check if the field needs extra treatment
				switch ($name) {
					case 'product_price_publish_up':
					case 'product_price_publish_down':
						if (!empty($value)) $this->$name = $this->convertDate($value);
						break;
					case 'product_currency':
						$this->$name = $this->helper->getCurrencyId(strtoupper($value), $this->virtuemart_vendor_id);
						break;
					case 'product_price':
					case 'product_price_new':
						$this->$name = $this->toPeriod($value);
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
	 */
	public function getProcessRecord() {
		$db = JFactory::getDbo();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);

		// Get the product ID if we don't already have it
		if (empty($this->virtuemart_product_id)) $this->virtuemart_product_id = $this->helper->getProductId();

		/**
		 * Get the shopper group ID
		 *
		 * The shopper group ID takes preference over the shopper group name
		 */
		if (strlen(trim($this->virtuemart_shoppergroup_id)) == 0) {
			if (strlen(trim($this->shopper_group_name)) > 0) {
				if ($this->shopper_group_name == '*') $this->virtuemart_shoppergroup_id = 0;
				else $this->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($this->shopper_group_name);
			}
			else $this->virtuemart_shoppergroup_id = $this->helper->getDefaultShopperGroupID();
		}

		// Currency check as we need a currency, take VM default currency if not set
		if (!isset($this->product_currency)) {
			$this->product_currency = $this->productCurrency($this->virtuemart_vendor_id);
		}

		// Bind the data
		$this->_product_prices->bind($this);

		// See if we need to find a product_price_id
		if (!isset($this->virtuemart_product_price_id)) {
			$csvilog->addDebug(JText::_('COM_CSVI_FIND_PRODUCT_PRICE_ID'));
			$this->_product_prices->check();
			$this->virtuemart_product_price_id = $this->_product_prices->virtuemart_product_price_id;

			// Bind the new values
			$this->_product_prices->bind($this);
		}
		else {
			$csvilog->addDebug(JText::_('COM_CSVI_ALREADY_HAVE_PRICE_ID'));
		}

		// Let's check for modified and creation dates
		if (empty($this->virtuemart_product_price_id)) {
			$this->_product_prices->created_on = $this->date->toSql();
			$this->_product_prices->modified_on = $this->date->toSql();

			// Check for some other default fields
			if (!isset($this->override)) $this->_product_prices->override = 0;
			if (!isset($this->product_override_price)) $this->_product_prices->product_override_price = 0;
			if (!isset($this->product_tax_id)) $this->_product_prices->product_tax_id = 0;
			if (!isset($this->product_discount_id)) $this->_product_prices->product_discount_id = 0;
			if (empty($this->product_price_publish_up)) $this->_product_prices->product_price_publish_up = '0000-00-00 00:00:00';
			if (empty($this->product_price_publish_down)) $this->_product_prices->product_price_publish_down = '0000-00-00 00:00:00';
		}
		else $this->_product_prices->modified_on = $this->date->toSql();

		// Check if the user wants to delete a price
		if (strtoupper($this->price_delete) == 'Y') {
			if (isset($this->virtuemart_product_price_id)) {
				if (!$this->_product_prices->delete($this->virtuemart_product_price_id)) {
					$csvilog->AddStats('incorrect', JText::_('COM_CSVI_PRICE_NOT_DELETED'));
				}
				else {
					$csvilog->AddStats('deleted', JText::_('COM_CSVI_PRICE_DELETED'));
				}
				$csvilog->addDebug(JText::_('COM_CSVI_TRIED_DELETE_PRICE'), true);
			}
			else $csvilog->AddStats('incorrect', JText::_('COM_CSVI_PRICE_NOT_DELETED'));
		}
		else {
			if (!isset($this->virtuemart_product_id)) {
				$csvilog->addDebug(JText::_('COM_CSVI_NO_PRODUCT_ID_FOUND'));
				$csvilog->AddStats('skipped', JText::_('COM_CSVI_NO_PRODUCT_ID_FOUND'));
			}
			else if (!isset($this->virtuemart_product_price_id) && !isset($this->product_price)) {
				$csvilog->addDebug(JText::_('COM_CSVI_NO_PRODUCT_PRICE_FOUND'));
				$csvilog->AddStats('skipped', JText::_('COM_CSVI_NO_PRODUCT_PRICE_FOUND'));
			}
			else {
				// Check if we need to change the product price
				if (!is_null($this->product_price_new)) {
					$this->_product_prices->product_price = $this->product_price_new;
				}

				// Check if there is an override price
				if (isset($this->product_override_price)) $this->_product_prices->product_override_price = $this->product_override_price;

				// Check if we need to change the shopper group name
				if (!is_null($this->shopper_group_name_new)) {
					if ($this->shopper_group_name_new == '*') $this->_product_prices->virtuemart_shoppergroup_id = 0;
					else {
						$this->_product_prices->virtuemart_shoppergroup_id = $this->helper->getShopperGroupId($this->shopper_group_name_new);
					}
				}

				// See if there is any calculation needed on the prices
				if (isset($this->virtuemart_product_price_id)) $this->_product_prices->CalculatePrice();

				// Store the price
				if ($this->_product_prices->store()) {
					if ($this->queryResult() == 'UPDATE') $csvilog->AddStats('updated', JText::_('COM_CSVI_UPDATE_MULTIPLE_PRICES'));
					else $csvilog->AddStats('added', JText::_('COM_CSVI_ADD_MULTIPLE_PRICES'));
				}
				else $csvilog->AddStats('incorrect', JText::sprintf('COM_CSVI_MULTIPLE_PRICES_NOT_ADDED', $this->_product_prices->getError()));

				// Store the debug message
				$csvilog->addDebug(JText::_('COM_CSVI_MULTIPLE_PRICES_QUERY'), true);
			}
		}

		// Clean the tables
		$this->cleanTables();
	}

	/**
	 * Load the multiple price related tables
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
		$this->_product_prices = $this->getTable('product_prices');
	}

	/**
	 * Cleaning the multiple price related tables
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
		$this->_product_prices->reset();

		// Clean local variables
		$class_vars = get_class_vars(get_class($this));
		foreach ($class_vars as $name => $value) {
			if (substr($name, 0, 1) != '_') {
				$this->$name = $value;
			}
		}
	}
}