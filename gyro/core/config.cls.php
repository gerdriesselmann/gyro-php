<?php
/**
 * Contains Configuration parameters
 * 
 * @author Gerd Riesselmann
 * @ingroup Core
 */
class Config {
	/**
	 * Max Gyro version the application supports
	 */
	const VERSION_MAX = 'VERSION_MAX';
	/**
	 * Version of Gyro
	 */
	const VERSION = 'VERSION';
	
	const TITLE = 'TITLE';
	const ITEMS_PER_PAGE = 'ITEMS_PER_PAGE';
	
	const TESTMODE = 'TESTMODE';
	const THROW_ON_DB_ERROR = 'THROW_ON_DB_ERROR';
	const THROW_ON_WARNING = 'THROW_ON_WARNING';
	const DEBUG_QUERIES = 'DEBUG_QUERIES';
	const PRINT_DURATION = 'PRINT_DURATION';
	const DISABLE_CACHE = 'DISABLE_CACHE';
	const DISABLE_ERROR_CACHE = 'DISABLE_ERROR_CACHE';
	const ENABLE_HTTPS = 'ENABLE_HTTPS';
	const START_SESSION = 'START_SESSION';
	const FORCE_FULL_DOMAINNAME = 'FORCE_FULL_DOMAINNAME';
	/**
	 * Name of Session Handling Class (class must be included manually) 
	 */
	const SESSION_HANDLER = 'SESSION_HANDLER';
	/**
	 * Template engine
	 */
	const DEFAULT_TEMPLATE_ENGINE = 'DEFAULT_TEMPLATE_ENGINE';
	const PAGE_TEMPLATE = 'PAGE_TEMPLATE';
	const GZIP_SUPPORT = 'GZIP';
	/**
	 * LOGGING
	 */
	const LOG_QUERIES = 'LOG_QUERIES';
	const LOG_FAILED_QUERIES = 'LOG_FAILED_QUERIES';
	const LOG_SLOW_QUERIES = 'LOG_SLOW_QUERIES';
	const DB_SLOW_QUERY_THRESHOLD = 'DB_SLOW_QUERY_THRESHOLD';
	const LOG_TRANSLATIONS = 'LOG_TRANSLATIONS';
	const LOG_HTML_ERROR_STATUS = 'LOG_HTML_ERROR_STATUS';
	const LOG_HTTPREQUESTS = 'LOG_HTTPREQUESTS';
    /**
     * DB UTF8 / UTF8MB4
     */
    const DB_USE_UTF8MB4_ON_UTF8 = 'DB_USE_UTF8MB4_ON_UTF8';
    const DB_TR_UTF8_TO_UTF8MB4 = 'DB_TR_UTF8_TO_UTF8MB4';
	/**
	 * Added to each email subject line
	 */
	const MAIL_SUBJECT = 'MAIL_SUBJECT';
	/**
	 * Default FROM address
	 */
	const MAIL_SENDER = 'MAIL_SENDER';
	/**
	 * Return path (or bounce) address
	 */
	const MAIL_RETURN_PATH = 'MAIL_RETURN_PATH';
	/**
	 * Address to receive system notifications
	 */
	const MAIL_ADMIN = 'MAIL_ADMIN';
	/**
	 * Address to receive user mails (e.g. contact)
	 */
	const MAIL_SUPPORT = 'MAIL_SUPPORT';
	/**
	 * Mailer type. Switch Mailer type to 'smtp' to use SMTP. 
	 * All other values will use PHP's mail() function 
	 */
	const MAILER_TYPE = 'MAILER_TYPE';
	/**
	 * SMTP Host. MAILER_TYPE must be set to 'smtp' for this setting to take effect
	 */
	const MAILER_SMTP_HOST = 'MAILER_SMTP_HOST';
	/**
	 * SMTP User. MAILER_TYPE must be set to 'smtp' for this setting to take effect
	 */
	const MAILER_SMTP_USER = 'MAILER_SMTP_USER';
	/**
	 * SMTP Password. MAILER_TYPE must be set to 'smtp' for this setting to take effect
	 */
	const MAILER_SMTP_PASSWORD = 'MAILER_SMTP_PASSWORD';
	/**
	 * Some URLs
	 */
	const URL_DOMAIN = 'URL_DOMAIN';
	const URL_BASEDIR = 'URL_BASEDIR';
	const URL_SERVER = 'URL_SERVER';
	const URL_SERVER_SAFE = 'URL_SERVER_SAFE';
	const URL_BASEURL = 'URL_BASEURL';
	const URL_BASEURL_SAFE = 'URL_BASEURL_SAFE';
	const URL_ABSPATH = 'URL_ABSPATH';
	/**
	 * URL of Images
	 */
	const URL_IMAGES_DIR = 'URL_IMAGES_DIR';
	const URL_IMAGES = 'URL_IMAGES';
	/**
	 * The default URL for users not logged in
	 */
	const URL_DEFAULT_PAGE = 'URL_DEFAULT_PAGE';

	/**
	 * Default scheme (used unless specified in routes)
	 */
	const DEFAULT_SCHEME = 'DEFAULT_SCHEME';

