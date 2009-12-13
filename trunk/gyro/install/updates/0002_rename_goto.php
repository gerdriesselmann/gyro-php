<?php
function core_update_2() {
	$ret = new Status();
	// Replace History::goto with History::go_to => PHP 5.3 compatability
	$dir_iterator = new RecursiveDirectoryIterator(APP_INCLUDE_ABSPATH);
	$iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
	foreach ($iterator as $file) {
	    if ($file->isFile()) {
	    	$path = $file->getPathName();
	    	if (substr($path, -4) == '.php') {
	    	    $content = file_get_contents($path);
	    	    $count = 0;
	    	    $content = str_replace('History::goto', 'History::go_to', $content, $count);
	    	    if ($count) {
	    	    	if (file_put_contents($path, $content) === false) {
	    	    		$ret->append("Could not update file $path");
	    	    		break;
	    	    	}
	    	    }
	    	}
	    }
    }
    
    return $ret;
}
