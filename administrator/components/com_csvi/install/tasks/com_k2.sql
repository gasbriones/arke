DELETE FROM `#__csvi_template_tables` WHERE `component` = 'com_k2';
INSERT IGNORE INTO `#__csvi_template_tables` (`template_type_name`, `template_table`, `component`) VALUES
('itemexport', 'itemexport', 'com_k2'),
('itemexport', 'k2_items', 'com_k2'),
('itemimport', 'itemimport', 'com_k2'),
('itemimport', 'k2_items', 'com_k2'),
('categoryexport', 'categoryexport', 'com_k2'),
('categoryexport', 'k2_categories', 'com_k2'),
('categoryimport', 'categoryimport', 'com_k2'),
('categoryimport', 'k2_categories', 'com_k2');

DELETE FROM `#__csvi_template_types` WHERE `component` = 'com_k2';
INSERT IGNORE INTO `#__csvi_template_types` (`template_type_name`, `template_type`, `component`, `url`, `options`) VALUES
('itemexport', 'export', 'com_k2', 'index.php?option=com_k2&view=items', 'file,fields,item,layout,email,limit'),
('itemimport', 'import', 'com_k2', 'index.php?option=com_k2&view=items', 'item_file,fields,image,limit'),
('categoryexport', 'export', 'com_k2', 'index.php?option=com_k2&view=categories', 'file,fields,category,layout,email,limit'),
('categoryimport', 'import', 'com_k2', 'index.php?option=com_k2&view=categories', 'file,fields,category,category_image,limit');