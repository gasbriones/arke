/* Joomla category import */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('skip', 'skip', 'categoryimport', 'com_categories'),
('combine', 'combine', 'categoryimport', 'com_categories'),
('category_path', 'category_path', 'categoryimport', 'com_categories'),

/* Joomla category export */
('custom', 'custom', 'categoryexport', 'com_categories'),
('category_path', 'category_path', 'categoryexport', 'com_categories');