<?php
/**
 * Analyzer model
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: about.php 2030 2012-06-14 15:06:23Z RolandD $
 */

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

/**
 * Analyzer Model
 */
class CsviModelAnalyzer extends JModelLegacy {

	private $_file = null;
	private $_lines = 3;
	private $_columnheader = true;
	private $_bom = false;
	private $_csverrors = array();
	private $_messages = array();
	private $_recommend = array();
	private $_data = '';
	private $_text_enclosure = '"';
	private $_field_delimiter = null;
	private $_fields = array();
	private $_csvdata = array();

	/**
	 * Analyze the uploaded file
	 *
	 * @copyright
	 * @author 		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return
	 * @since 		5.3.3
	 */
	public function getAnalyze() {
		// Prepare
		if ($this->_prepare()) {

			// Read the file
			$handle = fopen($this->_file, "r");
			if ($handle) {
				// Get the first line
				$this->_data = fread($handle, 4096);

				// Check for Mac line-ending
				if ($this->_checkMac()) {
					// Reload the file
					fclose($handle);
					$handle = fopen($this->_file, "r");
					$this->_data = fread($handle, 4096);
				}

				// Check for BOM
				$this->_checkBom();

				// Find delimiters
				$this->_findDelimiters();

				// Find fields
				$this->_findFields($handle);

				// Find data
				for ($i=0; $i < $this->_lines; $i++) {
					$this->_findData($handle);
				}

				fclose($handle);
			}
		}

		// Combine the data for showing
		$items = new stdClass();
		$items->csverrors = $this->_csverrors;
		$items->messages = $this->_messages;
		$items->fields = $this->_fields;
		$items->csvdata = $this->_csvdata;
		$items->recommend = $this->_recommend;

		return $items;
	}

	private function _prepare() {
		// Assign the local values
		if (empty($_FILES['filename']['tmp_name'])) {
			$this->_csverrors[] = JText::_('COM_CSVI_ANALYZER_NO_FILE');
			return false;
		}
		else {
			$jinput = JFactory::getApplication()->input;
			$this->_file = $_FILES['filename']['tmp_name'];
			$this->_columnheader = $jinput->get('columnheader', false, 'bool');
			$this->_lines = $jinput->get('lines', 3, 'int');
		}
		return true;
	}

	private function _checkMac() {
		$matches = array();
		// Check Windows first
		$total = preg_match('/\r\n/', $this->_data, $matches);
		if (!$total) {
			preg_match('/\r/', $this->_data, $matches);
			if (!empty($matches)) {
				$this->_csverrors['MACLINE'] = JText::_('COM_CSVI_ANALYZER_MAC_LINE');
				$this->_recommend[] = JText::_('COM_CSVI_ANALYZER_MAC_LINE_RECOMMEND');
				// Set auto detect to handle the rest of the file
				ini_set('auto_detect_line_endings', true);
				return true;
			}
		}
		return false;
	}

	private function _checkBom() {
		if (strlen($this->_data) > 3) {
			if (ord($this->_data{0}) == 239 && ord($this->_data{1}) == 187 && ord($this->_data{2}) == 191) {
				$this->_csverrors['BOM'] = JText::_('COM_CSVI_ANALYZER_BOM_FOUND');
				$this->_bom = true;
				$this->_data = substr($this->_data, 3, strlen($this->_data));
			}
		}
	}

	private function _findDelimiters() {
		// 1. Is the user using text enclosures
		$first_char = substr($this->_data, 0, 1);
		$pattern = '/[a-zA-Z0-9_]/';
		$matches = array();
		preg_match($pattern, $first_char, $matches);

		if (count($matches) == 0) {
			// User is using text delimiter
			$this->_text_enclosure = $first_char;
			$this->_messages[] = JText::_('COM_CSVI_ANALYZER_TEXT_ENCLOSURE').$first_char;

			// 2. What field delimiter is being used
			$match_next_char = strpos($this->_data, $this->_text_enclosure, 1);
			$second_char = substr($this->_data, $match_next_char+1, 1);
			if ($first_char == $second_char) {
				$this->_csverrors['NOFIELD'] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER_NOT_FOUND');
			}
			else {
				$this->_field_delimiter = $second_char;
				$this->_messages[] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER').$second_char;
			}
		}
		else {
			// Check for tabs
			$tabs = preg_match('/\t/', $this->_data, $matches);
			if ($tabs) {
				$this->_field_delimiter = "\t";
				$this->_messages[] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER').JText::_('COM_CSVI_ANALYZER_TAB');
			}
			else {
				$totalchars = strlen($this->_data);
				// 2. What field delimiter is being used
				for ($i = 0;$i <= $totalchars; $i++) {
					$current_char = substr($this->_data, $i, 1);
					preg_match($pattern, $current_char, $matches);
					if (count($matches) == 0) {
						$this->_field_delimiter = $current_char;
						$this->_messages[] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER').$current_char;
						$i = $totalchars;
					}
				}
			}
			if (is_null($this->_field_delimiter)) $this->_csverrors['NOFIELD'] = JText::_('COM_CSVI_ANALYZER_FIELD_DELIMITER_NOT_FOUND');
		}
	}

	private function _findFields($handle) {
		rewind($handle);
		$data = fgetcsv($handle, 1000, $this->_field_delimiter, $this->_text_enclosure);
		if ($this->_columnheader) {
			if ($data !== FALSE ) {
				if ($this->_bom) $data[0] = substr($data[0], 3, strlen($data[0]));
				$this->_fields = $data;

				// Check the fields for any _id fields
				foreach ($this->_fields as $field) {
					if (substr($field, -3) == '_id') {
						$this->_recommend[] = JText::sprintf('COM_CSVI_ANALYZER_FIELD_RECOMMEND', $field);
					}
				}
			}
			else $this->_csverrors['NOREAD'] = JText::_('COM_CSVI_ANALYZER_NO_READ');
		}
	}

	private function _findData($handle) {
		$data = fgetcsv($handle, 4096, $this->_field_delimiter, $this->_text_enclosure);
		if ($data !== FALSE ) {
			if ($this->_columnheader) {
				if (count($this->_fields) > count($data)) {
					$this->_csverrors['NODATA'] = JText::_('COM_CSVI_ANALYZER_MORE_FIELDS');
					$this->_recommend[] = JText::_('COM_CSVI_ANALYZER_MORE_FIELDS_RECOMMEND');
				}
				else if (count($this->_fields) < count($data)) {
					$this->_csverrors['NODATA'] = JText::_('COM_CSVI_ANALYZER_LESS_FIELDS');
					$this->_recommend[] = JText::_('COM_CSVI_ANALYZER_LESS_FIELDS_RECOMMEND');
				}
			}
			$this->_csvdata[] = $data;
		}
		else {
			if (!feof($handle))	$this->_csverrors['NOREAD'] = JText::_('COM_CSVI_ANALYZER_NO_READ');
		}
	}
}