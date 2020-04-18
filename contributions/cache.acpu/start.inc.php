<?php
/**
 * @defgroup ACPu
 * @ingroup Cache
 * 
 * Replace DB-based cache and sessions with ACPu based - that is: store cache in memory 
 *
 * @section Usage
 *
 * When enabled, the cache persistence gets changed to ACPu. Nothing else to do. 
 *
 * @section Notes Additional notes
 *
 * This module requires ACPu (https://www.php.net/manual/en/book.apcu.php) to be installed and
 * configured properly.
 */
require_once dirname(__FILE__) . '/cache.acpu.impl.php';
Cache::set_implementation(new CacheACPuImpl());

