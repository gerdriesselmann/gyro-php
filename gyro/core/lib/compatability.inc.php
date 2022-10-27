<?php
/**
 * Compatability layer
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */

// PHP_VERSION_ID is available as of PHP 5.2.7, if our 
// version is lower than that, then emulate it
// As suggested her: http://us2.php.net/manual/en/function.phpversion.php
if(!defined('PHP_VERSION_ID')) {
    $version = PHP_VERSION;

    define('PHP_VERSION_ID', (intval($version[0]) * 10000 + intval($version[2]) * 100 + intval($version[4])));
}
if(PHP_VERSION_ID < 50207) {
    define('PHP_MAJOR_VERSION',     $version[0]);
    define('PHP_MINOR_VERSION',     $version[2]);
    define('PHP_RELEASE_VERSION',     $version[4]);
}

if( !function_exists('memory_get_usage') ) {
	// Taken from http://de3.php.net/memory_get_usage
	function memory_get_usage() {
		//If its Windows
		//Tested on Win XP Pro SP2. Should work on Win 2003 Server too
		//Doesn't work for 2000
		//If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
		$output = array();
		if ( substr( PHP_OS, 0, 3 ) == 'WIN' ) {
			exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
			return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
		}
		else {
			//We now assume the OS is UNIX
			//Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
			//This should work on most UNIX systems
			$pid = getmypid();
			exec("ps -o rss -p $pid", $output);
			//rss is given in 1024 byte units
			return $output[1] * 1024;
		}
	}
}

if (!function_exists('memory_get_peak_usage')) {
	function memory_get_peak_usage($real_usage = null) {
		return memory_get_usage($real_usage);
	}
}

/**
 * Wrapper around glob that does not return false on empty directories
 *
 * @param string $path
 * @param int $flags
 */
function gyro_glob($path, $flags = 0) {
	$ret = glob($path, $flags);
	if ($ret === false) {
		$ret = array();
	}
	return $ret;
}

if (Config::get_value(Config::VERSION_MAX) < 0.4) {
	// Old define style 
	require_once GYRO_CORE_DIR . 'lib/compatability/defines.0.3.inc.php';
}