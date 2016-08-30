ALTER TABLE `#__csvi_template_types` ADD COLUMN `published` TINYINT(1) NOT NULL DEFAULT '1' AFTER `options`;
ALTER TABLE `#__csvi_template_types` ADD COLUMN `ordering` INT(11) NULL AFTER `published`;