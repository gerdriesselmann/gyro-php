<?php
/**
 * Notifications options
 *
 * @author Gerd Riesselmann
 */
class ConfigUserNotifications {
	/**
	 * If this module should add clicktrack links
	 */
	const ENABLE_CLICK_TRACKING = 'USER_NOTIFICATIONS_ENABLE_CLICK_TRACKING';

	/**
	 * Offer FEED delivery method
	 */
	const ENABLE_DELIVERY_FEED = 'USER_NOTIFICATIONS_ENABLE_DELIVERY_FEED';

	/**
	 * Offer DIGEST delivery method
	 */
	const ENABLE_DELIVERY_DIGEST = 'USER_NOTIFICATIONS_ENABLE_DELIVERY_DIGEST';
}


Config::set_feature_from_constant(
	ConfigUserNotifications::ENABLE_CLICK_TRACKING,
	'APP_USER_NOTIFICATIONS_ENABLE_CLICK_TRACKING',
	true
);
Config::set_feature_from_constant(
	ConfigUserNotifications::ENABLE_DELIVERY_FEED,
	'APP_USER_NOTIFICATIONS_ENABLE_DELIVERY_FEED',
	true
);
Config::set_feature_from_constant(
	ConfigUserNotifications::ENABLE_DELIVERY_DIGEST,
	'APP_USER_NOTIFICATIONS_ENABLE_DELIVERY_DIGEST',
	true
);
