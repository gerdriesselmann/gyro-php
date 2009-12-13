<?php
/**
 * A cache manager
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICacheManager {
	/**
	 * Initialize this instance
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data);
	
	/**
	 * Returs the cache id
	 *
	 * @return mixed
	 */
	public function get_cache_id();
	
	/**
	 * Sets the cache id
	 *
	 * @param mixed $id
	 */
	public function set_cache_id($id);
	
	/**
	 * Returns the datetime this cache expires
	 *
	 * @return timestamp
	 */
	public function get_expiration_datetime();
	
	/**
	 * Sets the expiration datetime this cache expires
	 *
	 * @param timestamp $datetime
	 */
	public function set_expiration_datetime($datetime);
	
	/**
	 * Set Cache duration in seconds
	 *
	 * @param int $seconds
	 */
	public function set_cache_duration($seconds);
}
