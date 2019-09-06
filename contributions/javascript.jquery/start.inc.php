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
 * To dissable automatic inclusion of jquery.js set constant APP_JQUERY_ON_EVERY_PAGE to false, and include
 * 'js/jquery.js' manually where approbiate.  
 * 
 * To define the version of JQuery to use, you must specify a version to use by defining
 * the constant APP_JQUERY_VERSION. Available values are "1.3" to "1.11" for the lates jquery version for each branch.
 * 
 * @attention The default version policy changed to "newest", so if you do not define a jquery version,
 *            you will automatically get the latest release
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
	 * - "jquery" for the JQuery CDN
	 * 
	 * @attention %version% will be always 3 digits (e.g. 1.6.0). Use %version_min% to force this to 1.6
	 * 
	 * Files will be loaded using https always
	 */
	const CDN = 'JQUERY_CDN';
}

Config::set_feature_from_constant(ConfigJQuery::ON_EVERY_PAGE, 'APP_JQUERY_ON_EVERY_PAGE', true);
Config::set_value_from_constant(ConfigJQuery::CDN, 'APP_JQUERY_CDN', '');

// To be changed on new releases
Config::set_value_from_constant(ConfigJQuery::VERSION, 'APP_JQUERY_VERSION', '1.11');

// Deprecated, kept for backward compatability. Version managment is now in jQuery class
define('JQUERY_VERSION_1_11', '1.11.3');
define('JQUERY_SRI_1_11', 'sha384-6ePHh72Rl3hKio4HiJ841psfsRJveeS+aLoaEf3BWfS+gTF0XdAqku2ka8VddikM');
define('JQUERY_VERSION_1_10', '1.10.2');
define('JQUERY_VERSION_1_9', '1.9.1');
define('JQUERY_VERSION_1_7', '1.7.1');
define('JQUERY_VERSION_1_6', '1.6.4');
define('JQUERY_VERSION_1_5', '1.5.2');

define('JQUERY_VERSION_1_4', '1.4.4');
define('JQUERY_VERSION_1_3', '1.3.2');

