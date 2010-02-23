<?php
/**
 * @defgroup DriverSwitch
 * @ingroup DB
 * 
 * Allows switching the driver (this is: the connection) of an already existing table.
 * 
 * @section Usage
 * 
 * To swithc a given table to a new driver, call 
 * 
 * @code
 * DBTableDriverSwitch::switch_table($table_name, $driver);
 * @endcoe
 * 
 * If you do this for a module's tables, palce this code in app/enabled.inc.php.  
 */