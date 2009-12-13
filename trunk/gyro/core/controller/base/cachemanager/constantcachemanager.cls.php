<?php
/**
 * Cache manager that returns a constant value as cache id
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ConstantCacheManager implements ICacheManager {
	private $cache_id;
	private $expiration;
	
	/**
	 * Constructor
	 *
	 * @param mixed $cache_id
	 * @param int $duration Cache duration , defaults to 2 hours
	 */
	public function __construct($cache_id, $duration = 7200) {
		$this->set_cache_id($cache_id);
		$this->set_cache_duration($duration);
	}

	public function initialize($page_data) {
		// Do nothing here
	}
		
	/**
	 * Return a chache id
	 */
	public function get_cache_id() {
		return $this->cache_id; 
	}
	
	/**
	 * Sets the cache id
	 *
	 * @param mixed $id
	 */
	public function set_cache_id($id) {
		$this->cache_id = $id;
	}
	
	/**
	 * Returns the datetime this cache expires
	 *
	 * @return timestamp
	 */
	public function get_expiration_datetime() {
		return $this->expiration;
	}
	
	/**
	 * Sets the expiration datetime this cache expires
	 *
	 * @param timestamp $datetime
	 */
	public function set_expiration_datetime($datetime) {
		$this->expiration = $datetime;
	}
	
	/**
	 * Set Cache duration in seconds
	 *
	 * @param int $seconds
	 */
	public function set_cache_duration($seconds) {
		$this->expiration = time() + $seconds;
	}
}
