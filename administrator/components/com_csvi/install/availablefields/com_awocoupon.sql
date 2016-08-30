/* AwoCoupon Coupon export */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('custom', 'custom', 'couponexport', 'com_awocoupon'),
('category_path', 'category_path', 'couponexport', 'com_awocoupon'),
('product_sku', 'product_sku', 'couponexport', 'com_awocoupon'),
('manufacturer_name', 'manufacturer_name', 'couponexport', 'com_awocoupon'),
('username', 'username', 'couponexport', 'com_awocoupon'),
('shoppergroup', 'shoppergroup', 'couponexport', 'com_awocoupon'),

/* AwoCoupon Coupon import */
('skip', 'skip', 'couponimport', 'com_awocoupon'),
('category_path', 'category_path', 'couponimport', 'com_awocoupon'),
('product_sku', 'product_sku', 'couponimport', 'com_awocoupon'),
('manufacturer_name', 'manufacturer_name', 'couponimport', 'com_awocoupon'),
('username', 'username', 'couponimport', 'com_awocoupon'),
('shoppergroup', 'shoppergroup', 'couponimport', 'com_awocoupon'),

/* AwoCoupon Gift certificate export */
('custom', 'custom', 'giftcertificateexport', 'com_awocoupon'),
('product_sku', 'product_sku', 'giftcertificateexport', 'com_awocoupon'),
('coupon_code', 'coupon_code', 'giftcertificateexport', 'com_awocoupon'),
('profile_image', 'profile_image', 'giftcertificateexport', 'com_awocoupon'),
('code', 'code', 'giftcertificateexport', 'com_awocoupon'),
('status', 'status', 'giftcertificateexport', 'com_awocoupon'),
('note', 'note', 'giftcertificateexport', 'com_awocoupon'),

/* AwoCoupon Gift Certificate import */
('skip', 'skip', 'giftcertificateimport', 'com_awocoupon'),
('product_sku', 'product_sku', 'giftcertificateimport', 'com_awocoupon'),
('coupon_code', 'coupon_code', 'giftcertificateimport', 'com_awocoupon'),
('profile_image', 'profile_image', 'giftcertificateimport', 'com_awocoupon'),
('code', 'code', 'giftcertificateimport', 'com_awocoupon'),
('status', 'status', 'giftcertificateimport', 'com_awocoupon'),
('note', 'note', 'giftcertificateimport', 'com_awocoupon'),

/* AwoCoupon Gift Certificate code import */
('skip', 'skip', 'giftcertificatecodeimport', 'com_awocoupon'),
('product_sku', 'product_sku', 'giftcertificatecodeimport', 'com_awocoupon');