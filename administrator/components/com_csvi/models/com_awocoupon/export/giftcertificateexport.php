<?php
/**
 * AwoCoupon Gift certificate export class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: couponexport.php 1925 2012-03-02 11:51:51Z RolandD $
 */

defined('_JEXEC') or die;

/**
 * Processor for coupons exports
 */
class CsviModelGiftcertificateExport extends CsviModelExportfile {

	// Private variables
	private $_exportmodel = null;

	/**
	 * Gift certificate export
	 *
	 * Exports subscription details data to either csv, xml or HTML format
	 *
	 * @copyright
	 * @author		RolandD
	 * @todo
	 * @see
	 * @access 		public
	 * @param
	 * @return 		void
	 * @since 		3.0
	 */
	public function getStart() {
		// Get some basic data
		$db = JFactory::getDbo();
		$csvidb = new CsviDb();
		$jinput = JFactory::getApplication()->input;
		$csvilog = $jinput->get('csvilog', null, null);
		$template = $jinput->get('template', null, null);
		$exportclass =  $jinput->get('export.class', null, null);
		$export_fields = $jinput->get('export.fields', array(), 'array');

		// Build something fancy to only get the fieldnames the user wants
		$userfields = array();
		foreach ($export_fields as $column_id => $field) {
			if ($field->process) {
				switch ($field->field_name) {
					case 'id':
						$userfields[] = $db->qn('gp').'.'.$db->qn('id');
						break;
					case 'product_id':
					case 'published':
						$userfields[] = $db->qn('gp').'.'.$db->qn($field->field_name);
						break;
					case 'coupon_code':
						$userfields[] = $db->qn('gp').'.'.$db->qn('coupon_template_id');
						break;
					case 'profile_image':
						$userfields[] = $db->qn('gp').'.'.$db->qn('profile_id');
						break;
					case 'estore':
						$userfields[] = $db->qn('gp').'.'.$db->qn('estore');
						break;
					case 'custom':
						break;
					default:
						$userfields[] = $db->qn($field->field_name);
						break;
				}
			}
		}

		// Build the query
		$userfields = array_unique($userfields);
		$query = $db->getQuery(true);
		$query->select(implode(",\n", $userfields));
		$query->from($db->qn('#__awocoupon_giftcert_product', 'gp'));
		$query->leftJoin($db->qn('#__awocoupon_giftcert_code', 'gc').' ON '.$db->qn('gc.product_id').'='.$db->qn('gp.product_id'));
		$query->leftJoin($db->qn('#__virtuemart_products', 'p').' ON '.$db->qn('p.virtuemart_product_id').'='.$db->qn('gp.product_id'));

		// Check if there are any selectors
		$selectors = array();

		// Filter by published state
		$publish_state = $template->get('publish_state', 'general');
		if ($publish_state !== '' && ($publish_state == 1 || $publish_state == 0)) {
			$selectors[] = $db->qn('gp.published').' = '.$publish_state;
		}

		// Filter on product SKU
		$productskufilter = $template->get('product_sku', 'giftcertificate');
		if ($productskufilter !== '') {
			$productskufilter .= ',';
			if (strpos($productskufilter, ',')) {
				$skus = explode(',', $productskufilter);
				$wildcard = '';
				$normal = array();
				foreach ($skus as $sku) {
					if (!empty($sku)) {
						if (strpos($sku, '%')) {
							$wildcard .= $db->qn('p.product_sku'). ' LIKE '.$db->q($sku)." OR ";
						}
						else $normal[] = $db->q($sku);
					}
				}
				if (substr($wildcard, -3) == 'OR ') $wildcard = substr($wildcard, 0, -4);
				if (!empty($wildcard) && !empty($normal)) {
					$selectors[] = '('.$wildcard.' OR '.$db->qn('p.product_sku').' IN ('.implode(',', $normal).'))';
				}
				else if (!empty($wildcard)) {
					$selectors[] = "(".$wildcard.")";
				}
				else if (!empty($normal)) {
					$selectors[] = '('.$db->qn('p.product_sku').' IN ('.implode(',', $normal).'))';
				}
			}
		}

		// Filter on estore
		$estore = $template->get('estore', 'giftcertificate');
		if ($estore !== '') {
			$selectors[] = $db->qn('gp.estore').' = '.$db->q($estore);
		}

		// Filter on template
		$awotemplate = $template->get('template', 'giftcertificate');
		if ($awotemplate !== '') {
			$selectors[] = $db->qn('gp.coupon_template_id').' = '.$db->q($awotemplate);
		}

		// Filter on coupon value type
		$profile = $template->get('profile', 'giftcertificate');
		if ($profile !== '') {
			$selectors[] = $db->qn('gp.profile_id').' = '.$db->q($profile);
		}

		// Check if we need to attach any selectors to the query
		if (count($selectors) > 0 ) $query->where(implode("\n AND ", $selectors));

		// Any fields to ignore
		$ignore = array('product_sku', 'coupon_code', 'profile_image');

		// Check if we need to group the orders together
		$groupby = $template->get('groupby', 'general', false, 'bool');
		if ($groupby) {
			$filter = $this->getFilterBy('groupby', $ignore);
			if (!empty($filter)) $query->group($filter);
		}

		// Order by set field
		$orderby = $this->getFilterBy('sort', $ignore);
		if (!empty($orderby)) $query->order($orderby);

		// Add a limit if user wants us to
		$limits = $this->getExportLimit();

		// Execute the query
		$csvidb->setQuery($query, $limits['offset'], $limits['limit']);
		$csvilog->addDebug(JText::_('COM_CSVI_EXPORT_QUERY'), true);
		// There are no records, write SQL query to log
		if (!is_null($csvidb->getErrorMsg())) {
			$this->addExportContent(JText::sprintf('COM_CSVI_ERROR_RETRIEVING_DATA', $csvidb->getErrorMsg()));
			$this->writeOutput();
			$csvilog->AddStats('incorrect', $csvidb->getErrorMsg());
		}
		else {
			$logcount = $csvidb->getNumRows();
			$jinput->set('logcount', $logcount);
			if ($logcount > 0) {
				while ($record = $csvidb->getRow()) {
					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') $this->addExportContent($exportclass->NodeStart());
					foreach ($export_fields as $column_id => $field) {
						$fieldname = $field->field_name;
						// Add the replacement
						if (isset($record->$fieldname)) $fieldvalue = CsviHelper::replaceValue($field->replace, $record->$fieldname);
						else $fieldvalue = '';
						switch ($fieldname) {
							case 'coupon_code':
								// Get all linked product SKUs
								$query = $db->getQuery(true)
									->select($db->qn('coupon_code'))
									->from($db->qn('#__awocoupon'))
									->where($db->qn('id').' = '.$record->coupon_template_id);
								$db->setQuery($query);
								$code = $db->loadResult();

								// Create the SKUs
								if (strlen(trim($code)) == 0) $username = $field->default_value;
								$code = CsviHelper::replaceValue($field->replace, $code);
								$record->output[$column_id] = $code;
								break;
							case 'profile_image':
								// Get all linked product SKUs
								$query = $db->getQuery(true)
									->select($db->qn('title'))
									->from($db->qn('#__awocoupon_profile'))
									->where($db->qn('id').' = '.$record->profile_id);
								$db->setQuery($query);
								$code = $db->loadResult();

								// Create the SKUs
								if (strlen(trim($code)) == 0) $username = $field->default_value;
								$code = CsviHelper::replaceValue($field->replace, $code);
								$record->output[$column_id] = $code;
								break;
							case 'custom':
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$fieldvalue = CsviHelper::replaceValue($field->replace, $fieldvalue);
								$record->output[$column_id] = $fieldvalue;
								break;
							default:
								// Check if we have any content otherwise use the default value
								if (strlen(trim($fieldvalue)) == 0) $fieldvalue = $field->default_value;
								$record->output[$column_id] = $fieldvalue;
								break;
						}
					}
					// Output the data
					$this->addExportFields($record);

					if ($template->get('export_file', 'general') == 'xml' || $template->get('export_file', 'general') == 'html') {
						$this->addExportContent($exportclass->NodeEnd());
					}

					// Output the contents
					$this->writeOutput();
				}
			}
			else {
				$this->addExportContent(JText::_('COM_CSVI_NO_DATA_FOUND'));
				// Output the contents
				$this->writeOutput();
			}
		}
	}
}