DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_users';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('userexport', 'userexport', 'com_users'),
('userexport', 'users', 'com_users'),
('userimport', 'userimport', 'com_users'),
('userimport', 'users', 'com_users');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_users';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('userexport', 'export', 'com_users', '', 'file,fields,users,layout,email,limit'),
('userimport', 'import', 'com_users', '', 'content_file,fields,limit');