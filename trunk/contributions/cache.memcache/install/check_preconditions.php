<?php
/** 
 * Check if memcache or memcached are installed
 */
function cache_memcache_check_preconditions() {
	$ret = new Status();
	if (!class_exists('Memcache') && !class_exists('Memcached')) {
		$ret->append('Memcache or Memcached are not installed, please install the according PECL extensions: http://pecl.php.net/package/memcached, http://pecl.php.net/package/memcache');
	}
	return $ret;
}
