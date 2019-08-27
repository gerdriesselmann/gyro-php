<?php
function tidy_check_preconditions() {
	$ret = new Status();
	if (!function_exists('tidy_parse_string')) {
		if (!GYRO_TIDY_IGNORE_NOT_INSTALLED) {
			$ret->append(
				'Tidy is not install, please enable it in the PHP config'
			);
		}
	}	
	return $ret;
}
