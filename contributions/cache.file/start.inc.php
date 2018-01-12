<?php
/**
 * @defgroup FileCache
 * @ingroup Cache
 * 
 * Replace DB-based cache and sessions with file based - that is: store cache in filesystem.
 *
 * Only useful with RAM-disks
 *
 * @section Usage
 *
 * When enabled, the cache persistence gets changed to a directory.
 *
 * Directory must be set (with trailing /) through constant APP_FILE_CACHE_DIR
 */

require_once __DIR__ . '/cache.file.impl.php';
Cache::set_implementation(new CacheFileImpl());
