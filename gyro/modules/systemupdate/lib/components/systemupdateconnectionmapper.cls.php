<?php
/**
 * Allows switching complete modules to another DB connection
 */
class SystemUpdateConnectionMapper {
	static $connections = array();

	/**
	 * Return all connections, kind of SELECT DISTINCT
	 * 
	 * @return array
	 */
	static function get_all_connections() {
		$ret = array();
		foreach(self::$connections as $module => $connection) {
			$ret[$connection] = $connection;
		}
		$ret[DB::DEFAULT_CONNECTION] = DB::DEFAULT_CONNECTION;
		return $ret;
	}
	
	/**
	 * Return conenction for given module
	 */
	static function get_module_connection($module) {
		return Arr::get_item(self::$connections, $module, DB::DEFAULT_CONNECTION);
	}
	
	/**
	 * Set module connection
	 */
	static function set_module_connection($module, $connection) {
		self::$connections[$module] = $connection;
	}
}