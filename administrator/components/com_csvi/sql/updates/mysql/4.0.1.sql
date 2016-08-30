ALTER TABLE `#__csvi_replacements` ALTER `findtext` DROP DEFAULT,	ALTER `replacetext` DROP DEFAULT;
ALTER TABLE `#__csvi_replacements` CHANGE `findtext` `findtext` TEXT NOT NULL AFTER `name`;
ALTER TABLE `#__csvi_replacements` CHANGE `replacetext` `replacetext` TEXT NOT NULL AFTER `findtext`;
ALTER TABLE `#__csvi_replacements` ADD COLUMN `multivalue` ENUM('0','1') NOT NULL AFTER `replacetext`;