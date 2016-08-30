#
#<?php die('Forbidden.'); ?>
#Date: 2016-03-16 01:24:32 UTC
#Software: Joomla Platform 11.4.0 Stable [ Brian Kernighan ] 03-Jan-2012 00:00 GMT

#Fields: date	time	line_nr	action	comment
2016-03-16	01:24:32	0	[DEBUG]	Version CSVI:@version@
2016-03-16	01:24:32	0	[DEBUG]	Version PHP: 5.3.29
2016-03-16	01:24:32	0	[DEBUG]	action: export
2016-03-16	01:24:32	0	[DEBUG]	component: com_virtuemart
2016-03-16	01:24:32	0	[DEBUG]	operation: productexport
2016-03-16	01:24:32	0	[DEBUG]	exportto: todownload
2016-03-16	01:24:32	0	[DEBUG]	localpath: 
2016-03-16	01:24:32	0	[DEBUG]	ftphost: 
2016-03-16	01:24:32	0	[DEBUG]	ftpport: 
2016-03-16	01:24:32	0	[DEBUG]	ftproot: 
2016-03-16	01:24:32	0	[DEBUG]	ftpfile: 
2016-03-16	01:24:32	0	[DEBUG]	export_filename: EXPORTACION PARA CAMBIO DE PRECIO.csv
2016-03-16	01:24:32	0	[DEBUG]	export_file: csv
2016-03-16	01:24:32	0	[DEBUG]	field_delimiter: ;
2016-03-16	01:24:32	0	[DEBUG]	text_enclosure: 
2016-03-16	01:24:32	0	[DEBUG]	include_column_headers: Sí
2016-03-16	01:24:32	0	[DEBUG]	signature: Sí
2016-03-16	01:24:32	0	[DEBUG]	export_frontend: No
2016-03-16	01:24:32	0	[DEBUG]	collect_debug_info: Sí
2016-03-16	01:24:32	0	[DEBUG]	publish_state: 
2016-03-16	01:24:32	0	[DEBUG]	recordstart: 
2016-03-16	01:24:32	0	[DEBUG]	recordend: 
2016-03-16	01:24:32	0	[DEBUG]	groupby: Sí
2016-03-16	01:24:32	0	[DEBUG]	export_date_format: d/m/Y H:i:s
2016-03-16	01:24:32	0	[DEBUG]	export_price_format_decimal: 2
2016-03-16	01:24:32	0	[DEBUG]	export_price_format_decsep: 
2016-03-16	01:24:32	0	[DEBUG]	export_price_format_thousep: .
2016-03-16	01:24:32	0	[DEBUG]	add_currency_to_price: No
2016-03-16	01:24:32	0	[DEBUG]	language: es-ES
2016-03-16	01:24:32	0	[DEBUG]	category_separator: /
2016-03-16	01:24:32	0	[DEBUG]	exportsef: Sí
2016-03-16	01:24:32	0	[DEBUG]	producturl_suffix: 
2016-03-16	01:24:32	0	[DEBUG]	vm_itemid: 
2016-03-16	01:24:32	0	[DEBUG]	picture_limit: Sí
2016-03-16	01:24:32	0	[DEBUG]	featured: No
2016-03-16	01:24:32	0	[DEBUG]	publish_state_categories: 
2016-03-16	01:24:32	0	[DEBUG]	incl_subcategory: No
2016-03-16	01:24:32	0	[DEBUG]	parent_only: No
2016-03-16	01:24:32	0	[DEBUG]	child_only: No
2016-03-16	01:24:32	0	[DEBUG]	productskufilter: 
2016-03-16	01:24:32	0	[DEBUG]	stocklevelstart: 
2016-03-16	01:24:32	0	[DEBUG]	stocklevelend: 
2016-03-16	01:24:32	0	[DEBUG]	shopper_group_price: none
2016-03-16	01:24:32	0	[DEBUG]	priceoperator: gt
2016-03-16	01:24:32	0	[DEBUG]	pricefrom: 
2016-03-16	01:24:32	0	[DEBUG]	priceto: 
2016-03-16	01:24:32	0	[DEBUG]	price_quantity_start: 
2016-03-16	01:24:32	0	[DEBUG]	price_quantity_end: 
2016-03-16	01:24:32	0	[DEBUG]	targetcurrency: 
2016-03-16	01:24:32	0	[DEBUG]	header: 
2016-03-16	01:24:32	0	[DEBUG]	body: 
2016-03-16	01:24:32	0	[DEBUG]	footer: 
2016-03-16	01:24:32	0	[DEBUG]	export_email_subject: 
2016-03-16	01:24:32	0	[DEBUG]	export_email_body: 
2016-03-16	01:24:32	0	[DEBUG]	use_system_limits: No
2016-03-16	01:24:32	0	[DEBUG]	max_execution_time: 
2016-03-16	01:24:32	0	[DEBUG]	memory_limit: 
2016-03-16	01:24:32	0	[DEBUG]	id: 84
2016-03-16	01:24:32	0	[DEBUG]	Campo: product_sku
2016-03-16	01:24:32	0	[DEBUG]	Campo: product_price
2016-03-16	01:24:32	0	[DEBUG]	Campo: product_override_price
2016-03-16	01:24:32	0	[DEBUG]	Campo: product_name
2016-03-16	01:24:32	0	[DEBUG]	Export query
2016-03-16	01:24:32	0	[QUERY]	SELECT p.virtuemart_product_id        FROM s5epu_virtuemart_products p        LEFT JOIN s5epu_virtuemart_product_categories x        ON p.virtuemart_product_id = x.virtuemart_product_id        WHERE x.virtuemart_category_id IN ('55')
2016-03-16	01:24:32	0	[DEBUG]	Export query
2016-03-16	01:24:32	0	[QUERY]	SELECT p.virtuemart_product_id         FROM s5epu_virtuemart_products p         WHERE p.product_parent_id IN ('253','424','425','426')
2016-03-16	01:24:32	0	[DEBUG]	Export query
2016-03-16	01:24:32	0	[QUERY]	 SELECT `product_sku`, `s5epu_virtuemart_product_prices`.`product_price`, `s5epu_virtuemart_currencies`.`currency_code_3`, `product_override_price`, `s5epu_virtuemart_products`.`virtuemart_product_id` FROM s5epu_virtuemart_products LEFT JOIN s5epu_virtuemart_product_prices ON s5epu_virtuemart_products.virtuemart_product_id = s5epu_virtuemart_product_prices.virtuemart_product_id LEFT JOIN s5epu_virtuemart_product_manufacturers ON s5epu_virtuemart_products.virtuemart_product_id = s5epu_virtuemart_product_manufacturers.virtuemart_product_id LEFT JOIN s5epu_virtuemart_manufacturers ON s5epu_virtuemart_product_manufacturers.virtuemart_manufacturer_id = s5epu_virtuemart_manufacturers.virtuemart_manufacturer_id LEFT JOIN s5epu_virtuemart_product_categories ON s5epu_virtuemart_products.virtuemart_product_id = s5epu_virtuemart_product_categories.virtuemart_product_id LEFT JOIN s5epu_virtuemart_categories ON s5epu_virtuemart_product_categories.virtuemart_category_id = s5epu_virtuemart_categories.virtuemart_category_id LEFT JOIN s5epu_virtuemart_currencies ON s5epu_virtuemart_currencies.virtuemart_currency_id = s5epu_virtuemart_product_prices.product_currency LEFT JOIN s5epu_virtuemart_product_shoppergroups ON s5epu_virtuemart_product_shoppergroups.virtuemart_product_id = s5epu_virtuemart_products.virtuemart_product_id WHERE s5epu_virtuemart_products.virtuemart_product_id IN ('253','424','425','426') GROUP BY `product_sku`,`product_price`,`product_override_price`
2016-03-16	01:24:32	4	[DEBUG]	Clean up old logs. Found 26 logs and threshold is 25 logs
