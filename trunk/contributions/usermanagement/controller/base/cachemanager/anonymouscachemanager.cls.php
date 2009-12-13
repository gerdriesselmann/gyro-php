<?php

/**
 * Cache manager that caches if user is not logged in
 * 
 * Returns url as cache key
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
}
