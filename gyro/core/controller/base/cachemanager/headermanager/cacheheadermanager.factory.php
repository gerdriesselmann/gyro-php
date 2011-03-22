<?php
/**
 * Simple factory for cache header manager
 */
class CacheHeaderManagerFactory {
	/**
	 * Create instance from class name stub.
	 * 
	 * Stub is class name without CacheHeaderManager, e.g. FullCache for FullCacheCacheHeaderManager
	 * 
	 * @return ICacheHeaderManager 
	 */
	public static function create($stub) {
		$cls = $stub  . 'CacheHeaderManager';
		if (class_exists($cls)) {
			return new $cls();
		}
		throw new Exception('Unknown cache header manager: ' . $stub);
	}
}