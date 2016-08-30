#
#<?php die('Forbidden.'); ?>
#Date: 2016-03-16 08:24:03 UTC
#Software: Joomla Platform 11.4.0 Stable [ Brian Kernighan ] 03-Jan-2012 00:00 GMT

#Fields: date	time	line_nr	action	comment
2016-03-16	08:24:03	0	[DEBUG]	Importing filetype: csv
2016-03-16	08:24:03	0	[DEBUG]	Found field delimiter: ;
2016-03-16	08:24:03	0	[DEBUG]	Carga campos de configuracion
2016-03-16	08:24:03	0	[DEBUG]	Field: product_sku
2016-03-16	08:24:03	0	[DEBUG]	Field: product_price
2016-03-16	08:24:03	0	[DEBUG]	Field: product_override_price
2016-03-16	08:24:03	0	[DEBUG]	Usar archivo para la configuracion
2016-03-16	08:24:03	0	[DEBUG]	Version CSVI:@version@
2016-03-16	08:24:03	0	[DEBUG]	Version PHP: 5.3.29
2016-03-16	08:24:03	0	[DEBUG]	action: import
2016-03-16	08:24:03	0	[DEBUG]	component: com_virtuemart
2016-03-16	08:24:03	0	[DEBUG]	operation: priceimport
2016-03-16	08:24:03	0	[DEBUG]	source: fromupload
2016-03-16	08:24:03	0	[DEBUG]	Load from computer: relojes-mdf.csv
2016-03-16	08:24:03	0	[DEBUG]	local_csv_file: 
2016-03-16	08:24:03	0	[DEBUG]	urlfile: 
2016-03-16	08:24:03	0	[DEBUG]	ftphost: 
2016-03-16	08:24:03	0	[DEBUG]	ftpport: 
2016-03-16	08:24:03	0	[DEBUG]	ftproot: 
2016-03-16	08:24:03	0	[DEBUG]	ftpfile: 
2016-03-16	08:24:03	0	[DEBUG]	auto_detect_delimiters: Sí
2016-03-16	08:24:03	0	[DEBUG]	field_delimiter: 
2016-03-16	08:24:03	0	[DEBUG]	text_enclosure: 
2016-03-16	08:24:03	0	[DEBUG]	use_file_extension: 
2016-03-16	08:24:03	0	[DEBUG]	im_mac: No
2016-03-16	08:24:03	0	[DEBUG]	use_column_headers: Sí
2016-03-16	08:24:03	0	[DEBUG]	add_extra_fields: No
2016-03-16	08:24:03	0	[DEBUG]	skip_first_line: No
2016-03-16	08:24:03	0	[DEBUG]	collect_debug_info: Sí
2016-03-16	08:24:03	0	[DEBUG]	xml_record_name: 
2016-03-16	08:24:03	0	[DEBUG]	use_system_limits: No
2016-03-16	08:24:03	0	[DEBUG]	max_execution_time: 
2016-03-16	08:24:03	0	[DEBUG]	memory_limit: 
2016-03-16	08:24:03	0	[DEBUG]	post_max_size: 
2016-03-16	08:24:03	0	[DEBUG]	upload_max_filesize: 
2016-03-16	08:24:03	0	[DEBUG]	template_name: Example VirtueMart Prices import
2016-03-16	08:24:03	0	[DEBUG]	id: 80
2016-03-16	08:24:04	1	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	1	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	1	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	1	[DEBUG]	Procesando linea 1
2016-03-16	08:24:04	1	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	1	[DEBUG]	Find product ID based on product SKU
2016-03-16	08:24:04	1	[QUERY]	 SELECT `virtuemart_product_id` FROM `s5epu_virtuemart_products` WHERE `product_sku` = 'MDF301'
2016-03-16	08:24:04	1	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	1	[DEBUG]	Check to see if the vendor ID exists
2016-03-16	08:24:04	1	[QUERY]	 SELECT IF (COUNT(virtuemart_vendor_id) = 0, 1, virtuemart_vendor_id) AS vendor_id FROM s5epu_virtuemart_products WHERE product_sku = 'MDF301'
2016-03-16	08:24:04	1	[DEBUG]	Get the default shopper group name
2016-03-16	08:24:04	1	[QUERY]	 SELECT virtuemart_shoppergroup_id FROM s5epu_virtuemart_shoppergroups WHERE `default` = 1 AND `virtuemart_vendor_id` = 1
2016-03-16	08:24:04	1	[DEBUG]	Obtener la moneda del producto
2016-03-16	08:24:04	1	[DEBUG]	Obtener la moneda del producto
2016-03-16	08:24:04	1	[QUERY]	 SELECT vendor_currency FROM s5epu_virtuemart_vendors WHERE virtuemart_vendor_id = 1
2016-03-16	08:24:04	1	[DEBUG]	Going to find a product_price_id
2016-03-16	08:24:04	1	[DEBUG]	Encontrando un product_price_id
2016-03-16	08:24:04	1	[QUERY]	 SELECT `virtuemart_product_price_id` FROM `s5epu_virtuemart_product_prices` WHERE `virtuemart_product_id` = '348' AND `virtuemart_shoppergroup_id` = '5' AND `product_currency` = '7' AND `price_quantity_start` = '0' AND `price_quantity_end` = '0' AND (`product_price_publish_up` = '0000-00-00 00:00:00' OR `product_price_publish_up` IS NULL) AND (`product_price_publish_down` = '0000-00-00 00:00:00' OR `product_price_publish_down` IS NULL)
2016-03-16	08:24:04	1	[DEBUG]	Product price query
2016-03-16	08:24:04	1	[QUERY]	UPDATE `s5epu_virtuemart_product_prices` SET `virtuemart_product_id`='348',`virtuemart_shoppergroup_id`='5',`product_price`='358',`override`='0',`product_override_price`='358',`product_tax_id`='-1',`product_discount_id`='-1',`product_currency`='7',`product_price_publish_up`='0000-00-00 00:00:00',`product_price_publish_down`='0000-00-00 00:00:00',`price_quantity_start`='0',`price_quantity_end`='0',`created_on`='2014-07-28 12:30:37',`created_by`='598',`modified_on`='2016-03-16 08:24:04',`modified_by`='598',`locked_on`='0000-00-00 00:00:00',`locked_by`='0' WHERE `virtuemart_product_price_id`='193'
2016-03-16	08:24:04	2	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	2	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	2	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	2	[DEBUG]	Procesando linea 2
2016-03-16	08:24:04	2	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	2	[DEBUG]	Find product ID based on product SKU
2016-03-16	08:24:04	2	[QUERY]	 SELECT `virtuemart_product_id` FROM `s5epu_virtuemart_products` WHERE `product_sku` = 'MDF302'
2016-03-16	08:24:04	2	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	2	[DEBUG]	Check to see if the vendor ID exists
2016-03-16	08:24:04	2	[QUERY]	 SELECT IF (COUNT(virtuemart_vendor_id) = 0, 1, virtuemart_vendor_id) AS vendor_id FROM s5epu_virtuemart_products WHERE product_sku = 'MDF302'
2016-03-16	08:24:04	2	[DEBUG]	Get the default shopper group name
2016-03-16	08:24:04	2	[QUERY]	 SELECT virtuemart_shoppergroup_id FROM s5epu_virtuemart_shoppergroups WHERE `default` = 1 AND `virtuemart_vendor_id` = 1
2016-03-16	08:24:04	2	[DEBUG]	Going to find a product_price_id
2016-03-16	08:24:04	2	[DEBUG]	Encontrando un product_price_id
2016-03-16	08:24:04	2	[QUERY]	 SELECT `virtuemart_product_price_id` FROM `s5epu_virtuemart_product_prices` WHERE `virtuemart_product_id` = '349' AND `virtuemart_shoppergroup_id` = '5' AND `product_currency` = '7' AND `price_quantity_start` = '0' AND `price_quantity_end` = '0' AND (`product_price_publish_up` = '0000-00-00 00:00:00' OR `product_price_publish_up` IS NULL) AND (`product_price_publish_down` = '0000-00-00 00:00:00' OR `product_price_publish_down` IS NULL)
2016-03-16	08:24:04	2	[DEBUG]	Product price query
2016-03-16	08:24:04	2	[QUERY]	UPDATE `s5epu_virtuemart_product_prices` SET `virtuemart_product_id`='349',`virtuemart_shoppergroup_id`='5',`product_price`='330',`override`='0',`product_override_price`='330',`product_tax_id`='-1',`product_discount_id`='-1',`product_currency`='7',`product_price_publish_up`='0000-00-00 00:00:00',`product_price_publish_down`='0000-00-00 00:00:00',`price_quantity_start`='0',`price_quantity_end`='0',`created_on`='2014-07-28 12:32:27',`created_by`='598',`modified_on`='2016-03-16 08:24:04',`modified_by`='598',`locked_on`='0000-00-00 00:00:00',`locked_by`='0' WHERE `virtuemart_product_price_id`='194'
2016-03-16	08:24:04	3	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	3	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	3	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	3	[DEBUG]	Procesando linea 3
2016-03-16	08:24:04	3	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	3	[DEBUG]	Find product ID based on product SKU
2016-03-16	08:24:04	3	[QUERY]	 SELECT `virtuemart_product_id` FROM `s5epu_virtuemart_products` WHERE `product_sku` = 'MDF303'
2016-03-16	08:24:04	3	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	3	[DEBUG]	Check to see if the vendor ID exists
2016-03-16	08:24:04	3	[QUERY]	 SELECT IF (COUNT(virtuemart_vendor_id) = 0, 1, virtuemart_vendor_id) AS vendor_id FROM s5epu_virtuemart_products WHERE product_sku = 'MDF303'
2016-03-16	08:24:04	3	[DEBUG]	Get the default shopper group name
2016-03-16	08:24:04	3	[QUERY]	 SELECT virtuemart_shoppergroup_id FROM s5epu_virtuemart_shoppergroups WHERE `default` = 1 AND `virtuemart_vendor_id` = 1
2016-03-16	08:24:04	3	[DEBUG]	Going to find a product_price_id
2016-03-16	08:24:04	3	[DEBUG]	Encontrando un product_price_id
2016-03-16	08:24:04	3	[QUERY]	 SELECT `virtuemart_product_price_id` FROM `s5epu_virtuemart_product_prices` WHERE `virtuemart_product_id` = '420' AND `virtuemart_shoppergroup_id` = '5' AND `product_currency` = '7' AND `price_quantity_start` = '0' AND `price_quantity_end` = '0' AND (`product_price_publish_up` = '0000-00-00 00:00:00' OR `product_price_publish_up` IS NULL) AND (`product_price_publish_down` = '0000-00-00 00:00:00' OR `product_price_publish_down` IS NULL)
2016-03-16	08:24:04	3	[DEBUG]	Product price query
2016-03-16	08:24:04	3	[QUERY]	UPDATE `s5epu_virtuemart_product_prices` SET `virtuemart_product_id`='420',`virtuemart_shoppergroup_id`='5',`product_price`='292',`override`='0',`product_override_price`='292',`product_tax_id`='-1',`product_discount_id`='-1',`product_currency`='7',`product_price_publish_up`='0000-00-00 00:00:00',`product_price_publish_down`='0000-00-00 00:00:00',`price_quantity_start`='0',`price_quantity_end`='0',`created_on`='2014-11-18 22:45:18',`created_by`='598',`modified_on`='2016-03-16 08:24:04',`modified_by`='598',`locked_on`='0000-00-00 00:00:00',`locked_by`='0' WHERE `virtuemart_product_price_id`='266'
2016-03-16	08:24:04	4	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	4	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	4	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	4	[DEBUG]	Procesando linea 4
2016-03-16	08:24:04	4	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	4	[DEBUG]	Find product ID based on product SKU
2016-03-16	08:24:04	4	[QUERY]	 SELECT `virtuemart_product_id` FROM `s5epu_virtuemart_products` WHERE `product_sku` = 'MDF304'
2016-03-16	08:24:04	4	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	4	[DEBUG]	Check to see if the vendor ID exists
2016-03-16	08:24:04	4	[QUERY]	 SELECT IF (COUNT(virtuemart_vendor_id) = 0, 1, virtuemart_vendor_id) AS vendor_id FROM s5epu_virtuemart_products WHERE product_sku = 'MDF304'
2016-03-16	08:24:04	4	[DEBUG]	Get the default shopper group name
2016-03-16	08:24:04	4	[QUERY]	 SELECT virtuemart_shoppergroup_id FROM s5epu_virtuemart_shoppergroups WHERE `default` = 1 AND `virtuemart_vendor_id` = 1
2016-03-16	08:24:04	4	[DEBUG]	Going to find a product_price_id
2016-03-16	08:24:04	4	[DEBUG]	Encontrando un product_price_id
2016-03-16	08:24:04	4	[QUERY]	 SELECT `virtuemart_product_price_id` FROM `s5epu_virtuemart_product_prices` WHERE `virtuemart_product_id` = '421' AND `virtuemart_shoppergroup_id` = '5' AND `product_currency` = '7' AND `price_quantity_start` = '0' AND `price_quantity_end` = '0' AND (`product_price_publish_up` = '0000-00-00 00:00:00' OR `product_price_publish_up` IS NULL) AND (`product_price_publish_down` = '0000-00-00 00:00:00' OR `product_price_publish_down` IS NULL)
2016-03-16	08:24:04	4	[DEBUG]	Product price query
2016-03-16	08:24:04	4	[QUERY]	UPDATE `s5epu_virtuemart_product_prices` SET `virtuemart_product_id`='421',`virtuemart_shoppergroup_id`='5',`product_price`='330',`override`='0',`product_override_price`='330',`product_tax_id`='-1',`product_discount_id`='-1',`product_currency`='7',`product_price_publish_up`='0000-00-00 00:00:00',`product_price_publish_down`='0000-00-00 00:00:00',`price_quantity_start`='0',`price_quantity_end`='0',`created_on`='2014-11-18 22:48:20',`created_by`='598',`modified_on`='2016-03-16 08:24:04',`modified_by`='598',`locked_on`='0000-00-00 00:00:00',`locked_by`='0' WHERE `virtuemart_product_price_id`='267'
2016-03-16	08:24:04	5	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	5	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	5	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	5	[DEBUG]	Procesando linea 5
2016-03-16	08:24:04	5	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	5	[DEBUG]	Find product ID based on product SKU
2016-03-16	08:24:04	5	[QUERY]	 SELECT `virtuemart_product_id` FROM `s5epu_virtuemart_products` WHERE `product_sku` = 'MDF305'
2016-03-16	08:24:04	5	[DEBUG]	Usar valor del campo
2016-03-16	08:24:04	5	[DEBUG]	Check to see if the vendor ID exists
2016-03-16	08:24:04	5	[QUERY]	 SELECT IF (COUNT(virtuemart_vendor_id) = 0, 1, virtuemart_vendor_id) AS vendor_id FROM s5epu_virtuemart_products WHERE product_sku = 'MDF305'
2016-03-16	08:24:04	5	[DEBUG]	Get the default shopper group name
2016-03-16	08:24:04	5	[QUERY]	 SELECT virtuemart_shoppergroup_id FROM s5epu_virtuemart_shoppergroups WHERE `default` = 1 AND `virtuemart_vendor_id` = 1
2016-03-16	08:24:04	5	[DEBUG]	Going to find a product_price_id
2016-03-16	08:24:04	5	[DEBUG]	Encontrando un product_price_id
2016-03-16	08:24:04	5	[QUERY]	 SELECT `virtuemart_product_price_id` FROM `s5epu_virtuemart_product_prices` WHERE `virtuemart_product_id` = '805' AND `virtuemart_shoppergroup_id` = '5' AND `product_currency` = '7' AND `price_quantity_start` = '0' AND `price_quantity_end` = '0' AND (`product_price_publish_up` = '0000-00-00 00:00:00' OR `product_price_publish_up` IS NULL) AND (`product_price_publish_down` = '0000-00-00 00:00:00' OR `product_price_publish_down` IS NULL)
2016-03-16	08:24:04	5	[DEBUG]	Product price query
2016-03-16	08:24:04	5	[QUERY]	UPDATE `s5epu_virtuemart_product_prices` SET `virtuemart_product_id`='805',`virtuemart_shoppergroup_id`='5',`product_price`='319',`override`='0',`product_override_price`='319',`product_tax_id`='-1',`product_discount_id`='-1',`product_currency`='7',`product_price_publish_up`='0000-00-00 00:00:00',`product_price_publish_down`='0000-00-00 00:00:00',`price_quantity_start`='0',`price_quantity_end`='0',`created_on`='2015-08-27 23:01:33',`created_by`='598',`modified_on`='2016-03-16 08:24:04',`modified_by`='598',`locked_on`='0000-00-00 00:00:00',`locked_by`='0' WHERE `virtuemart_product_price_id`='662'
2016-03-16	08:24:04	11	[DEBUG]	Clean up old logs. Found 26 logs and threshold is 25 logs
