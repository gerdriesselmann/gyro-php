<?php
/**
 * Interface for classes that persist cache items 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICachePersister {
	/**
	 * Returns true, if item is cached
	 *  
	 * @param $cache_keys mixed A set of key params, may be an array or a string
	 * @return bool 
	 */
	public function is_cached($cache_keys);

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return ICacheItem False if cache is not found
	 */
	public function read($cache_keys);
	
	/**
	 * Store content in cache
	 * 
	 * @param mixed $cache_keys A set of key params, may be an array or a string
	 * @param string $content The cache
	 * @param int $cache_life_time Cache life time in seconds
	 * @param mixed $data Any data assoziated with this item
	 * @param bool $is_compressed True, if $content is already gzip compressed 
	 */
	public function store($cache_keys, $content, $cache_life_time, $data = '', $is_compressed = false);
	
	/**
	 * Clear the cache
	 * 
	 * @param mixed $cache_keys A set of key params, may be an array or a string, or an ICachable instance. If NULL, all is cleared
	 */
	public function clear($cache_keys = NULL);

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired();
}
