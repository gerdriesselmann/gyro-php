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
	 * CSS Compression method. Only 'yui' and 'csstidy' and 'concat' and `webpack` are supported
	 */
	const CSS_COMPRESSOR = 'JCSS_CSS_COMPRESSOR';
	/**
	 * Javascript Compression method. Possible values are
	 *
	 * - 'yui'
	 * - 'closure'
	 * - 'concat'
	 * - 'webpack'
	 * - 'uglifyjs'
	 *
	 *  To use terser (=uglifyjs for ED6+) set UGLIFY_USE_TERSER to true
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
	/**
	 * yuicompressor versions to use, bundled with gyro-php / possible values are
	 *
	 * '2.4.2' => used as default, to avoid issues with CKeditor
	 * '2.4.8' / alias: 'latest', see below
	 *
	 * If APP_3RDPARTY_DIR is set, custom versions may be
	 * added by application too, just define
	 *
	 * - APP_3RDPARTY_DIR (i.e. dirname(__FILE__) . '/3rdparty')
	 *
	 * and
	 *
	 * - YUI_VERSION (i.e. '2.4.6')
	 *
	 * with propper values and provide yuicompressor.jar at that path:
	 *
	 * APP_3RDPARTY_DIR . '/yuicompressor/' . YUI_VERSION . '/yuicompressor.jar'
	 */
	const YUI_VERSION = 'JCSS_YUI_VERSION';
	const YUI_VERSION_LATEST = '2.4.8';

	const WEBPACK_CONFIG_FILE = 'JCSS_WEBPACK_CONFIG_FILE';

	/**
	 * Supply a special postcss config instead of postcss.config.js
	 */
	const POSTCSS_CONFIG_FILE = 'JCSS_POSTCSS_CONFIG_FILE';

	const UGLIFY_COMPRESS_OPTIONS = 'JCSS_UGLIFY_COMPRESS_OPTIONS';
	const UGLIFY_USE_TERSER = 'JCSS_UGLIFY_USE_TERSER';

}


Config::set_value_from_constant(ConfigJCSSManager::CSS_DIR, 'APP_JCSS_CSS_DIR', 'css/');
Config::set_value_from_constant(ConfigJCSSManager::JS_DIR, 'APP_JCSS_JS_DIR', 'js/');
Config::set_value_from_constant(ConfigJCSSManager::YUI_VERSION, 'APP_JCSS_YUI_VERSION', '2.4.2');

Config::set_value_from_constant(ConfigJCSSManager::WEBPACK_CONFIG_FILE, 'APP_JCSS_WEBPACK_CONFIG_FILE', '');
Config::set_value_from_constant(ConfigJCSSManager::POSTCSS_CONFIG_FILE, 'APP_JCSS_POSTCSS_CONFIG_FILE', '');
Config::set_value_from_constant(ConfigJCSSManager::UGLIFY_COMPRESS_OPTIONS, 'APP_JCSS_UGLIFY_COMPRESS_OPTIONS', '');
Config::set_feature_from_constant(ConfigJCSSManager::UGLIFY_USE_TERSER, 'APP_JCSS_UGLIFY_USE_TERSER', false);

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
 * @li YUI Compressor 2.4.2 (http://developer.yahoo.com/yui/compressor/) for both CSS and Javascript. Unfortunately, the current release 2.4.6 does not
 *     work well with YAML CSS framework, so 2.4.2 is kept. YUI is default.
 * @li Closure Compiler 20100330 (http://code.google.com/closure/compiler/) for JavaScript only. Closure Compression Level is
 *     SIMPLE_OPTIMIZATIONS
 * @li CSS Tidy 1.3 (http://csstidy.sourceforge.net/index.php) for CSS compression. Compression level is default + removing last ";"
 * @li Webpack Uses locally installed webpack
 * @li UglifyJS for JavaScript compression
 * @li PostCSS for CSS compression
 * @li csso for CSS compression
 *
 * @attention JCSSManager needs Java to be installed on your system to run both YUI Compressoor or Closure Compiler!
 *
 * @attention JCSSManager needs NodeJS based tools to be installed to run them. It assume webpack, csso or PostCSS are installed within the project and
 *            accessible at {project root}/node_modules/.bin/
 *
 */