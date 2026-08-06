<?php
/**
 * CLI Bootstrap - loads the Gyro-PHP framework without HTTP context.
 *
 * This file sets up the framework core for CLI usage:
 * - Defines required constants
 * - Loads .env if present
 * - Loads core helpers, interfaces, and model layer
 * - Does NOT start sessions, routing, or output buffering
 *
 * @since 0.8
 * @ingroup CLI
 */

define('APP_START_MICROTIME', microtime(true));
define('GYRO_CLI_MODE', true);
date_default_timezone_set('UTC');

// Determine paths
$gyro_root = dirname(__DIR__, 2) . '/';
define('GYRO_CORE_DIR', $gyro_root . 'core/');
define('GYRO_ROOT_DIR', dirname(GYRO_CORE_DIR) . '/');

// APP_INCLUDE_ABSPATH = project root (where .env and modules.php live)
if (!defined('APP_INCLUDE_ABSPATH')) {
	define('APP_INCLUDE_ABSPATH', dirname($gyro_root) . '/');
}

// Load Config class
require_once GYRO_CORE_DIR . 'config.cls.php';
Config::set_value(Config::VERSION, 0.6);

// Load .env if present (before constants)
require_once GYRO_CORE_DIR . 'lib/helpers/env.cls.php';
if (defined('APP_INCLUDE_ABSPATH')) {
	Env::load(APP_INCLUDE_ABSPATH . '.env');
}

// Minimal app constants for CLI (can be overridden by .env)
if (!defined('APP_URL_DOMAIN')) define('APP_URL_DOMAIN', 'localhost');
if (!defined('APP_MAIL_SENDER')) define('APP_MAIL_SENDER', 'cli@localhost');
if (!defined('APP_MAIL_ADMIN')) define('APP_MAIL_ADMIN', 'cli@localhost');
if (!defined('APP_MAIL_SUPPORT')) define('APP_MAIL_SUPPORT', 'cli@localhost');
if (!defined('APP_TESTMODE')) define('APP_TESTMODE', false);

// Load framework constants
require_once GYRO_CORE_DIR . 'constants.inc.php';

// Load core includes (helpers, locale, etc.)
require_once GYRO_CORE_DIR . 'lib/includes.inc.php';

// Load the autoloader
require_once GYRO_CORE_DIR . 'load.cls.php';
Load::add_module_base_dir(GYRO_ROOT_DIR . 'modules/');
Load::add_module_base_dir(APP_INCLUDE_ABSPATH . 'contributions/');

// Set locale
GyroLocale::set_locale('en', 'UTF-8');

// Load interfaces and helpers
Load::directories('lib/interfaces');
Load::directories('lib/helpers');
Load::directories('lib/helpers/converters');

// Load model base and DB driver
Load::directories('model/base');
Load::directories('model/base/fields');
Load::directories('model/base/queries');
Load::directories('model/base/sqlbuilder');
Load::directories('model/base/constraints');
Load::directories('model/drivers/mysql');
Load::directories('model/classes');

// Load behaviour base
Load::directories('behaviour/base');

// Initialize DB connection if configured
if (defined('APP_DB_TYPE') && defined('APP_DB_NAME') && defined('APP_DB_USER')) {
	try {
		DB::initialize();
	} catch (\Exception $e) {
		// DB not available — commands that don't need DB can still run
	}
}

// Enable modules if modules.php exists
if (file_exists(APP_INCLUDE_ABSPATH . 'modules.php')) {
	include_once APP_INCLUDE_ABSPATH . 'modules.php';
}
