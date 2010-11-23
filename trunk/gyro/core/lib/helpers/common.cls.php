<?php
/**
 * Helper methods for PHP
 *
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Common {
	/**
	 * Returns true, if PHP runs as CGI (and not mod_php)
	 * 
	 * @return unknown_type
	 */
	public static function is_cgi() {
		return 	( substr(php_sapi_name(), 0, 3) == 'cgi' );				
	}
	
	/**
	 * Isusses a header with given http code
	 */
	public static function send_status_code($iStatusCode) {
		if (headers_sent()) {
			return;
		}
		$arr = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '[Unused]',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'		
		);
		if (isset($arr[$iStatusCode])) {
			$text = $iStatusCode . ' ' . $arr[$iStatusCode];
			if ( self::is_cgi() ) { 
				header('Status: ' . $text);
			}
			else {
				header('HTTP/1.x ' . $text);
			}
		}
		if ($iStatusCode >= 500 && $iStatusCode < 600) {
			 header('Retry-After: 120'); // Retry after 2 minutes
		}
	} 		
	
	/**
	 * Send a header
	 * 
	 * @since 0.5.1
	 * 
	 * @param string $name Name of header or complete header (if $value is false)
	 * @param string $value Value of header
	 * @param bool If FALSE, header will not be send, if header with same name was already sent
	 */
	public static function header($name, $value = false, $override = true) {
		$header = '';
		if ($value === false) {
			$tmp = self::split_header($name);
			$header = $name;
			$name = array_shift($tmp);
			$value = array_shift($tmp);
		}
		else {
			$header = $name . ':' . $value;
		}
		if ($override || !self::is_header_sent($name)) {
			header($header);
		}
	}
	
	/**
	 * Remove given header
	 */
	public static function header_remove($name) {
		if (function_exists('header_remove')) {
			header_remove($name);
		}
		else {
			self::header($name, '', true);
		}
	}

	/**
	 * Split header into array with name as first element, and value as second
	 *  
	 * @since 0.5.1
	 * 
	 * @return array
	 */
	public static function split_header($header) {
		$ret = explode(':', $header, 2);
		if (count($ret) == 1) {
			$ret = explode(' ', $header, 2);
		}
		if (count($ret) == 1) {
			$ret[] = '';
		}
		array_walk($ret, 'trim');
		return $ret;
	}
	
	/**
	 * Returns TRUE, if header with given name was alreday sent
	 * 
	 * @since 0.5.1
	 * 
	 * @param string $name
	 * @return bool
	 */
	public static function is_header_sent($name) {
		$ret = false;
		
		$name = strtolower($name) . ':';
		$l = strlen($name);
		foreach(headers_list() as $header) {
			$header = strtolower($header);
			if (substr($header, 0, $l) == $name) {
				$ret = true;
				break;
			}	
		}
		return $ret;
	}

	/**
	 * Get all send headers as assoziative array with (lower case) header name as key
	 * and full header as value
	 * 
	 * @since 0.5.1
	 * 
	 * @return array
	 */
	public static function get_headers() {
		$ret = array();
		foreach(headers_list() as $header) {
			$tmp = self::split_header($header);
			$ret[strtolower($tmp[0])] = $header;
		}
		return $ret;
	}
	
	/**
	 * Restore headers 
	 * 
	 * Headers not in passed array wil be removed, headers fro marray will be set
	 * 
	 * @param $arr_headers Array of headers retrieved by Common::get_headers()
	 */
	public static function header_restore($arr_headers) {
		$current = self::get_headers();
		// array_diff_key exists as of PHP 5.1.0 only
		foreach(array_diff(array_keys($current), array_keys($arr_headers)) as $name) {
			self::header_remove($name);
		}
		foreach($arr_headers as $name => $value) {
			self::header($value, '', true);
		}
	}
	
	/**
	 * Strips possible slashes added by magic quotes
	 */
	public static function preprocess_input() {
		// Is magic quotes on?
		if ( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() ) {
 			// Yes? Strip the added slashes
			$_REQUEST = self::transcribe($_REQUEST);
			$_GET = self::transcribe($_GET);
			$_POST = self::transcribe($_POST);
			$_COOKIE = self::transcribe($_COOKIE);
		}
	}

	// Taken from here: http://de.php.net/manual/de/function.get-magic-quotes-gpc.php#49612
	private static function transcribe($aList, $aIsTopLevel = true) {
	    $gpcList = array();
	    foreach ($aList as $key => $value) {
    	    if (is_array($value)) {
        	    $decodedKey = (!$aIsTopLevel) ? stripslashes($key) : $key;
            	$decodedValue = self::transcribe($value, false);
        	} else {
            	$decodedKey = stripslashes($key);
            	$decodedValue = stripslashes($value);
        	}
        	$gpcList[$decodedKey] = $decodedValue;
	    }
    	return $gpcList;
	}
	
	/**
	 * Returns value of constant, if defined, else default value
	 *
	 * @param string $name Name of constant
	 * @param mixed $default Default value
	 * @return mixed
	 */
	public static function constant($name, $default = false) {
		return (defined($name)) ? constant($name) : $default; 
	}
	
	/**
	 * Returns true if bitflag is set on value
	 *
	 * @param int $value
	 * @param int $flag
	 * @return bool
	 */
	public static function flag_is_set($value, $flag) {
		if ($flag == 0) {
			return ($value == 0);
		}
		return (($value & $flag) == $flag);
	}
	
	/**
	 * Checks if Modified-Since is sended and sends a 304, if larger then passed date.
	 * Returns true if header was send
	 */
	public static function check_not_modified($date) {
		// Get client headers - Apache only
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
 			// Split the If-Modified-Since (Netscape < v6 gets this wrong)
 			$modifiedSince = explode(';', $_SERVER['HTTP_IF_MODIFIED_SINCE']);

 			// Turn the client request If-Modified-Since into a timestamp
 			$modifiedSince = strtotime($modifiedSince[0]);
		} 
		else {
 			// Set modified since to 0
 			$modifiedSince = 0;
		}

		// Compare time the content was last modified with client cache
		if ($date <= $modifiedSince) {
 			// Save on some bandwidth!
 			// self::header_restore(array());
			Common::send_status_code(304); // Not modified
 			exit; 		
 		}
 		
 		return false;
	}

	/**
	 * Check if If-None-Match header is set and if it matches the given ETag
	 * If so, return a "304 Not Modifed" HTTP header
	 */
	public static function check_if_none_match($etag) {
		// Get client headers - Apache only
		$match_tag = Arr::get_item($_SERVER, 'HTTP_IF_NONE_MATCH', '');
		if ($match_tag && $match_tag == $etag) {
 			// Save on some bandwidth!
 			//self::header_restore(array());
 			Common::send_status_code(304); // Not modified
 			exit; 		
 		}
 		
 		return false;		
	}
	
	public static function is_google() {
		if (isset($_SERVER["HTTP_USER_AGENT"]))
			return (strpos($_SERVER["HTTP_USER_AGENT"], "Googlebot") !== false);
		else
			return false;
	}	
}