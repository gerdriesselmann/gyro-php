<?php
/**
 * @defgroup Bootstrap3
 * @ingroup CSS
 * 
 * Basic installation of Bootstrap CSS and JS framework.
 * 
 * @see http://getbootstrap.com
 * 
 * @section Usage Usage
 * 
 * The module copies Bootstrap css, js and fonts to the app/www directory. These files are overwritten
 * on each system update
 */

EventSource::Instance()->register(new CSSBootstrap3EventSink());

/**
 * Config for CSS Bootstrap3 module
 * 
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class ConfigBootstrap3 {
	const VERSION = 'BOOTSTRAP3_VERSION';

	const ON_EVERY_PAGE = 'BOOTSTRAP3_ON_EVERY_PAGE';
}

Config::set_value_from_constant(ConfigBootstrap3::VERSION, 'APP_BOOTSTRAP3_VERSION', '3.0');
Config::set_feature_from_constant(ConfigBootstrap3::ON_EVERY_PAGE, 'APP_BOOTSTRAP3_ON_EVERY_PAGE', true);


