/* K2 item export */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('custom', 'custom', 'itemexport', 'com_k2'),
('category_path', 'category_path', 'itemexport', 'com_k2'),
('image', 'image', 'itemexport', 'com_k2'),

/* K2 item import */
('skip', 'skip', 'itemimport', 'com_k2'),
('combine', 'combine', 'itemimport', 'com_k2'),
('image', 'image', 'itemimport', 'com_k2'),
('category_path', 'category_path', 'itemimport', 'com_k2'),

/* K2 category export */
('custom', 'custom', 'categoryexport', 'com_k2'),
('category_path', 'category_path', 'categoryexport', 'com_k2'),
('parent_category_path', 'parent_category_path', 'categoryexport', 'com_k2'),

/* K2 category import */
('skip', 'skip', 'categoryimport', 'com_k2'),
('combine', 'combine', 'categoryimport', 'com_k2'),
('category_path', 'category_path', 'categoryimport', 'com_k2'),
('category_delete', 'category_delete', 'categoryimport', 'com_k2');