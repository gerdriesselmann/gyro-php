<?php
/**
 * @defgroup DriverSwitch
 * @ingroup DB
 * 
 * Allows switching the driver (this is: the connection) of an already existing table.
 * 
 * @section Usage Usage
 * 
 * To switch a given table to a new driver, call
 * 
 * @code
 * DBTableDriverSwitch::switch_table($table_name, $driver);
 * @endcode
 * 
 * If you do this for a module's tables, place this code in app/enabled.inc.php.  
 */