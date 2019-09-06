<?php
/**
 * This class works around some limitations in PHP header handling
 * 
 * LImitations are:
 * 
 * - headers cannot be removes prior to PHP 5.3
 * - if headers are removed, one cannot tell, so they may be set from other parts of application
 */
class GyroHeaders {
	/**
	 * Associative array of name and value. 
	 * 
	 * If value is empty string, it indicated header will not be sent
	 * 
	 * @var array
	 */
	private static $headers = array();
	
	/**
	 * Set a header
	 * 
	 * @param string $name Name of header or complete header (if $value is false)
	 * @param string $value Value of header
	 * @param bool If FALSE, header will not be set, if header with same name was already set
	 */
	public static function set($name, $value = false, $override = true) {
		if ($value === false) {
			$tmp = self::split($name);
			$name = array_shift($tmp);
			$value = array_shift($tmp);
		}
		if ($override || !self::is_set($name)) {
			self::$headers[$name] = $value;	
		}
	}

	/**
	 * Append to a given header or create a new one
	 *
	 * @param string $name Name of header or complete header (if $value is false)
	 * @param string $value Value of header
	 */
	public static function append($name, $value = false) {
		if ($value === false) {
			$tmp = self::split($name);
			$name = array_shift($tmp);
			$value = array_shift($tmp);
		}
		if (!self::is_set($name)) {
			self::set($name, $value, true);
		} else {
			$existing = self::$headers[$name];
			self::$headers[$name] = $existing . ', ' . $value;
		}
	}

	/**
	 * Remove given header
	 */
	public static function remove($name) {
		self::$headers[$name] = '';		
	}
	
	/**
	 * Returns TRUE, if header with given name was alreday set or send
	 * 
	 * @param string $name
	 * @return bool
	 */	
	public static function is_set($name) {
		$ret = false;
		
		$name = strtolower($name);
		foreach(self::$headers as $hdr => $value) {
			$hdr = strtolower($hdr);
			if ($hdr == $name) {
				$ret = true;
				break;
			}	
		}
		return $ret;		
	}
	
	/**
	 * Get all set headers as assoziative array with (lower case) header name as key
	 * and full header as value
	 * 
	 * @return array
	 */
	public static function headers() {
		$ret = array();
		foreach(self::$headers as $name => $value) {
			if ($value) {
				$value = $name . ': ' . $value; 
			}
			$ret[strtolower($name)] = $value;
		}
		return $ret;
	}
	
	/**
	 * Get all set headers as assoziative array with (lower case) header name as key
	 * and value as value
	 * 
	 * @return array
	 */
	public static function values() {
		$ret = array();
		foreach(self::$headers as $name => $value) {
			$ret[strtolower($name)] = $value;
		}
		return $ret;
	}

	/**
	 * Restore headers 
	 * 
	 * Headers not in passed array wil be removed, headers from array will be set
	 * 
	 * @attention Expects array
	 * 
	 * @param $arr_headers Array of headers with header name as key and full header as value
	 */
	public static function restore($arr_headers) {
		$current = self::headers();
		// array_diff_key exists as of PHP 5.1.0 only
		foreach(array_diff(array_keys($current), array_keys($arr_headers)) as $name) {
			self::remove($name);
		}
		foreach($arr_headers as $name => $value) {
			self::set($value, false, true);
		}
	}
	
	/**
	 * Read headers send into internal array
	 * 
	 * @return void
	 */
	public static function sync() {
		foreach(headers_list() as $header) {
			self::set($header, false, false);
		}
	}
	
	/**
	 * Send all headers set
	 * 
	 * @throws Exception if headers were already sent
	 * @return void
	 */
	public static function send() {
		$file = '';
		$line = 0;
		if (headers_sent($file, $line)) {
			throw new Exception('GyroHeaders: Headers already sent in file "' . $file . '" on line ' . $line);
		}
		$has_remove = function_exists('header_remove');
		foreach(self::$headers as $name => $value) {
			if ($value) {
				header($name . ': ' . $value);
			}
			else if ($has_remove) {
				// As of PHP 5.3
				header_remove($name);
			} 
		}
	}

	/**
	 * Split header into array with name as first element, and value as second
	 * 
	 * @return array
	 */
	public static function split($header) {
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
}