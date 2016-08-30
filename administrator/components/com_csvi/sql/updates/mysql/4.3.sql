ALTER TABLE `#__csvi_template_settings` ADD COLUMN `process` ENUM('import','export') NOT NULL DEFAULT 'import' COMMENT 'The type of template' AFTER `settings`;
CREATE TABLE IF NOT EXISTS `#__csvi_template_fields` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the template field',
  `template_id` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The template ID',
  `ordering` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The order of the field',
  `field_name` VARCHAR(255) NOT NULL COMMENT 'Name for the field',
  `column_header` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Header for the column',
  `default_value` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Default value for the field',
  `process` ENUM('0','1') NOT NULL DEFAULT '1' COMMENT 'Process the field',
  `combine` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'Combine the field',
  `sort` ENUM('0','1') NOT NULL DEFAULT '0' COMMENT 'Sort the field',
  PRIMARY KEY (`id`)
) COMMENT='Holds the fields for a CSVI template';
CREATE TABLE IF NOT EXISTS `#__csvi_template_fields_replacement` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Unique ID for the cross reference',
  `field_id` VARCHAR(255) NOT NULL COMMENT 'ID of the field',
  `replace_id` VARCHAR(255) NOT NULL COMMENT 'ID of the replacement rule',
  PRIMARY KEY (`id`)
) COMMENT='Holds the replacement cross reference for a CSVI template field';
