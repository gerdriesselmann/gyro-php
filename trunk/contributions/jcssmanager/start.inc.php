<?php
/**
 * Config Options for JCSSManager 
 *
 * @author Gerd Riesselmann
 * @ingroup JCSSManager
 */
class ConfigJCSSManager {
	const CSS_DIR = 'JCSS_CSS_DIR';
	const JS_DIR = 'JCSS_JS_DIR';
	
	/**
	 * CSS Compression method. Only 'yui' and 'csstidy' are supported
	 */
	const CSS_COMPRESSOR = 'JCSS_CSS_COMPRESSOR';
	/**
	 * Javascript Compression method. Possible values are 'yui' or 'closure'
	 */
	const JS_COMPRESSOR = 'JCSS_JS_COMPRESSOR';
	
	/**
	 * Set to FALSE to disable usage compressed scripts 
	 */
	const USE_COMPRESSED = 'JCSS_USE_COMPRESSED';
	/**
	 * Set to FALSE to disable gzipping compressed files 
	 */
	const ALSO_GZIP = 'JCSS_ALSO_GZIP'; 
}


Config::set_value_from_constant(ConfigJCSSManager::CSS_DIR, 'APP_JCSS_CSS_DIR', 'css/');
Config::set_value_from_constant(ConfigJCSSManager::JS_DIR, 'APP_JCSS_JS_DIR', 'js/');

Config::set_value_from_constant(ConfigJCSSManager::CSS_COMPRESSOR, 'APP_JCSS_CSS_COMPRESSOR', 'yui');
Config::set_value_from_constant(ConfigJCSSManager::JS_COMPRESSOR, 'APP_JCSS_JS_COMPRESSOR', 'yui');

Config::set_feature_from_constant(ConfigJCSSManager::USE_COMPRESSED, 'APP_JCSS_USE_COMPRESSED', !APP_TESTMODE);
Config::set_feature_from_constant(ConfigJCSSManager::ALSO_GZIP, 'APP_JCSS_ALSO_GZIP', true);

/**
 * @defgroup JCSSManager
 * 
 * JCSSManager combines and compresses both Javascript and CSS files, reducing both the number of requests and
 * the amount of data transfered by your webserver. 
 * 
 * JCSSManager currently supports:
 * 
 * @li YUI Compressor 2.4.2 (http://developer.yahoo.com/yui/compressor/) for both CSS and Javascript. YUI is default.
 * @li Closure Compiler 20100330 (http://code.google.com/closure/compiler/) for JavaScript only. Closure Compression Level is
 *     SIMPLE_OPTIMIZATIONS
 * @li CSS Tiry 1.3 (http://csstidy.sourceforge.net/index.php) for CSS compression. Compression level is default + removing last ";"
 *     
 * @attention JCSSManager need Java to be installed on your system to run both YUI Compressoor or Closure Compiler!
 * 
 * @section Usage Usage
 * 
 * 
 */