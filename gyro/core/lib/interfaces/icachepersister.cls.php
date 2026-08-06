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
	public function is_cached(mixed $cache_keys): bool;

	/**
	 * Read from cache
	 *
	 * @param mixed $cache_keys A set of key params, may be an array or a string
	 * @return ICacheItem|false False if cache is not found
	 */
	public function read(mixed $cache_keys): ICacheItem|false;

	/**
	 * Store content in cache
	 *
	 * @param mixed $cache_keys A set of key params, may be an array or a string
	 * @param string $content The cache
	 * @param int $cache_life_time Cache life time in seconds
	 * @param mixed $data Any data assoziated with this item
	 * @param bool $is_compressed True, if $content is already gzip compressed
	 */
	public function store(mixed $cache_keys, string $content, int $cache_life_time, mixed $data = '', bool $is_compressed = false): void;

	/**
	 * Clear the cache
	 *
	 * @param mixed $cache_keys A set of key params, may be an array or a string, or an ICachable instance. If NULL, all is cleared
	 */
	public function clear(mixed $cache_keys = NULL): void;

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired(): void;
}
