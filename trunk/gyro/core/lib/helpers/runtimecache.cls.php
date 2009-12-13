<?php
/**
 * Cache for storing data at runtime
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class RuntimeCache {
	private static $cache = array(); 
	
	/**	 
	 * Sets cache item
	 *
	 * @param string|array $keys
	 * @param mixed $value
	 */
	public static function set($keys, $value) {
		Arr::set_item_recursive(self::$cache, $keys, $value);
	}
	
	/**
	 * Find cache iten
	 *
	 * @param string|array $keys
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($keys, $default = false) {
		return Arr::get_item_recursive(self::$cache, $keys, $default);
	}
	
	/**	 
	 * Unsets cache item
	 *
	 * @param string|array $keys
	 */	
	public static function remove($keys) {
		Arr::unset_item_recursive(self::$cache, $keys);
	}
	
	/**
	 * Clears all global cache items
	 * 
	 * @return void
	 */
	public static function clear_all() {
		self::$cache = array();
	}
}
