/* Custom Filters custom fields import */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('skip', 'skip', 'customfieldsimport', 'com_customfilters'),
('custom_title', 'custom_title', 'customfieldsimport', 'com_customfilters'),
('display_type', 'display_type', 'customfieldsimport', 'com_customfilters'),
('smart_search', 'smart_search', 'customfieldsimport', 'com_customfilters'),
('expanded', 'expanded', 'customfieldsimport', 'com_customfilters'),
('scrollbar_after', 'scrollbar_after', 'customfieldsimport', 'com_customfilters'),

/* Custom Filters custom fields export */
('custom', 'custom', 'customfieldsexport', 'com_customfilters'),
('custom_title', 'custom_title', 'customfieldsexport', 'com_customfilters'),
('display_type', 'display_type', 'customfieldsexport', 'com_customfilters'),
('smart_search', 'smart_search', 'customfieldsexport', 'com_customfilters'),
('expanded', 'expanded', 'customfieldsexport', 'com_customfilters'),
('scrollbar_after', 'scrollbar_after', 'customfieldsexport', 'com_customfilters');