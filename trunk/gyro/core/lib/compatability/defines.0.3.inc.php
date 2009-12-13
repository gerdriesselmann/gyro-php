<?php
if (!defined('APP_THROW_ON_DB_ERROR')) define('APP_THROW_ON_DB_ERROR', !APP_TESTMODE);
if (!defined('APP_THROW_ON_WARNING')) define('APP_THROW_ON_WARNING', !APP_TESTMODE);
if (!defined('APP_DEBUG_QUERIES')) define('APP_DEBUG_QUERIES', APP_TESTMODE);
if (!defined('APP_PRINT_DURATION')) define('APP_PRINT_DURATION', APP_TESTMODE);
if (!defined('APP_DISABLE_CACHE')) define('APP_DISABLE_CACHE', APP_TESTMODE);

if (!defined('APP_ENABLE_HTTPS')) define('APP_ENABLE_HTTPS', true);

/**
 * Template engine
 */
if (!defined('APP_DEFAULT_TEMPLATE_ENGINE')) define ('APP_DEFAULT_TEMPLATE_ENGINE', 'core');
if (!defined('APP_PAGE_TEMPLATE')) define ('APP_PAGE_TEMPLATE', 'core::page');

/**
 * LOGGING
 */
if (!defined('APP_LOG_QUERIES')) define('APP_LOG_QUERIES', APP_TESTMODE);
if (!defined('APP_LOG_TRANSLATIONS')) define('APP_LOG_TRANSLATIONS', APP_TESTMODE);
if (!defined('APP_LOG_HTML_ERROR_STATUS')) define('APP_LOG_HTML_ERROR_STATUS', APP_TESTMODE);

/**
 * Added to each email subject line
 */
if (!defined('APP_MAIL_SUBJECT')) define('APP_MAIL_SUBJECT', '[' . APP_TITLE . '] ');

/**
 * Mailer type. Switch Mailer type to 'smtp' to use SMTP. 
 * All other values will use PHP's mail() function 
 */
if (!defined('APP_MAILER_TYPE')) define ('APP_MAILER_TYPE', 'mail');
/**
 * SMTP Host. APP_MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
if (!defined('APP_MAILER_SMTP_HOST')) define ('APP_MAILER_SMTP_HOST', '');
/**
 * SMTP User. APP_MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
if (!defined('APP_MAILER_SMTP_USER')) define ('APP_MAILER_SMTP_USER', '');
/**
 * SMTP Password. APP_MAILER_TYPE must be set to 'smtp' for this setting to take effect
 */
if (!defined('APP_MAILER_SMTP_PASSWORD')) define ('APP_MAILER_SMTP_PASSWORD', '');

/**
 * Use GZIP?
 */
if ( isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') ) {
	define('APP_GZIP', true);
}
else {
	define('APP_GZIP', false);
}	

/**
 * Some URLs
 */
if (!defined('APP_URL_BASEDIR')) define('APP_URL_BASEDIR', '/');
if (!defined('APP_URL_SERVER')) define('APP_URL_SERVER', 'http://' . APP_URL_DOMAIN);
if (!defined('APP_URL_SERVER_SAFE')) define('APP_URL_SERVER_SAFE', 'https://' . APP_URL_DOMAIN);
define('APP_URL_BASEURL', APP_URL_SERVER . APP_URL_BASEDIR);
define('URL_BASEURL_SAFE', APP_URL_SERVER_SAFE . APP_URL_BASEDIR);
if (!defined('APP_URL_ABSPATH')) define('APP_URL_ABSPATH', APP_INCLUDE_ABSPATH . '../htdocs/');

/**
 * The default URL for users not logged in
 *
 * @var String
 */
if (!defined('APP_DEFAULT_PAGE')) define('APP_DEFAULT_PAGE', APP_URL_BASEURL);

/**
 * URL of Images
 */
if (!defined('APP_URL_IMAGES')) define('APP_URL_IMAGES', Config::get_value(Config::URL_BASEDIR) . 'images/');

/**
 * Temporary directory
 */
if (!defined('APP_TEMP_DIR')) define('APP_TEMP_DIR', APP_INCLUDE_ABSPATH . '../tmp/');

/**
 * Files output directory 
 */
if (!defined('APP_OUT_DIR')) define('APP_OUT_DIR', APP_TEMP_DIR); 

/**
 * Include 3rd party dir
 */
if (defined('APP_3RDPARTY_DIR')) set_include_path(get_include_path() . PATH_SEPARATOR . APP_3RDPARTY_DIR);

if (!defined('FORMVALIDATION_FIELD_NAME')) define('FORMVALIDATION_FIELD_NAME', 'wergpiozt');
if (!defined('FORMVALIDATION_HANDLER_NAME')) define('FORMVALIDATION_HANDLER_NAME', 'uehfjkwe');
