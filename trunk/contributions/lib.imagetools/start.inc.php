<?php

/**
 * @defgroup ImageTools
 * @ingroup Libs
 *
 * Wraps GD and (sometime) other image manipulation libraries in a custom interface target on higher level
 * image manipulation tasks, like fitting an image into a given space.
 */


/**
 * Config class for image tools
 *
 * @ingroup ImageTools
 */
class ConfigImageTools {
	/**
	 * Enable the test controller
	 */
	const IS_TEST_CONTROLLER_ENABLED = 'IMAGETOOLS_IS_TEST_CONTROLLER_ENABLED';
}

Config::set_feature_from_constant(
	ConfigImageTools::IS_TEST_CONTROLLER_ENABLED,
	'APP_IMAGETOOLS_IS_TEST_CONTROLLER_ENABLED',
	APP_TESTMODE
);

