DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_categories';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('categoryexport', 'categoryexport', 'com_categories'),
('categoryexport', 'categories', 'com_categories'),
('categoryimport', 'categoryimport', 'com_categories'),
('categoryimport', 'categories', 'com_categories');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_categories';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('categoryexport', 'export', 'com_categories', '', 'file,fields,layout,email,limit'),
('categoryimport', 'import', 'com_categories', '', 'file,fields,limit');