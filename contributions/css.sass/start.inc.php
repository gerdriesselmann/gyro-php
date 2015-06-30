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
}

Config::set_value_from_constant(ConfigSASS::OUTPUT_DIR, 'APP_SASS_OUTPUT_DIR', 'www/css/generated/');


