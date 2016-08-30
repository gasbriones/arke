#
#<?php die('Forbidden.'); ?>
#Date: 2016-03-16 01:14:11 UTC
#Software: Joomla Platform 11.4.0 Stable [ Brian Kernighan ] 03-Jan-2012 00:00 GMT

#Fields: date	time	line_nr	action	comment
2016-03-16	01:14:11	0	[DEBUG]	Version CSVI:@version@
2016-03-16	01:14:11	0	[DEBUG]	Version PHP: 5.3.29
2016-03-16	01:14:11	0	[DEBUG]	action: export
2016-03-16	01:14:11	0	[DEBUG]	component: com_virtuemart
2016-03-16	01:14:11	0	[DEBUG]	operation: priceexport
2016-03-16	01:14:11	0	[DEBUG]	exportto: todownload
2016-03-16	01:14:11	0	[DEBUG]	localpath: 
2016-03-16	01:14:11	0	[DEBUG]	ftphost: 
2016-03-16	01:14:11	0	[DEBUG]	ftpport: 
2016-03-16	01:14:11	0	[DEBUG]	ftproot: 
2016-03-16	01:14:11	0	[DEBUG]	ftpfile: 
2016-03-16	01:14:11	0	[DEBUG]	export_filename: FLOREROS MDF
2016-03-16	01:14:11	0	[DEBUG]	export_file: csv
2016-03-16	01:14:11	0	[DEBUG]	field_delimiter: ;
2016-03-16	01:14:11	0	[DEBUG]	text_enclosure: 
2016-03-16	01:14:11	0	[DEBUG]	include_column_headers: Sí
2016-03-16	01:14:11	0	[DEBUG]	signature: Sí
2016-03-16	01:14:11	0	[DEBUG]	export_frontend: No
2016-03-16	01:14:11	0	[DEBUG]	collect_debug_info: Sí
2016-03-16	01:14:11	0	[DEBUG]	publish_state: Sí
2016-03-16	01:14:11	0	[DEBUG]	recordstart: 
2016-03-16	01:14:11	0	[DEBUG]	recordend: 
2016-03-16	01:14:11	0	[DEBUG]	groupby: Sí
2016-03-16	01:14:11	0	[DEBUG]	export_date_format: d/m/Y H:i:s
2016-03-16	01:14:11	0	[DEBUG]	export_price_format_decimal: 2
2016-03-16	01:14:11	0	[DEBUG]	export_price_format_decsep: 
2016-03-16	01:14:11	0	[DEBUG]	export_price_format_thousep: 
2016-03-16	01:14:11	0	[DEBUG]	add_currency_to_price: No
2016-03-16	01:14:11	0	[DEBUG]	language: es-ES
2016-03-16	01:14:11	0	[DEBUG]	header: 
2016-03-16	01:14:11	0	[DEBUG]	body: 
2016-03-16	01:14:11	0	[DEBUG]	footer: 
2016-03-16	01:14:11	0	[DEBUG]	export_email_subject: 
2016-03-16	01:14:11	0	[DEBUG]	export_email_body: 
2016-03-16	01:14:11	0	[DEBUG]	use_system_limits: No
2016-03-16	01:14:11	0	[DEBUG]	max_execution_time: 
2016-03-16	01:14:11	0	[DEBUG]	memory_limit: 
2016-03-16	01:14:11	0	[DEBUG]	id: 86
2016-03-16	01:14:11	0	[DEBUG]	Campo: product_sku
2016-03-16	01:14:11	0	[DEBUG]	Campo: product_name
2016-03-16	01:14:11	0	[DEBUG]	Campo: product_override_price
2016-03-16	01:14:11	0	[DEBUG]	Campo: product_price
2016-03-16	01:14:11	0	[DEBUG]	Export query
2016-03-16	01:14:11	0	[QUERY]	 SELECT `s5epu_virtuemart_product_prices`.`virtuemart_product_id`, `s5epu_virtuemart_products`.`virtuemart_product_id`, `product_override_price`, `product_price` FROM `s5epu_virtuemart_product_prices` LEFT JOIN `s5epu_virtuemart_products` ON `s5epu_virtuemart_product_prices`.`virtuemart_product_id` = `s5epu_virtuemart_products`.`virtuemart_product_id` LEFT JOIN `s5epu_virtuemart_shoppergroups` ON `s5epu_virtuemart_product_prices`.`virtuemart_shoppergroup_id` = `s5epu_virtuemart_shoppergroups`.`virtuemart_shoppergroup_id` LEFT JOIN `s5epu_virtuemart_currencies` ON `s5epu_virtuemart_product_prices`.`product_currency` = `s5epu_virtuemart_currencies`.`virtuemart_currency_id` WHERE s5epu_virtuemart_products.published = 1 GROUP BY `product_override_price`,`product_price`
2016-03-16	01:14:11	93	[DEBUG]	Clean up old logs. Found 26 logs and threshold is 25 logs
