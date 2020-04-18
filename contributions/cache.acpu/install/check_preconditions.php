<?php
/** 
 * Check if ACPu is active
 */
function cache_acpu_check_preconditions() {
	$ret = new Status();
	if (!function_exists('apcu_store')) {
		$ret->merge('APCu is not enabled - ACPu cache persister will not work!');
	}
	return $ret;
}
