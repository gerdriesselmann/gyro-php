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
 * 
 * By default a 213-template is installed. Use the YAML builder to create your own template
 * and CSS stubs: http://builder.yaml.de/
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
}

Config::set_value_from_constant(ConfigBootstrap3::VERSION, 'APP_BOOTSTRAP3_VERSION', '3.0');


