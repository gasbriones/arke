DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_content';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('contentexport', 'contentexport', 'com_content'),
('contentexport', 'content', 'com_content'),
('contentimport', 'contentimport', 'com_content'),
('contentimport', 'content', 'com_content');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_content';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('contentexport', 'export', 'com_content', '', 'file,fields,content,layout,email,limit'),
('contentimport', 'import', 'com_content', '', 'content_file,fields,limit');