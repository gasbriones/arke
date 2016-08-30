UPDATE `#__csvi_template_settings` SET `settings` = REPLACE(`settings`, 'multiplepricesimport', 'priceimport');
UPDATE `#__csvi_template_settings` SET `settings` = REPLACE(`settings`, 'multiplepricesexport', 'priceexport');
CREATE TABLE IF NOT EXISTS `#__csvi_maps` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NULL DEFAULT NULL,
	`mapfile` VARCHAR(100) NULL DEFAULT NULL,
	`action` VARCHAR(100) NULL DEFAULT NULL,
	`component` VARCHAR(100) NULL DEFAULT NULL,
	`operation` VARCHAR(100) NULL DEFAULT NULL,
	`checked_out` INT(10) NULL DEFAULT NULL,
	`checked_out_time` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) CHARSET=utf8 COMMENT='Holds map configurations';

CREATE TABLE IF NOT EXISTS `#__csvi_mapheaders` (
	`id` INT(10) NOT NULL AUTO_INCREMENT,
	`map_id` INT(10) NOT NULL,
	`csvheader` VARCHAR(100) NOT NULL,
	`templateheader` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`)
) CHARSET=utf8 COMMENT='Holds map field mapping';