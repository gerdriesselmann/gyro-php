<?php
/**
 * Cache manager that returns a constant value as cache id
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ConstantCacheManager implements ICacheManager {
	private $cache_id;
	
	/**
	 * Expiration date and time
	 * 
	 * @var timestamp
	 */
	private $expiration;
	
	/**
	 * Creation date and time
	 * 
	 * @var timestamp
	 */
	private $creation;
	
	/**
	 * Cache header manager
	 * 
	 * @var ICacheHeaderManager
	 */
	private $header_manager;
	
	
	/**
	 * Constructor
	 *
	 * @param mixed $cache_id
	 * @param int $duration Cache duration , defaults to 2 hours
	 * @param ICacheHeaderManager $header_manager Defaults to NoCacheCacheHeaderManager
	 */
	public function __construct($cache_id, $duration = 7200, $header_manager = false) {
		$this->set_cache_id($cache_id);
		$this->set_cache_duration($duration);
		$this->set_creation_datetime(time());
		if (empty($header_manager)) { $header_manager = CacheHeaderManagerFactory::create(Config::get_value(Config::CACHEHEADER_CLASS_UNCACHED)); }  
		$this->set_cache_header_manager($header_manager);
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
	 * Returns the datetime this cache has been created
	 *
	 * @return timestamp
	 */
	public function get_creation_datetime() {
		return $this->creation;
	}
	
	/**
	 * Sets the datetime this cache has been created
	 *
	 * @param timestamp $datetime
	 */
	public function set_creation_datetime($datetime) {
		$this->creation = $datetime;
	}	
	
	/**
	 * Set Cache duration in seconds
	 *
	 * @param int $seconds
	 */
	public function set_cache_duration($seconds) {
		$this->expiration = time() + $seconds;
	}
	
	/**
	 * Set cache header manager
	 */
	public function set_cache_header_manager(ICacheHeaderManager $manager) {
		$this->header_manager = $manager;		
	}
	
	/**
	 * Get cache header manager
	 * 
	 * @return ICacheHeaderManager
	 */
	public function get_cache_header_manager() {
		return $this->header_manager;
	}	
}
