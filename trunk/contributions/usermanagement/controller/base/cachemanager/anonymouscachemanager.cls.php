<?php
/**
 * Cache manager that caches only if user is not logged in
 * 
 * If user is logged in, it returns the CacheHeaderManager set
 * as ConfigUsermanagement::CACHEHEADER_CLASS_LOGGEDIN
 * 
 * Returns url as cache key
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class AnonymousCacheManager extends SuccessCacheManager {
	/**
	 * Return a chache id
	 */
	public function get_cache_id() {
		if (Users::is_logged_in() == false) {
			return parent::get_cache_id();
		}
		return ''; 
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
