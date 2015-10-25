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

}

Config::set_value_from_constant(ConfigSASS::OUTPUT_DIR, 'APP_SASS_OUTPUT_DIR', 'www/css/generated/');
Config::set_feature_from_constant(ConfigSASS::KEEP_DIRECTORY_STRUCTURE, 'APP_SASS_KEEP_DIRECTORY_STRUCTURE', true);


