<?php
/**
 * Language and charset
 */
if (!defined('APP_LANG')) define ('APP_LANG', 'en');
if (!defined('APP_CHARSET')) define('APP_CHARSET', 'UTF-8');

if (!defined('APP_DB_HOST')) {
	define('APP_DB_HOST', '127.0.0.1');
}
if (!defined('APP_DB_TYPE')) {
	define('APP_DB_TYPE', 'mysql');
}

/**
 * Basic properties
 */
Config::set_value_from_constant(Config::TITLE, 'APP_TITLE', '');
Config::set_value_from_constant(Config::ITEMS_PER_PAGE, 'APP_ITEMS_PER_PAGE', 10);
/**
 * Gyro Version
 * 
 * Define APP_VERSION_MAX to older Gyro version to keep compatability 
 * 
 * Notable breakpoints are:
 * 
 *  - 0.3: Will include all Config constants as defines, too. E.g. APP_ITEMS_PER_PAGE will be always
 *         defined. 
 */
Config::set_value_from_constant(Config::VERSION_MAX, 'APP_VERSION_MAX', 10.0);
/**
 * Set debug related constants, if not already defined.
 */
if (!defined('APP_TESTMODE')) define('APP_TESTMODE', false); 
Config::set_feature(Config::TESTMODE, APP_TESTMODE);
Config::set_feature_from_constant(Config::THROW_ON_DB_ERROR, 'APP_THROW_ON_DB_ERROR', true);
Config::set_feature_from_constant(Config::THROW_ON_WARNING, 'APP_THROW_ON_WARNING', !APP_TESTMODE);
Config::set_feature_from_constant(Config::DEBUG_QUERIES, 'APP_DEBUG_QUERIES', APP_TESTMODE);
Config::set_feature_from_constant(Config::PRINT_DURATION, 'APP_PRINT_DURATION', APP_TESTMODE);
Config::set_feature_from_constant(Config::DISABLE_CACHE, 'APP_DISABLE_CACHE', APP_TESTMODE);
Config::set_feature_from_constant(Config::DISABLE_ERROR_CACHE, 'APP_DISABLE_ERROR_CACHE', APP_TESTMODE);
Config::set_feature_from_constant(Config::FORCE_FULL_DOMAINNAME, 'APP_FORCE_FULL_DOMAINNAME', true);
Config::set_feature_from_constant(Config::VALIDATE_URL, 'APP_VALIDATE_URL', true);
Config::set_feature_from_constant(Config::UNICODE_URLS, 'APP_UNICODE_URLS', false);
/**
 * Enable some specific behaviours
 */
Config::set_feature_from_constant(Config::ENABLE_HTTPS, 'APP_ENABLE_HTTPS', true);
Config::set_value_from_constant(Config::DEFAULT_SCHEME, 'APP_DEFAULT_SCHEME', 'http');
Config::set_feature_from_constant(Config::START_SESSION, 'APP_START_SESSION', true);
Config::set_value_from_constant(Config::SESSION_HANDLER, 'APP_SESSION_HANDLER', 'DBSession');
/**
 * Template engine
 */
Config::set_value_from_constant(Config::DEFAULT_TEMPLATE_ENGINE, 'APP_DEFAULT_TEMPLATE_ENGINE', 'core');
Config::set_value_from_constant(Config::PAGE_TEMPLATE, 'APP_PAGE_TEMPLATE', 'core::page');
/**
 * LOGGING
 */
Config::set_feature_from_constant(Config::LOG_QUERIES, 'APP_LOG_QUERIES', APP_TESTMODE);
Config::set_feature_from_constant(Config::LOG_FAILED_QUERIES, 'APP_LOG_FAILED_QUERIES', true);
Config::set_feature_from_constant(Config::LOG_SLOW_QUERIES, 'APP_LOG_SLOW_QUERIES', APP_TESTMODE);
Config::set_feature_from_constant(Config::LOG_TRANSLATIONS, 'APP_LOG_TRANSLATIONS', APP_TESTMODE);
Config::set_feature_from_constant(Config::LOG_HTML_ERROR_STATUS, 'APP_LOG_HTML_ERROR_STATUS', APP_TESTMODE);
Config::set_feature_from_constant(Config::LOG_HTTPREQUESTS, 'APP_LOG_HTTPREQUESTS', APP_TESTMODE);
/**
 * Added to each email subject line
 */
Config::set_value_from_constant(Config::MAIL_SUBJECT, 'APP_MAIL_SUBJECT', '[' . Config::get_value(Config::TITLE). ']');
/**
 * Default FROM address
 */
Config::set_value(Config::MAIL_SENDER, APP_MAIL_SENDER);
Config::set_value_from_constant(Config::MAIL_RETURN_PATH, 'APP_MAIL_RETURN_PATH', '');
/**
 * Address to receive system notifications
 */
Config::set_value(Config::MAIL_ADMIN, APP_MAIL_ADMIN);
/**
 * Address to receive user mails (e.g. contact)
 */
Config::set_value(Config::MAIL_SUPPORT, APP_MAIL_SUPPORT);
/**
 * Mailer type. Switch Mailer type to 'smtp' to use SMTP. 
 * All other values will use PHP's mail() function 
 */
Config::set_value_from_constant(Config::MAILER_TYPE, 'APP_MAILER_TYPE', 'mail');
/**
 * SMTP Host. MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
Config::set_value_from_constant(Config::MAILER_SMTP_HOST, 'APP_MAILER_SMTP_HOST', '');
/**
 * SMTP User. MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
Config::set_value_from_constant(Config::MAILER_SMTP_USER, 'APP_MAILER_SMTP_USER', '');
/**
 * SMTP Password. MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
Config::set_value_from_constant(Config::MAILER_SMTP_PASSWORD, 'APP_MAILER_SMTP_PASSWORD', '');
/**
 * Temporary directory
 */
