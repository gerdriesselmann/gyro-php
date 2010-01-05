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
	 * @return bool 
	 */
	public static function is_cached($cache_keys) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
			return false;			
		}
		
		$impl = self::get_implementation();
		return $impl->is_cached($cache_keys);
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return ICacheItem False if cache is not found
	 */
	public static function read($cache_keys) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
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
	 */
	public static function store($cache_keys, $content, $cache_life_time, $data = '', $is_compressed = false) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
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
	 */
	public static function clear($cache_keys = NULL) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
			return;			
		}
		
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

