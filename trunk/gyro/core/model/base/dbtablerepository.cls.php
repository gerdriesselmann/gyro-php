<?php
/**
 * Centralized repository of table definitions
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBTableRepository {
	static $tables = array();
	
	public static function register(IDBTable $table, $name = false) {
		if (empty($name)) {
			$name = $table->get_table_name();
		}
		self::$tables[$name] = $table;
	}
	
	public static function get($name) {
		return Arr::get_item(self::$tables, $name, false);
	}
}
