<?php
/**
 * @defgroup Simpletest
 * @ingroup Modules
 * 
 * Wraps the simpletest unit test library (http://www.simpletest.org/)
 * 
 * Place your tests in a directory "simpletests" directly beneath the module or application root.
 * They will be automatically run when simpletest/run is invoked.
 * 
 * Test files must end with .test.php and the class name must start with "Test"  
 */

// simpletest doesn't run in STRICT mode
error_reporting(E_ALL ^ E_NOTICE);

if (!defined('APP_SIMPLETEST_DIR')) {
	define('APP_SIMPLETEST_DIR', dirname(__FILE__) . '/3rdparty/simpletest/'); 
}

