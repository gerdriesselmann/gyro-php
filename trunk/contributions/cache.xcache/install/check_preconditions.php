<?php
/** 
 * Check if XCache is active
 */
function cache_xcache_check_preconditions() {
	$ret = new Status();
	if (!function_exists('xcache_set')) {
		$ret->merge('XCache is not enabled - XCache cache persister will not work!');
	}
	return $ret;
}
