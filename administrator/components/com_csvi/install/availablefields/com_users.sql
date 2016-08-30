/* Joomla user import */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('skip', 'skip', 'userimport', 'com_users'),
('combine', 'combine', 'usertimport', 'com_users'),
('password_crypt', 'password_crypt', 'userimport', 'com_users'),
('usergroup_name', 'usergroup_name', 'userimport', 'com_users'),
('fullname', 'fullname', 'userimport', 'com_users'),

/* Joomla user export */
('custom', 'custom', 'userexport', 'com_users'),
('usergroup_name', 'usergroup_name', 'userexport', 'com_users'),
('fullname', 'fullname', 'userexport', 'com_users')