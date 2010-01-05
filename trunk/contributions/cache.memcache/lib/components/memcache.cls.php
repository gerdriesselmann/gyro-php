<?php
/*
 * Wrapper around memcache/memcached
 * 
 * @author Gerd Riesselmann
 * @ingroup Memcache 
 */
class GyroMemcache  {
	/**
	 * Memcache(d) instance
	 * 
	 * @var Memcached
	 */
	private static $delegate;

	/**
	 * Init Memcache instance
	 */
	public static function init() {
		if (class_exists('Memcached')) {		
			self::$delegate = new Memcached(Config::get_value(Config::TITLE));
		}
		else if (class_exists('Memcache')) {
			self::$delegate = new Memcache();	
		}
		else {
			throw new Exception('GyroMemcache::init(): No memcache extension installed');
		}
	}
	
	/**
	 * Add a server
	 * 
	 * @param string $host Server name or IP address
	 * @param int $port Port
	 * @param int $weight 
	 *   The weight of the server relative to the total weight of all the servers in the pool. 
	 *   This controls the probability of the server being selected for operations. This is used 
	 *   only with consistent distribution option and usually corresponds to the amount of 
	 *   memory available to memcache on that server. 
	 */
	public static function add_server($host = 'localhost', $port = 11211) {
		if (self::$delegate instanceof Memcache) {
			self::$delegate->addServer($host, $port); // , true, $weight);
		}
		else {
			self::$delegate->addServer($host, $port);
		}
	}
	
	/**
	 * Returns data from server
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public static function get($key) {
		return self::$delegate->get($key);	
	}
	
	/**
	 * Set data on server
	 * 
	 * @param string $key 
	 * @param mixed $value
	 * @param int $lifetime_in_seconds
	 * @return bool TRUE on success, FALSE otherwise 
	 */
	public static function add($key, $value, $lifetime_in_seconds) {
		if (self::$delegate instanceof Memcache) {
			return self::$delegate->add($key, $value, false, $lifetime_in_seconds);
		}
		else {
			return self::$delegate->add($eky, $value, $lifetime_in_seconds);
		}		
	}
	
	/**
	 * Set data on server
	 * 
	 * @param string $key 
	 * @param mixed $value
	 * @param int $lifetime_in_seconds
	 */
	public static function set($key, $value, $lifetime_in_seconds) {
		if (self::$delegate instanceof Memcache) {
			self::$delegate->set($key, $value, false, $lifetime_in_seconds);
		}
		else {
			self::$delegate->set($eky, $value, $lifetime_in_seconds);
		}
	}
	
	/**
	 * Remove data
	 * 
	 * @param string $key
	 */
	public static function delete($key) {
		self::$delegate->delete($key);
	}
	
	/**
	 * Increment a counter
	 * 
	 * @param string $key
	 * @param int $by
	 * @return int New Value or FALSE if $key does not exists
	 */
	public static function increment($key, $by = 1) {
		var_dump($key, $by);
		return self::$delegate->increment($key, $by);
	}

	/**
	 * Decrement a counter
	 * 
	 * @param string $key
	 * @param int $by
	 * @return int New Value or FALSE if $key does not exists
	 */
	public static function decrement($key, $by = 1) {
		return self::$delegate->decrement($key, $by);
	}
} 
