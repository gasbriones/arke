/* EZ Realty property export */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('custom', 'custom', 'propertyexport', 'com_ezrealty'),
('category', 'category', 'propertyexport', 'com_ezrealty'),
('city', 'city', 'propertyexport', 'com_ezrealty'),
('state', 'state', 'propertyexport', 'com_ezrealty'),
('country', 'country', 'propertyexport', 'com_ezrealty'),
('fname', 'fname', 'propertyexport', 'com_ezrealty'),
('file_title', 'file_title', 'propertyexport', 'com_ezrealty'),
('file_description', 'file_description', 'propertyexport', 'com_ezrealty'),
('file_ordering', 'file_ordering', 'propertyexport', 'com_ezrealty'),
('picture_url', 'picture_url', 'propertyexport', 'com_ezrealty'),
('picture_url_thumb', 'picture_url_thumb', 'propertyexport', 'com_ezrealty'),

/* EZ Realty property import */
('skip', 'skip', 'propertyimport', 'com_ezrealty'),
('combine', 'combine', 'propertyimport', 'com_ezrealty'),
('category', 'category', 'propertyimport', 'com_ezrealty'),
('transaction_type', 'transaction_type', 'propertyimport', 'com_ezrealty'),
('market_status', 'market_status', 'propertyimport', 'com_ezrealty'),
('agent', 'agent', 'propertyimport', 'com_ezrealty'),
('city', 'city', 'propertyimport', 'com_ezrealty'),
('state', 'state', 'propertyimport', 'com_ezrealty'),
('fname', 'fname', 'propertyimport', 'com_ezrealty'),
('file_title', 'file_title', 'propertyimport', 'com_ezrealty'),
('file_description', 'file_description', 'propertyimport', 'com_ezrealty'),
('file_ordering', 'file_ordering', 'propertyimport', 'com_ezrealty'),

/* EZ Realty category export */
('custom', 'custom', 'categoryexport', 'com_ezrealty'),

/* EZ Realty category import */
('skip', 'skip', 'categoryimport', 'com_ezrealty'),

/* EZ Realty images export */
('custom', 'custom', 'imageexport', 'com_ezrealty'),

/* EZ Realty category import */
('skip', 'skip', 'imageimport', 'com_ezrealty'),
('image_delete', 'image_delete', 'imageimport', 'com_ezrealty');