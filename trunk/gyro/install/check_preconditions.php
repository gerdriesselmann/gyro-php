<?php
function core_check_preconditions() {
	$ret = new Status();
	
	$tempdir = Config::get_value(Config::TEMP_DIR);
	$subdirs = array(
		'',
		'log', 
		'view', 'view/templates_c'
	);
	foreach($subdirs as $subdir) {
		$dir = $tempdir . $subdir;
		if (!file_exists($dir)) {
			if (!mkdir($pathname, 0777, true)) {
				$ret->append('Could not create temporary directory ' . $dir);			
			}
		}
		// Try to place file into temp dir
		$file = $dir . '/test' . md5(uniqid());
		if (touch($file)) {
			unlink($file);
		}
		else {
			$ret->append('Could not create file in temporary directory ' . $dir);
		}
	}
	
	return $ret;
}
