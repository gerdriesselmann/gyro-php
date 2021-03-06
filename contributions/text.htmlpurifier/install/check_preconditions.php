<?php
function text_htmlpurifier_check_preconditions() {
	$ret = new Status();
	$basedir = Config::get_value(Config::TEMP_DIR) . 'htmlpurifier/';
	if (!file_exists($basedir)) {
		mkdir($basedir);
		chmod($basedir, 0777);
	}
	
	$dirs = array('CSS', 'HTML', 'URI');
	foreach($dirs as $dir) {
		$dir = $basedir . $dir;
		if (!file_exists($dir)) {
			mkdir($dir);
			chmod($dir, 0777);
		}
		if (!is_dir($dir)) {
			$ret->append("Directory $dir missing");
		}	
	}
	return $ret;
}
