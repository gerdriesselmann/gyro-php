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
		// Taken from http://www.regular-expressions.info/email.html
		// $pattern = "/^[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/";
		// Very simple assume something like [not a space]@[domain].[domain]{.[domain]*}
		$pattern = "|^[^ @]+@[^ .@]+\.[^ @]*$|";
		return preg_match($pattern, $value);
		
		//return eregi("^[_\+a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $value);
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
}