Config::set_value_from_constant(Config::TEMP_DIR, 'APP_TEMP_DIR', APP_INCLUDE_ABSPATH . '../tmp/');
/**
 * Files output directory 
 */
Config::set_value_from_constant(Config::OUT_DIR, 'APP_OUT_DIR', Config::get_value(Config::TEMP_DIR)); 
/**
 * Include 3rd party dir
 */
if (defined('APP_3RDPARTY_DIR')) {
	set_include_path(get_include_path() . PATH_SEPARATOR . APP_3RDPARTY_DIR);
	Config::set_value(Config::THIRDPARTY_DIR, APP_3RDPARTY_DIR);
}
/*
 * Logfile directory and name
 */
Config::set_value_from_constant(Config::LOG_DIR, 'APP_LOG_DIR', Config::get_value(Config::TEMP_DIR) . 'log/');
Config::set_value_from_constant(Config::LOG_FILE_NAME_PATTERN, 'APP_LOG_FILE_NAME_PATTERN', '%date%_%name%.log');

/**
 * Formhandler stuff
 */
Config::set_value_from_constant(Config::FORMVALIDATION_FIELD_NAME, 'APP_FORMVALIDATION_FIELD_NAME', 'jfioeudkswefs');
Config::set_value_from_constant(Config::FORMVALIDATION_HANDLER_NAME, 'APP_FORMVALIDATION_HANDLER_NAME', 'uerwudjmdjwu');
Config::set_value_from_constant(Config::FORMVALIDATION_EXPIRATION_TIME, 'APP_FORMVALIDATION_EXPIRATION_TIME', 15);
/**
 * Use GZIP?
 */
$http_accept_encoding =  isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
if (strstr($http_accept_encoding, 'deflate')) {
	// Gyro Build in gzip compression uses compressed cache => less CPU usage
	Config::set_feature(Config::GZIP_SUPPORT, true);
} elseif (strstr($http_accept_encoding, 'gzip')) {
	// But Gyro compression supports deflate only, so for gzip only clients (Java HTTP Clients, that is) use PHP
	// build in compression
	ini_set('zlib.output_compression', 'On');
}
/**
 * Some URLs
 */
Config::set_value(Config::URL_DOMAIN, APP_URL_DOMAIN);
Config::set_value_from_constant(Config::URL_BASEDIR, 'APP_URL_BASEDIR', '/');
Config::set_value_from_constant(Config::URL_SERVER, 'APP_URL_SERVER', '%scheme%://%domain%');
Config::set_value_from_constant(Config::URL_BASEURL, 'APP_URL_BASEURL', '%scheme%://%domain%%basedir%');
if (Config::has_feature(Config::ENABLE_HTTPS)) {
	Config::set_value_from_constant(Config::URL_SERVER_SAFE, 'APP_URL_SERVER_SAFE', 'https://%domain%');
	Config::set_value_from_constant(Config::URL_BASEURL_SAFE, 'APP_URL_BASEURL', 'https://%domain%%basedir%');
}
else {
	Config::set_value_from_constant(Config::URL_SERVER_SAFE, 'APP_URL_SERVER_SAFE', '%scheme%://%domain%');
	Config::set_value_from_constant(Config::URL_BASEURL_SAFE, 'APP_URL_BASEURL', '%scheme%://%domain%%basedir%');
}
Config::set_value_from_constant(Config::URL_ABSPATH, 'APP_URL_ABSPATH', APP_INCLUDE_ABSPATH . 'www/');
/**
 * URL of Images
 */
Config::set_value_from_constant(Config::URL_IMAGES_DIR, 'APP_URL_IMAGES_DIR', 'images/');
Config::set_value_from_constant(Config::URL_IMAGES, 'APP_URL_IMAGES', '%basedir%' . Config::get_value(Config::URL_IMAGES_DIR));
/**
 * The default URL for users not logged in
 */
Config::set_value_from_constant(Config::URL_DEFAULT_PAGE, 'APP_DEFAULT_PAGE', '%scheme%://%domain%%basedir%');

/**
 * The query parameter to hold the path orginally invoked
 */
Config::set_value_from_constant(Config::QUERY_PARAM_PATH_INVOKED, 'APP_QUERY_PARAM_PATH_INVOKED', 'path');

/**
 * DB Slow Query threshold
 */
Config::set_value_from_constant(Config::DB_SLOW_QUERY_THRESHOLD, 'APP_DB_SLOW_QUERY_THRESHOLD', 0.0100);

/**
 * DB UTF8 / UTF8MB4
 */
Config::set_value_from_constant(Config::DB_USE_UTF8MB4_ON_UTF8, 'APP_DB_USE_UTF8MB4_ON_UTF8', false);
Config::set_value_from_constant(Config::DB_TR_UTF8_TO_UTF8MB4, 'APP_DB_TR_UTF8_TO_UTF8MB4', false);

/**
 * Cache headers
 */
Config::set_value_from_constant(Config::CACHEHEADER_CLASS_CACHED, 'APP_CACHEHEADER_CLASS_CACHED', 'PrivateRigid');
Config::set_value_from_constant(Config::CACHEHEADER_CLASS_UNCACHED, 'APP_CACHEHEADER_CLASS_UNCACHED', 'NoCache');