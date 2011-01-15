<?php
/**
 * Validate several values
 * 
 * @todo Can be transformed into an adapter to filter extension with PHP 5.2
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Validation {
	/**
	 * Check if value is a valid e-mail address
	 */
	public static function is_email($value) {
		$parts = explode('@', $value, 2);
		$local_part = array_shift($parts);
		$domain = array_shift($parts);
		
		$ret = self::is_domain($domain);
		// local part may be up to 64 characters 
		$ret = $ret && (strlen($local_part) <= 64);
		// dot is not allowed at the end or beginning
		// There is also a rule that 2 or more dots are illegal like in 'forname..lastname@web.de'
		// Unfortunately: my neighbor's address IS forname..lastname@web.de! And I can't lock my neighbor 
		// out of the services I program, can I? 
		$ret = $ret && (substr($local_part, 0, 1) !== '.');
		$ret = $ret && (substr($local_part, -1) !== '.');
		// Only a-z, A-Z, 0-9 and !#$%&'*+-/=?^_`{|}~ and . are allowed
		// (There is quoting and escaping, but we do not hear, we do not hear, we do not hear...)
		$pattern = "@^[a-zA-Z0-9!#$%&'*+\-/=?^_`{|}~.]+$@s";
		$ret = $ret && preg_match($pattern, strtr($local_part, "\r\n", '  '));
		
		return $ret;
	}
	
	/**
	 * Check if string is s domai name. Does not check for valid TLD, use Url validation for that 
	 */
	public static function is_domain($value) {
		$ret = true;
		$ret = $ret && (strlen($value) <= 255);
		
		$elements = explode('.', $value);
		$ret = $ret && (count($elements) > 1);

		// Check elements
		foreach($elements as $e) {
			$l = strlen($e);
			$ret = $ret && ($l > 0);
			$ret = $ret && ($l <= 63);
			$ret = $ret && preg_match('|^[^@ ]+$|', $e);
		}  
		
		// Check TLD
		$tld = array_pop($elements);
		$ret = $ret && !preg_match('|^\d+$|', $tld); // TLD not only numbers 
		
		return $ret;
	}
	
	/**
	 * Check if value is a string and validate its length
	 * 
	 * @param 
	 */
	public static function is_string_of_length($value, $min_length = 0, $max_length = -1) {
		if (is_null($value)) {
			$value = '';
		}
		$ret = is_string($value);
		if ($ret) {
			$ret = $ret && ( String::length($value) >= $min_length);
			if ($max_length > 0) {
				$ret = $ret && ( String::length($value) <= $max_length); 
			} 
		}
		return $ret;
	}
	
	/**
	 * Check if value is an url
	 */
	public static function is_url($value) {
		$test = new Url($value);
		return $test->is_valid();
	}
	
	/**
	 * Check if value is a double within given range
	 * 
	 * @param mixed The value to check
	 * @param mixed Min value or false to not check for min value 
	 * @param mixed Max value or false to not check for max value 
	 */
	public static function is_double($value, $min = false, $max = false) {
		$ret = is_numeric($value);
		if ($ret) {
			$value = Cast::float($value);
			if ($min !== false) {
				$ret = $ret && ($value >= $min);
			}
			if ($max !== false) {
				$ret = $ret && ($value <= $max);
			}
		}
		return $ret;
	}

	/**
	 * Check if value is a int within given range
	 * 
	 * @param mixed The value to check
	 * @param mixed Min value or false to not check for min value 
	 * @param mixed Max value or false to not check for max value 
	 * @return bool
	 */
	public static function is_int($value, $min = false, $max = false) {
		$ret = ($value == strval(intval($value)));
		if ($ret) {
			$value = Cast::int($value);
			if ($min !== false) {
				$ret = $ret && ($value >= $min);
			}
			if ($max !== false) {
				$ret = $ret && ($value <= $max);
			}
		}
		return $ret;
	}
	
	/**
	 * Check if $ip is a valid IPv4 address in dotted decimal format
	 * 
	 * If the validation succeeds, the given string is replaced by a _very_
	 * well formatted version, that will even pass tests that check for 3 digits 
	 * 
	 * Example: 192.0168.1.2 is valid, but a lot of IP tests will fail because of 0168.
	 * This function will pass, and set $ip to 192.168.1.2
	 * 
	 * @attention Note that Octal, Hexadecimal, Decimal, Dotted Hexadecimal and Dotted Octal are not supported.
	 *            They are rarely used, though
	 */
	public static function is_ip4(&$ip) {
		// Using filter_var here fails in recognizing 255.255.255.0255 as a valid IP
		// See http://en.wikipedia.org/wiki/IPv4#Address_representations
		$test = explode('.', $ip);
		$ints = array();
		$ret = (count($test) == 4);
		foreach($test as $datum) {
			$ret = $ret && self::is_int($datum, 0, 255);
			if ($ret) { $ints[] = intval($datum); }
		}	
		
		$regex = '@^[0.]*$@'; // Contains only ., and 0
		$ret = $ret && !preg_match($regex, $ip);
		
		if ($ret) {
			$ip = implode('.', $ints);
		}

		return $ret;
	}
	
	/**
	 * Checks if given string is an IPv6
	 */
	public static function is_ip6($ip) {
		$ret = false;
		if (function_exists('filter_var')) {
			// This regards :: as valid, also ::0 or ::0.0.0.0 as OK, which is wrong
			$ret = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
		}
		else {
			// This regards :: as invalid, but ::0 or ::0.0.0.0 as OK, which is wrong
			// Taken from here: http://regexlib.com/REDetails.aspx?regexp_id=1000
			$regex = "@^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){1,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){1}:([0-9A-Fa-f]{1,4}:){0,4}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,2}:([0-9A-Fa-f]{1,4}:){0,3}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,3}:([0-9A-Fa-f]{1,4}:){0,2}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,4}:([0-9A-Fa-f]{1,4}:){1}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$@";
			$ret = preg_match($regex, $ip);
		}	
		if ($ret) {
			// :: in any combination (::, ::0, 0::0:0.0.0.0) is invalid!
			$regex = '@^[0.:]*$@'; // Contains only ., :, and 0
			$ret = !preg_match($regex, $ip);
		}
		return $ret;
	}
	
	/**
	 * Check if given string is either a IPv4 or IPv6
	 */
	public static function is_ip(&$ip) {
		return self::is_ip4($ip) || self::is_ip6($ip);
	}
	
}
