<?php
/**
 * @defgroup XCache
 * @ingroup Cache
 * 
 * Replace DB-based cache and sessions with XCache based - that is: store cache in memory 
 *
 * @section Usage
 *
 * When enabled, the cache persistence gets changed to XCache. Nothing else to do. 
 *
 * @section Notes Additional notes
 *
 * This module requires XCache (http://xcache.lighttpd.net/) to be installed and 
 * configured properly. Check especially xcache.var_size in xcache config! 
 */
require_once dirname(__FILE__) . '/cache.xcache.impl.php';
Cache::set_implementation(new CacheXCacheImpl());

