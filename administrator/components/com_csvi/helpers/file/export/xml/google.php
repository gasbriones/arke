<?php
/**
 * Google Base XML class
 *
 * @author 		Roland Dalmulder
 * @link 		http://www.csvimproved.com
 * @copyright 	Copyright (C) 2006 - 2013 RolandD Cyber Produksi. All rights reserved.
 * @license 	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version 	$Id: google.php 2314 2013-02-06 09:32:00Z RolandD $
 */

defined( '_JEXEC' ) or die;

class CsviGoogle {

	/** @var string contains the data to export */
	var $contents = "";
	/** @var string contains the XML node to export */
	var $node = "";

	/**
	* Creates the XML header
	*
	* @see $contents
	* @todo take settings from the global array
	* @return string XML header
	 */
	function HeaderText() {
		$this->contents = '<?xml version="1.0" encoding="UTF-8"?>'.chr(10);
		$this->contents .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" ';
		// Google Base Custom Namespace
		$this->contents .= 'xmlns:c="http://base.google.com/cns/1.0">'.chr(10);
		$this->contents .= '<channel>'.chr(10);
		// Get the XML channel header
		$jinput = JFactory::getApplication()->input;
		$settings = $jinput->get('settings', null, null);
		$this->contents .= '<title>'.$settings->get('google_base.gb_title').'</title>'.chr(10);
		$this->contents .= '<link>'.$settings->get('google_base.gb_link').'</link>'.chr(10);
		$this->contents .= '<description>'.$settings->get('google_base.gb_description').'</description>'.chr(10);
		return $this->contents;
	}

	/**
	* Creates the XML footer
	*
	* @see $contents
	* @return string XML header
	 */
	function FooterText() {
		$this->contents = '</channel>'.chr(10);
		$this->contents .= '</rss>'.chr(10);
		return $this->contents;
	}

	/**
	* Opens an XML item node
	*
	* @see $contents
	* @return string XML node data
	 */
	function NodeStart() {
		$this->contents = '<item>'.chr(10);
		return $this->contents;
	}

	/**
	* Closes an XML item node
	*
	* @see $contents
	* @return string XML node data
	 */
	function NodeEnd() {
		$this->contents = '</item>'.chr(10);
		return $this->contents;
	}

	/**
	* Adds an XML element
	*
	* @see $node
	* @return string XML element
	 */
	function Element($column_header, $cdata=false) {
		if (stristr($column_header, 'c:')) {
			$this->node = '<'.$column_header.' type="string">';
		}
		else $this->node = '<'.$column_header.'>';
		if ($cdata) $this->node .= '<![CDATA[';
		$this->node .= $this->contents;
		if ($cdata) $this->node .= ']]>';
		$this->node .= '</'.$column_header.'>';
		$this->node .= "\n";
		return $this->node;
	}

	/**
	* Handles all content and modifies special cases
	*
	* @see $contents
	* @return string formatted XML element
	 */
	function ContentText($content, $column_header, $fieldname, $cdata=false) {
		switch ($fieldname) {
			case 'custom_shipping':
				switch($column_header) {
					case 'g:shipping':
						if (strpos($content, ':')) {
							list($country, $service, $price) = explode(":", $content);
							$this->contents = '
							<g:country>'.$country.'</g:country>
							<g:service>'.$service.'</g:service>
							<g:price>'.$price.'</g:price>
							';
						}
						else $this->contents = '';
					break;
				}
				break;
			case 'custom':
				switch($column_header) {
					case 'g:tech_spec_link':
						$cdate = true;
						$this->contents = $content;
						break;
					case 'g:tax':
						list($country, $region, $rate, $tax_ship) = explode(":", $content);
						$this->contents = '
						<g:country>'.$country.'</g:country>
						<g:region>'.$region.'</g:region>
						<g:rate>'.$rate.'</g:rate>
						<g:tax_ship>'.$tax_ship.'</g:tax_ship>
						';
						break;
					default:
						$this->contents = $content;
						break;
				}
				break;
			case 'category_path':
				// Only export the first category
				$paths = explode("|", $content);
				if (isset($paths[0])) {
					$this->contents = str_replace('/', '>', $paths[0]);
				}
				break;
			case 'picture_url':
				if (empty($column_header)) $column_header = $fieldname;
				if (stripos($content, ',')) {
					// We need to create an entry for each image
					$images = explode(',', $content);
					$xml = '';
					$nr = 1;
					foreach ($images as $image) {
						$this->contents = $image;
						if ($nr == 1) {
							$xml .= $this->Element($column_header, $cdata);
							$nr++;
						}
						else {
							$xml .= $this->Element('g:additional_image_link', $cdata);
						}
					}
				}
				else {
					$this->contents = $content;
					$xml = $this->Element($column_header, $cdata);
				}
				return $xml;
				break;
			case 'manufacturer_name':
			case 'product_url':
				$cdata = true;
			default:
				// Replace certain characters
				if (!$cdata) {
					$find = array();
					$find[] = '&';
					$find[] = '>';
					$find[] = '<';
					$replace = array();
					$replace[] = '&amp;';
					$replace[] = '&gt;';
					$replace[] = '&lt;';
					$this->contents = str_replace($find, $replace, $content);
				}
				else $this->contents = $content;
				break;
		}
		if (empty($column_header)) $column_header = $fieldname;
		return $this->Element($column_header, $cdata);
	}
}