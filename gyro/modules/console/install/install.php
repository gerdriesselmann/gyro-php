<?php
/**
 * Copy example files to app directory
 * 
 * You may want to change modes or file name afterwards
 */
function console_install() {
	$ret = new Status();
	$target = APP_INCLUDE_ABSPATH . 'run_console.php';
	if (!file_exists($target)) {
		$src = dirname(__FILE__) . '/run_console.php.example';
		if (!copy($src, $target)) {
			$ret->merge(tr('Could not create %target. Please do it manually', 'console', array('%target' => $target)));
		}
		else {
			if (!chmod($target, 0744)) {
				$ret->merge(tr('Could not chmod %target to be executable', 'console', array('%target' => $target)));
			}
		}
	}
	return $ret;
}
