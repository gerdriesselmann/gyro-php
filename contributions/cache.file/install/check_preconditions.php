<?php
/** 
 * Check if memcache or memcached are installed
 */
function cache_file_check_preconditions() {
	$ret = new Status();
	$cache_base_dir = Config::get_value(ConfigFileCache::CACHE_DIR);
	$subdirs = array(
		'cache',
		'cache/' . GyroString::plain_ascii(Config::get_url(Config::URL_DOMAIN)),
		'sessions'
	);
	foreach($subdirs as $subdir) {
		$dir = $cache_base_dir . $subdir;
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
	}

	return $ret;
}
