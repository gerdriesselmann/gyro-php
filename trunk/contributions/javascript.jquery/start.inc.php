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
 * To dissable automatic inclusion of jquery.js set constant APP-JQUERY_ON_EVERY_PAGE to false, and include
 * 'js/jquery.js' manually where approbiate.  
 * 
 * To define the version of JQuery to use, you must specify a version to use by defining
 * the constant APP_JQUERY_VERSION. Available values are "1.3" for JQuery 1.3.2, "1.4" for JQuery 1.4.4,
 * "1.5" for JQuery 1.5.2., "1.6" for 1.6.4 and "1.7" for 1.7.1 The default is "1.7".
 * 
 * @attention The default version policy changed to "newest", so if you do not define a jquery version,
 *            you will automaticalyy get the latest release
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
	/** @deprecated Use VERSION instead */
	const JQUERY_VERSION = 'JQUERY_VERSION';
	const VERSION = 'JQUERY_VERSION';
	
	/** @deprecated Use ON_EVERY_PAGE instead */	
	const JQUERY_ON_EVERY_PAGE = 'JQUERY_ON_EVERY_PAGE';
	const ON_EVERY_PAGE = 'JQUERY_ON_EVERY_PAGE';
	
	/**
	 * JQuery CDN URL. Either an URL with %version% as placeholder for version OR
	 *
	 * - "google" for the Google CDN
	 * - "ms" for the Microsoft CDN
	 * 
	 * @attention %version% will be allways 3 digits (e.g. 1.6.0). Use %version_min% to force this to 1.6
	 * 
	 * Files will be loaded using https with both Google and Microsoft CDN
	 */
	const CDN = 'JQUERY_CDN';
}

Config::set_feature_from_constant(ConfigJQuery::ON_EVERY_PAGE, 'APP_JQUERY_ON_EVERY_PAGE', true);
Config::set_value_from_constant(ConfigJQuery::CDN, 'APP_JQUERY_CDN', '');

// To be changed on new releases
Config::set_value_from_constant(ConfigJQuery::VERSION, 'APP_JQUERY_VERSION', '1.10');
define('JQUERY_VERSION_1_10', '1.10.2');
define('JQUERY_VERSION_1_9', '1.9.1');
define('JQUERY_VERSION_1_7', '1.7.1');
define('JQUERY_VERSION_1_6', '1.6.4');
define('JQUERY_VERSION_1_5', '1.5.2');

define('JQUERY_VERSION_1_4', '1.4.4');
define('JQUERY_VERSION_1_3', '1.3.2');

