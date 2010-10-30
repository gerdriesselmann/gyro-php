<?php


/**
 * Config class for image tools
 */
class ConfigImageTools {
	const IS_TEST_CONTROLLER_ENABLED = 'IMAGETOOLS_IS_TEST_CONTROLLER_ENABLED';
}

Config::set_feature_from_constant(
	ConfigImageTools::IS_TEST_CONTROLLER_ENABLED,
	'APP_IMAGETOOLS_IS_TEST_CONTROLLER_ENABLED',
	APP_TESTMODE
);

