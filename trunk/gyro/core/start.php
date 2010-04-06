<?php
/**
 * Script starting time
 */
define('APP_START_MICROTIME', microtime(true));

/**
 * Set a default timezone (This is a STRICT issue - PHP can't do this on its own :-) )
 */
date_default_timezone_set(date_default_timezone_get());

/**
 * Root dir of gyro framework
 *
 */
define ('GYRO_CORE_DIR', dirname(__FILE__) . '/');
define ('GYRO_ROOT_DIR', GYRO_CORE_DIR . '../');
require_once GYRO_CORE_DIR . 'config.cls.php';
Config::set_value(Config::VERSION, 0.5);
require_once GYRO_CORE_DIR . 'constants.inc.php';
require_once GYRO_CORE_DIR . 'lib/includes.inc.php';
// Include "Load" class
require_once GYRO_CORE_DIR . 'load.cls.php';
Load::add_module_base_dir(GYRO_ROOT_DIR . 'modules/');
//Load::directories('lib/helpers');

// Set error reporting settings
error_reporting(E_ALL ^ E_NOTICE);
if (Common::constant('APP_TESTMODE')) {
	ini_set('display_errors', 1);
	ini_set('log_errors', 1);
	error_reporting(E_ALL | E_STRICT);
}
else {
	ini_set('display_errors', 0);
	ini_set('log_errors', 1);
}

// Set locales
GyroLocale::set_locale(APP_LANG, APP_CHARSET);

// See if the URL follows some basiuc rules 
if (Config::has_feature(Config::VALIDATE_URL)) {
	Url::validate_current();
}

// Strip magic quotes
Common::preprocess_input();

//NOTE: display_errors MUST BE OFF in php.ini
if (Config::has_feature(Config::THROW_ON_WARNING)) {
	function ___errhandler($errno, $errstr, $errfile, $errline) {
		$msg = $errno . ': ' . $errstr;
		$msg .= "\n\n";
		$msg .= 'File: ' . $errfile . ' -- Line: ' . $errline;
		if (ini_get('log_errors')) {
			error_log($msg);
		}
		throw new Exception($msg);
	}
	set_error_handler('___errhandler', E_WARNING);
}

if (file_exists(APP_INCLUDE_ABSPATH . 'modules.php')) {
	// Enable Modules
	include_once APP_INCLUDE_ABSPATH . 'modules.php';
}

// Check if domain is OK. That is redirect domain.com to www.domain.com, if www.domain.com is APP_URL_DOMAIN )
if (Config::has_feature(Config::FORCE_FULL_DOMAINNAME)) {
	$url_cur = Url::current();
	$url_domain = Url::current()->set_host(Config::get_value(Config::URL_DOMAIN));
	if ($url_domain->build() != $url_cur->build()) {
		$url_domain->redirect(Url::PERMANENT);
		exit;
	}	
}

// Load all helpers
Load::directories('lib/helpers');
// Load all interfaces
Load::directories('lib/interfaces');

if (Config::has_feature(Config::TESTMODE)) {
	// Load logger
	Load::components('logger');	
}

// Load translation
Load::components('translator');

// DB Setup
//include dataobject classes
Load::directories('model/base');
Load::directories('model/base/fields');
Load::directories('model/base/constraints');
Load::directories('model/base/queries');
Load::first_file('model/base/sqlbuilder/dbsqlbuilderfactory.cls.php');
DB::initialize();

// Routing 
Load::directories('controller/base');
// Views
Load::directories('view/base');
// Load behaviour base classes
Load::directories('behaviour/base');

$session_handler_class = Config::get_value(Config::SESSION_HANDLER);  
if ($session_handler_class) {
	// Switch session to use DB (default)
	Session::set_handler(new $session_handler_class());
}
if (Config::has_feature(Config::START_SESSION)) {
	Session::start();
} else {
	Session::start_existing();
}

if (file_exists(APP_INCLUDE_ABSPATH . 'enabled.inc.php')) {
	// Allow app to do things before modules start
	include_once APP_INCLUDE_ABSPATH . 'enabled.inc.php';
}
Load::files('start.inc.php', Load::ORDER_DECORATORS);

AccessControl::load();
