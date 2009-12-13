<?php
function tidy_check_preconditions() {
	$ret = new Status();
	if (!function_exists('tidy_parse_string')) {
		$ret->append('Tidy is not install, please install the tidy PECL extension: http://pecl.php.net/package/tidy');
	}	
	return $ret;
}
