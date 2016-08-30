CREATE TABLE IF NOT EXISTS `#__csvi_related_categories` (
	`product_sku` varchar(64) NOT NULL,
	`related_cat` text NOT NULL
) CHARSET=utf8 COMMENT='Related categories import for CSVI';