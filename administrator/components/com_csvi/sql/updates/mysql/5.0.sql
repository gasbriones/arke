ALTER TABLE `#__csvi_template_fields` ADD COLUMN `file_field_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name for the field from the file' AFTER `field_name`;
ALTER TABLE `#__csvi_template_fields` ADD COLUMN `template_field_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Name for the field from the template' AFTER `file_field_name`;
ALTER TABLE `#__csvi_template_fields` ADD COLUMN `cdata` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT 'Use the CDATA tag' AFTER `sort`;
CREATE TABLE IF NOT EXISTS `#__csvi_template_fields_combine` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the cross reference',
  `field_id` VARCHAR(255) NOT NULL COMMENT 'ID of the field',
  `combine_id` VARCHAR(255) NOT NULL COMMENT 'ID of the combine rule',
  PRIMARY KEY (`id`)
) COMMENT='Holds the combine cross reference for a CSVI template field';