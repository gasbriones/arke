/* Joomla content import */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('skip', 'skip', 'contentimport', 'com_content'),
('combine', 'combine', 'contentimport', 'com_content'),
('category_path', 'category_path', 'contentimport', 'com_content'),

/* Joomla content export */
('custom', 'custom', 'contentexport', 'com_content'),
('category_path', 'category_path', 'contentexport', 'com_content'),
('article_url', 'article_url', 'contentexport', 'com_content');