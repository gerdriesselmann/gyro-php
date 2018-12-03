<?php
/**
 * @defgroup SASS
 * @ingroup CSS
 * 
 * Support for SASS
 *
 * @see http://sass-lang.com/
 */

/**
 * Config for CSS SASS module
 *
 * @author Gerd Riesselmann
 * @ingroup SASS

 */
class ConfigSASS {
	/**
	 * Output path relative to application base dir (/app), Default is
	 * "www/css/generated"
	 */
	const OUTPUT_DIR = 'SASS_OUTPUT_DIR';

	/**
	 * If generator should keep directory structure (default) or not.
	 *
	 * If true file app/view/sass/lib/elements.sass would be compiled to
	 *    www/css/generated/lib/elements.css
	 *
	 * If false the target would be
	 *    www/css/generated/elements.css
	 */
	const KEEP_DIRECTORY_STRUCTURE = 'SASS_KEEP_DIRECTORY_STRUCTURE';

	/**
	 * Output format.
	 *
	 * If set to 'default' resolves to 'nested' in test mode and 'compressed' in live mode
	 */
	const OUTPUT_FORMAT = 'SASS_OUTPUT_FORMAT';


	/**
	 * IF set to true, ignores files that start with an uderscore
	 */
	const IGNORE_PRIVATE_FILES = 'SASS_IGNORE_PRIVATE_FILES';

	/**
	 * Set if node sass (located at {project dir}/node_modules/.bin) should be used or
	 * globally install sass executable (like /usr/binb/sass)
	 */
	const USE_LOCAL_NODE_SASS = 'SASS_USE_LOCAL_NODE_SASS';
}

Config::set_value_from_constant(ConfigSASS::OUTPUT_DIR, 'APP_SASS_OUTPUT_DIR', 'www/css/generated/');
Config::set_value_from_constant(ConfigSASS::OUTPUT_FORMAT, 'APP_SASS_OUTPUT_FORMAT', 'default');
Config::set_feature_from_constant(ConfigSASS::KEEP_DIRECTORY_STRUCTURE, 'APP_SASS_KEEP_DIRECTORY_STRUCTURE', true);
Config::set_feature_from_constant(ConfigSASS::IGNORE_PRIVATE_FILES, 'APP_SASS_IGNORE_PRIVATE_FILES', false);
Config::set_feature_from_constant(ConfigSASS::USE_LOCAL_NODE_SASS, 'APP_SASS_USE_LOCAL_NODE_SASS', false);


