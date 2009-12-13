<?php
/**
 * Cache manager to disable caching
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class NoCacheCacheManager implements ICacheManager {
	public function initialize($page_data) {
		// Do nothing here
	}

	/**
	 * Return a chache id
	 */
	public function get_cache_id() {
		return ''; 
	}

	/**
	 * Sets the cache id
	 *
	 * @param mixed $id
	 */
	public function set_cache_id($id)  {
		// Do nothing here
	}
	
	/**
	 * Returns the datetime this cache expires
	 *
	 * @return timestamp
	 */
	public function get_expiration_datetime() {
		return time() - GyroDate::ONE_DAY;
	}
	
	/**
	 * Sets the expiration datetime this cache expires
	 *
	 * @param timestamp $datetime
	 */
	public function set_expiration_datetime($datetime) {
		// Do nothing here
	}
	
	/**
	 * Set Cache duration in seconds
	 *
	 * @param int $seconds
	 */
	public function set_cache_duration($seconds) {
		// Do nothing here
	}	
}
