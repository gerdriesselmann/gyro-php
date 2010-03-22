<?php
function lib_identifrac_check_preconditions() {
	$ret = new Status();
	if (!function_exists('imagecreatetruecolor')) {
		$ret->append('Please enable the gd extension: http://php.net/manual/en/book.image.php');
	}	
	return $ret;
}
