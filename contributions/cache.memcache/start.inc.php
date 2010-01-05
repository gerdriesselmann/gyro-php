<?php
/**
 * @defgroup Memcache
 * @ingroup Cache
 * 
 * Replace DB-based cache and sessions with Memcache based - that is: store cache in memory 
 *
 * @section Usage
 *
 * When enabled, the cache and session persistence gets changed to Memcache. Nothing else to do. 
 *
 * @section Notes Additional notes
 *
 * This module requires either memcache or memcached extension to be installed and 
 * configured properly. Check especially xcache.var_size in xcache config! 
 */
require_once dirname(__FILE__) . '/cache.memcache.impl.php';
Cache::set_implementation(new CacheMemcacheImpl());

