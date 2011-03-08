<?php
function core_check_preconditions() {
	$ret = new Status();
	
	$tempdir = realpath(Config::get_value(Config::TEMP_DIR)) . '/';
	$subdirs = array(
		'',
		'log', 
		'view', 'view/templates_c'
	);
	foreach($subdirs as $subdir) {
		$dir = rtrim($tempdir . $subdir, '/');
		if (!file_exists($dir)) {
			$cmd = 'mkdir -p ' . $dir;
			if (shell_exec($cmd)) { 
				$ret->append('Could not create temporary directory ' . $dir);			
			}
			else {
				chmod($dir, 0777);
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
