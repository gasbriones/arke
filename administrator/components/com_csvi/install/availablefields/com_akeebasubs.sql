/* Akeeba Subscriptions subscription export */
INSERT IGNORE INTO `#__csvi_available_fields` (`csvi_name`, `component_name`, `component_table`, `component`) VALUES
('custom', 'custom', 'subscriptionexport', 'com_akeebasubs'),
('name', 'name', 'subscriptionexport', 'com_akeebasubs'),
('username', 'username', 'subscriptionexport', 'com_akeebasubs'),
('email', 'email', 'subscriptionexport', 'com_akeebasubs'),
('password', 'password', 'subscriptionexport', 'com_akeebasubs'),

/* Akeeba Subscriptions affiliate export */
('custom', 'custom', 'affiliateexport', 'com_akeebasubs'),
('money_owed', 'money_owed', 'affiliateexport', 'com_akeebasubs'),
('money_paid', 'money_paid', 'affiliateexport', 'com_akeebasubs'),
('total_commission', 'total_commission', 'affiliateexport', 'com_akeebasubs'),

/* Akeeba Subscriptions coupon export */
('custom', 'custom', 'couponexport', 'com_akeebasubs'),
('name', 'name', 'couponexport', 'com_akeebasubs'),
('username', 'username', 'couponexport', 'com_akeebasubs'),
('email', 'email', 'couponexport', 'com_akeebasubs'),
('password', 'password', 'couponexport', 'com_akeebasubs'),

/* Akeeba Subscriptions coupon import */
('skip', 'skip', 'couponimport', 'com_akeebasubs'),
('username', 'username', 'couponimport', 'com_akeebasubs'),
('subscription_title', 'subscription_title', 'couponimport', 'com_akeebasubs'), /* Comma separated value */

/* Akeeba Subscriptions subscription import */
('skip', 'skip', 'subscriptionimport', 'com_akeebasubs'),
('subscription_delete', 'subscription_delete', 'subscriptionimport', 'com_akeebasubs'),
('subscription_title', 'subscription_title', 'subscriptionimport', 'com_akeebasubs'),
('name', 'name', 'subscriptionimport', 'com_akeebasubs'),
('username', 'username', 'subscriptionimport', 'com_akeebasubs'),
('email', 'email', 'subscriptionimport', 'com_akeebasubs'),
('password', 'password', 'subscriptionimport', 'com_akeebasubs'),

/* Akeeba Subscriptions affiliate import */
('skip', 'skip', 'affiliateimport', 'com_akeebasubs'),
('affiliate_delete', 'affiliate_delete', 'affiliateimport', 'com_akeebasubs'),
('username', 'username', 'affiliateimport', 'com_akeebasubs'),
('amount', 'amount', 'affiliateimport', 'com_akeebasubs'),

/* Akeeba Subscriptions user import */
('skip', 'skip', 'userimport', 'com_akeebasubs'),
('combine', 'combine', 'userimport', 'com_akeebasubs'),
('usergroup_name', 'usergroup_name', 'userimport', 'com_akeebasubs'),
('password_crypt', 'password_crypt', 'userimport', 'com_akeebasubs');

/* Custom fields */
INSERT IGNORE INTO `#__csvi_available_fields` (csvi_name, component_name, component_table, component)
	(SELECT TRIM(slug), TRIM(slug), 'userimport', 'com_akeebasubs' FROM `#__akeebasubs_customfields`);

INSERT IGNORE INTO `#__csvi_available_fields` (csvi_name, component_name, component_table, component)
	(SELECT TRIM(slug), TRIM(slug), 'userexport', 'com_akeebasubs' FROM `#__akeebasubs_customfields`);