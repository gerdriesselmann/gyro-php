<?php
/**
 * Cache manager that caches two versions: One for logged in users and one for guests 
 * 
 * If user is logged in, it returns the CacheHeaderManager set
 * as ConfigUsermanagement::CACHEHEADER_CLASS_LOGGEDIN
 * 
 * Appends [g] or [u] to the first cache key
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class LoggedInSwitchCacheManager extends SuccessCacheManager {
	/**
	 * Return a chache id
	 */
	public function get_cache_id() {
		$ret = Arr::force(parent::get_cache_id(), false);
		$ret[0] .= Users::is_logged_in() ? '[u]' : '[g]';
		return $ret; 
	}
	
	/**
	 * Get cache header manager
	 * 
	 * @return ICacheHeaderManager
	 */
	public function get_cache_header_manager() {
		if (Users::is_logged_in()) {
			return CacheHeaderManagerFactory::create(Config::get_value(ConfigUsermanagement::CACHEHEADER_CLASS_LOGGEDIN));
		}
		else {
			return parent::get_cache_header_manager();
		}
	}		
}
