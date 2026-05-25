<?php
/**
 * PHPUnit bootstrap for Gyro-PHP tests.
 *
 * Loads the minimal framework core needed for unit testing helpers,
 * without requiring a database connection or full application context.
 */

define('APP_START_MICROTIME', microtime(true));
date_default_timezone_set('UTC');

define('GYRO_CORE_DIR', dirname(__DIR__) . '/gyro/core/');
define('GYRO_ROOT_DIR', GYRO_CORE_DIR . '../');

// Minimal app constants required by the framework
if (!defined('APP_INCLUDE_ABSPATH')) define('APP_INCLUDE_ABSPATH', dirname(__DIR__) . '/');
if (!defined('APP_URL_DOMAIN')) define('APP_URL_DOMAIN', 'localhost');
if (!defined('APP_MAIL_SENDER')) define('APP_MAIL_SENDER', 'test@localhost');
if (!defined('APP_MAIL_ADMIN')) define('APP_MAIL_ADMIN', 'test@localhost');
if (!defined('APP_MAIL_SUPPORT')) define('APP_MAIL_SUPPORT', 'test@localhost');
if (!defined('APP_TESTMODE')) define('APP_TESTMODE', true);

// Load Config class and constants
require_once GYRO_CORE_DIR . 'config.cls.php';
Config::set_value(Config::VERSION, 0.6);
require_once GYRO_CORE_DIR . 'constants.inc.php';

// Load core includes (helpers, locale, etc.)
require_once GYRO_CORE_DIR . 'lib/includes.inc.php';

// Load the autoloader
require_once GYRO_CORE_DIR . 'load.cls.php';
Load::add_module_base_dir(GYRO_ROOT_DIR . 'modules/');

// Set locale
GyroLocale::set_locale('en', 'UTF-8');

// Load interfaces and helpers
Load::directories('lib/interfaces');
Load::directories('lib/helpers');
Load::directories('lib/helpers/converters');

// Load model base and DB driver classes for DB-related tests
Load::directories('model/base');
Load::directories('model/base/fields');
Load::directories('model/base/queries');
Load::directories('model/base/sqlbuilder');
Load::directories('model/base/constraints');
Load::directories('model/drivers/mysql');

// Load model facade/classes (Cache, etc.)
Load::directories('model/classes');

// Load behaviour/commands
Load::directories('behaviour/base');

// Load controller/routing classes
Load::directories('controller/base');
Load::directories('controller/base/routes');
Load::directories('controller/base/routes/parameterizedroutehandlers');
Load::directories('controller/base/cachemanager');

// Load view base for widget tests
Load::directories('view/widgets');

// Load referer and translator components
Load::components('referer');
Load::components('translator');

// Load simpletest mock classes for DB tests
require_once GYRO_ROOT_DIR . 'modules/simpletest/simpletests/mocks/dbdriver.sql.mock.cls.php';
require_once GYRO_ROOT_DIR . 'modules/simpletest/model/base/dbtable.mock.cls.php';

// Register mock driver as default DB connection (avoids "Connection default not found")
DB::create_connection(DB::DEFAULT_CONNECTION, 'mysql', 'db', 'user', 'password', 'host');
// Replace the real driver with our mock that doesn't connect
$mock_driver = new DBDriverMySqlMock();
$reflector = new ReflectionClass(DB::class);
$prop = $reflector->getProperty('connections');
$prop->setAccessible(true);
$connections = $prop->getValue();
$connections[DB::DEFAULT_CONNECTION] = $mock_driver;
$prop->setValue(null, $connections);

// Load test DAO model classes
foreach (glob(GYRO_ROOT_DIR . 'modules/simpletest/model/classes/*.model.php') as $model_file) {
	require_once $model_file;
}
