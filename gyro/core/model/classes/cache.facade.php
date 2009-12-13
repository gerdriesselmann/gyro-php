<?php
/**
 * Wrapper around cache access
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */ 
class Cache {
	private static $cache_item = null;

	/**
	 * Returns true, if item is chaced 
	 */
	public static function is_cached($cache_keys) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
			self::$cache_item = false;
			return false;			
		}

		$dao = new DAOCache();
		$dao->add_where('content_gzip', DBWhere::OP_NOT_NULL);
		$dao->set_keys(self::extract_keys($cache_keys));
		$dao->add_where('expirationdate', '>', DBFieldDateTime::NOW);
		
		if ($dao->find(DAOCache::AUTOFETCH)) {
			self::$cache_item = $dao;
			return true; 
		}
		else {
			self::$cache_item = false;
			return false;
		}
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @param bool If true, both Content and Gziped Content must be available
	 * @return DAOCache The cache as array with members "content" and "data", false if cache is not found
	 */
	public static function read($cache_keys) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
			return false;			
		}

		$dao = new DAOCache();
		$dao->add_where('content_gzip', DBWhere::OP_NOT_NULL);
		$dao->set_keys(self::extract_keys($cache_keys));
		$dao->add_where('expirationdate', '>', DBFieldDateTime::NOW);
		
		if ($dao->find(DAOCache::AUTOFETCH)) {
			return $dao; 
		}
		else {
			return false;
		}
	}
	
	/**
	 * Store content in cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @param string The cache
	 */
	public static function store($cache_keys, $content, $cache_life_time, $data = '', $is_compressed = false) {
		// Allow diableing of cache
		if (Config::has_feature(Config::DISABLE_CACHE)) {
			return;			
		}

		try {
			// Clear old items
			self::remove_expired();
			$dao = new DAOCache();
			$dao->set_keys(self::extract_keys($cache_keys));
			$update = $dao->find(DAOCache::AUTOFETCH);
			
			if ($is_compressed) {
				$dao->set_content_compressed($content);
			}
			else {
				$dao->set_content_plain($content);
			}
			$dao->data = $data;
			$dao->expirationdate = time() + $cache_life_time;
			if ($update) {
				$dao->update();
			}
			else {
				$dao->insert();
			}
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

		if (empty($cache_keys)) {
			self::do_clear_all();
		}
		else if ($cache_keys instanceof ICachable) {
			self::do_clear_cachable($cache_keys);
			foreach($cache_keys->get_dependend_cachables() as $dependance) {
				self::do_clear_cachable($dependance);
			}
		}
		else {
			self::do_clear($cache_keys);
		}
	}
	
	/**
	 * Clear chache for given ICachable 
	 */
	private static function do_clear_cachable($cachable) {
		$keys = $cachable->get_all_cache_ids();
		foreach($keys as $key) {
			self::do_clear($key);
		}		
	}
	
	/**
	 * Clear all cache
	 */
	private static function do_clear_all() {
		$dao = new DAOCache();
		$dao->add_where('1 = 1');
		$dao->delete(DAOCache::WHERE_ONLY);	
	}
	
	/**
	 * Clear cache for given cache key(s)
	 */
	private static function do_clear($cache_keys) {
		$keys = self::extract_keys($cache_keys);
		$dao = new DAOCache();
		$dao->set_keys($keys, true);
		$dao->delete(DAOCache::WHERE_ONLY);	
	}
	
	/**
	 * Transform the given param into an array of keys
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 */
	private static function extract_keys($cache_keys) {
		if (is_array($cache_keys)) {
			return array_values($cache_keys);
		}
		else if (is_string($cache_keys) || is_numeric($cache_keys)) {
			return array($cache_keys);
		}
		return array();		
	}

	/**
	 * Removes expired cache entries
	 */
	public static function remove_expired() {
		$dao = new DAOCache();
		$dao->add_where('expirationdate', '<', DBFieldDateTime::NOW);
		$dao->delete(DAOCache::WHERE_ONLY);
	}
}

