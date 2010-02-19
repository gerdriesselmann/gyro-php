<?php
/**
 * @defgroup JQuery
 * @ingroup JavaScript
 * 
 * Include JQuery (http://jquery.com/) on all pages 
 *
 * @section Usage
 *
 * On install, the module copies jquery.js to the a folder named "js" below web root. This file will
 * get included on all pages, if JCSSManager WidgetJCSS is used. It also will be compressed by JCSSManager, 
 * if compression is enabled.
 * 
 * To define the version of JQuery to use, you must specify a version to use by defining
 * the constant APP_JQUERY_VERSION. Available values are "1.3" for JQuery 1.3.2, and "1.4" for JQuery 1.4.2. 
 * The default is "1.3".
 * 
 * @code
 * define('APP_JQUERY_VERSION', '1.4');
 * @endcode
 *
 * @section Notes Additional notes
 *
 * JQuery is released under MIT license.
 */

EventSource::Instance()->register(new JavascriptJQueryEventSink());

/**
 * JQuery Config options 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQuery
 */
class ConfigJQuery {
	const JQUERY_VERSION = 'JQUERY_VERSION';
}

Config::set_value_from_constant(ConfigJQuery::JQUERY_VERSION, 'APP_JQUERY_VERSION', '1.3');

