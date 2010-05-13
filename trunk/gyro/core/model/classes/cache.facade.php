<?php
/**
 * Wrapper around cache access
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */ 
class Cache {
	/**
	 * Cache item persister implementation
	 * 
	 * @var ICachePersister
	 */
	private static $implementation;
	
	/**
	 * Get implementation
	 * 
	 * @return ICachePersister
	 */
	private static function get_implementation() {
		if (empty(self::$implementation)) {
			require_once dirname(__FILE__) . '/cache.db.impl.php';
 			self::set_implementation(new CacheDBImpl());
		}
		return self::$implementation;
	}
	
	/**
	 * Set persistance implementation
	 * 
	 * @param ICachePersister $impl
	 */
	public static function set_implementation(ICachePersister $impl) {
		self::$implementation = $impl;
	}
	
	/**
	 * Returns true, if item is cached
	 *  
	 * @param $cache_keys mixed A set of key params, may be an array or a string
	 * @param $ignore_disabled Lookup item, even if cache is disabled
	 * @return bool 
	 */
	public static function is_cached($cache_keys, $ignore_disabled = false) {
		// Allow diableing of cache
		if (!$ignore_disabled && Config::has_feature(Config::DISABLE_CACHE)) {
			return false;			
		}
		
		$impl = self::get_implementation();
		return $impl->is_cached($cache_keys);
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @param $ignore_disabled Lookup item, even if cache is disabled
	 * @return ICacheItem False if cache is not found
	 */
	public static function read($cache_keys, $ignore_disabled = false) {
		// Allow diableing of cache
		if (!$ignore_disabled && Config::has_feature(Config::DISABLE_CACHE)) {
			return false;			
		}

		$impl = self::get_implementation();
		return $impl->read($cache_keys);
	}
	
	/**
	 * Store content in cache
	 * 
	 * @param mixed $cache_keys A set of key params, may be an array or a string
	 * @param string $content The cache
	 * @param int $cache_life_time Cache life time in seconds
	 * @param mixed $data Any data assoziated with this item
	 * @param bool $is_compressed True, if $content is already gzip compressed
	 * @param $ignore_disabled Store item, even if cache is disabled 
	 */
	public static function store($cache_keys, $content, $cache_life_time, $data = '', $is_compressed = false, $ignore_disabled = false) {
		// Allow diableing of cache
		if (!$ignore_disabled && Config::has_feature(Config::DISABLE_CACHE)) {
			return;			
		}
		
		try {
			$impl = self::get_implementation();
			$impl->store($cache_keys, $content, $cache_life_time, $data, $is_compressed);
		}
		catch (Exception $ex) {
			// If inserting into cache fails, just resume application!
			@error_log($ex->getMessage());			
		}		
	}
	
	/**
	 * Clear the cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string, or an ICachable instance. If NULL, all is cleared
	 * @param $ignore_disabled Clear item, even if cache is disabled
	 */
	public static function clear($cache_keys = NULL, $ignore_disabled = false) {
		// Allow diableing of cache
		if (!$ignore_disabled && Config::has_feature(Config::DISABLE_CACHE)) {
			return;			
		}
		
		if ($cache_keys instanceof ICachable) {
			$keys = $cache_keys->get_all_cache_ids();
			foreach($keys as $key) {
				self::do_clear($key);
			}
			foreach($cache_keys->get_dependend_cachables() as $dependance) {
				self::clear($dependance);
			}
		}
		else {
			self::do_clear($cache_keys);
		}	
	}

	/**
	 * Invoke implemantation to clear a given item
	 */
	private static function do_clear($cache_keys) {
		$impl = self::get_implementation();
		$impl->clear($cache_keys);
	}

	/**
	 * Removes expired cache entries
	 */
	public static function remove_expired() {
		$impl = self::get_implementation();
		$impl->remove_expired();
	}
}