	/**
	 * True to validate url on start 
	 */
	const VALIDATE_URL = 'VALIDATE_URL';
	/**
	 * Support Unicode domain name, requires the PHP intl module to be installed
	 * @see http://php.net/manual/de/book.intl.php
	 *
	 * Default is false for backward compatibility reasons
	 */
	const UNICODE_URLS = 'UNICODE_URLS';
	
	/**
	 * Temporary directory
	 */
	const TEMP_DIR = 'TEMP_DIR';
	/**
	 * Files output directory 
	 */
	const OUT_DIR = 'OUT_DIR'; 
	/**
	 * Include 3rd party dir
	 */
	const THIRDPARTY_DIR = '3RDPARTY_DIR';
	/** Logfile directory. Default is Config;;TEMP_DIR/log */
	const LOG_DIR = 'LOG_DIR';

	/**
	 * How the logfile's name is build. Available are %date% as %name% as palceholders
	 *
	 * Default is %date%_%name%.log
	 */
	const LOG_FILE_NAME_PATTERN = 'LOG_FILE_NAME_PATTERN';
	
	/**
	 * The query parameter that contains the path orignally invoked
	 */
	const QUERY_PARAM_PATH_INVOKED = 'QUERY_PARAM_PATH_INVOKED';

	const FORMVALIDATION_FIELD_NAME = 'FORMVALIDATION_FIELD_NAME';
	const FORMVALIDATION_HANDLER_NAME = 'FORMVALIDATION_HANDLER_NAME';
	const FORMVALIDATION_EXPIRATION_TIME = 'FORMVALIDATION_EXPIRATION_TIME';
	
	const PAGER_NUM_LINKS = 'PAGER_NUM_LINKS';
	const PAGER_CALCULATOR = 'PAGER_CALCULATOR';
	const PAGER_DEFAULT_POLICY = 'PAGER_DEFAULT_POLICY';

	/** CacheHeaderManager policy for cached stuff. Class name without CacheHeaderManager, e.g. FullCache for FullCacheCacheHeaderManager */
	const CACHEHEADER_CLASS_CACHED = 'CACHEHEADER_CLASS_CACHED';
	/** CacheHeaderManager policy for uncached stuff. Class name without CacheHeaderManager, e.g. NoCache for NoCacheCacheHeaderManager */
	const CACHEHEADER_CLASS_UNCACHED = 'CACHEHEADER_CLASS_UNCACHED'; 
	
	/**
	 * Features array
	 *
	 * @var array
	 */
	private static $data = array();
	
	/**
	 * Returns TRUE, if given feature is enabled
	 * 
	 * @param string $feature
	 * @return bool
	 */
	public static function has_feature($feature) {
		return isset(self::$data[$feature]);	
	}
	
	/**
	 * Enable or disable a feature
	 *
	 * @param string $feature
	 * @param bool $enabled
	 */
	public static function set_feature($feature, $enabled) {
		if ($enabled) {
			self::$data[$feature] = true;
		}
		else {
			unset(self::$data[$feature]);
		}
	}

	/**
	 * Set a feature by reading given constant
	 *
	 * @param string $feature
	 * @param string $constant
	 * @param bool $default
	 */
	public static function set_feature_from_constant($feature, $constant, $default) {
		self::set_feature($feature, defined($constant) ? constant($constant) : $default); 
	}
	
	/**
	 * Returns given value
	 * 
	 * @param string $feature
	 * @param bool $require If set, an exception is raised if value not set 
	 * @return mixed
	 */
	public static function get_value($feature, $require = false, $default = false) {
		if (isset(self::$data[$feature])) {
			return self::$data[$feature]; 
		}
		else if ($require) {
			throw new Exception(tr('Required Config-Value %feature not set', 'core', array('%feature' => $feature)));
		}
		return $default;	
	}
	
	/**
	 * Set a value
	 *
	 * @param string $feature
	 * @param mixed $value
	 */
	public static function set_value($feature, $value) {
		self::$data[$feature] = $value;
	}

	/**
	 * Set a value by reading given constant
	 *
	 * @param string $feature
	 * @param string $constant
	 * @param mixed $default
	 */
	public static function set_value_from_constant($feature, $constant, $default) {
		self::set_value($feature, defined($constant) ? constant($constant) : $default); 
	}
	
	/**
	 * Returns given url
	 * 
	 * Urls are values with placeholders, namely 
	 * - %domain%: Replaced with domain
	 * - %basedir%: Replaced with base dir
	 * 
	 * @param string $feature
	 * @return string
	 */
	public static function get_url($feature) {
		$url = self::get_value($feature, true);
		$url = str_replace('%scheme%', self::get_value(self::DEFAULT_SCHEME), $url);
		$url = str_replace('%domain%', self::get_value(self::URL_DOMAIN), $url);
		$url = str_replace('%basedir%', self::get_value(self::URL_BASEDIR), $url);
		return $url;
	}	
	
	/**
	 * Creates a hash from config values
	 * 
	 * @return string 40 character long hexstring (sha1)  
	 */
	public static function create_fingerprint() {
		$ret = '';
		foreach(self::$data as $key => $value) {
			$ret .= $value;
		}
		return sha1($ret);
	}
}
