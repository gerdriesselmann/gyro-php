<?php
/**
 * @defgroup Console
 * @ingroup Modules
 *  
 * Command Line interface 
 */

// Register our views
ViewFactory::set_implementation(new ViewFactoryConsole());

if (!defined('CONSOLE_PHP_INVOCATION')) define('CONSOLE_PHP_INVOCATION', 'php');

