<?php
/**
 * String class using mbstring stuff
 */
class StringMBString {
	/**
	 * Check if given string matches current encoding
	 * 
	 * @param string $value Value to check
	 * @param string $encoding Encoding to check against. Use FALSE for current encoding
	 * @return bool
	 */
	public function check_encoding($value, $encoding = false) {
		if (empty($encoding)) { 
			$encoding = GyroLocale::get_charset(); 
		}
		return mb_check_encoding($value, $encoding);
	}
	
	/**
	 * Convert input to current charset
	 * 
	 * @param string $value Input to convert
	 * @param string $from Encoding to convert from. Use FALSE for auto-detecting encoding of $value
	 * @param string $to Encoding to covert to. Use FALSE for current encoding set on GyroLocale
	 * @return string Converted Value
	 */
	public function convert($value, $from = false, $to = false) {
		if (empty($to)) { $to = GyroLocale::get_charset(); }
		
		$ret = $value;
		if (empty($from)) {
			// Autodetecting
			if (!$this->check_encoding($value, $to)) {
				$ret = mb_convert_encoding($value, $to);
			}
		}
		else {
			$ret = mb_convert_encoding($value, $to, $from);
		}
		return $ret;
	}	
	
	/**
	 * Character set aware strtolower()
	 * 
	 * @param String Value to convert into lowercase
	 * @param Integer Number of chars to convert, 0 for all.
	 * 
	 * @return String converted string
	 */
	public function to_lower($val) {
		return mb_strtolower($this->safeval($val), GyroLocale::get_charset());
	}
	
	/**
	 * Character set aware strtoupper()
	 * 
	 * @param String Value to convert into upper case 
	 * 
	 * @return String converted string
	 */
	public function to_upper($val) {
		return mb_strtoupper($this->safeval($val), GyroLocale::get_charset());
	}
	
	/**
	 * Character set aware strlen()
	 */
	public function length($val) {
		return mb_strlen($this->safeval($val), GyroLocale::get_charset());
	}

	public function strpos($haystack, $needle, $offset = NULL) {
		return mb_strpos($haystack, $needle, $offset, GyroLocale::get_charset());
	}

	public function stripos($haystack, $needle, $offset = NULL) {
		return mb_stripos($haystack, $needle, $offset, GyroLocale::get_charset());
	}

	public function strrpos($haystack, $needle) {
		if (is_null($haystack) || $haystack == '') {
			return false;
		}
		if (PHP_VERSION_ID < 50200) {
			// Before PHP 5.2 charset is at 3rd position
			return mb_strrpos($haystack, $needle, GyroLocale::get_charset());
		} else {
			// afterwards at 4th
			return mb_strrpos($haystack, $needle, 0, GyroLocale::get_charset());
		}
	}
	
	/**
	 * Character set aware substr
	 */
	public function substr($val, $start = 0, $length = NULL) {
		$safe_val = $this->safeval($val);
		if ($length === NULL) {
			$length = $this->length($safe_val);
		}
		return mb_substr($safe_val, $start, $length, GyroLocale::get_charset());
	}

	/**
	 * PHP 8.x does not allow NULL anymore, so clean possible wroing input
	 *
     * @param string $val
	 * @return string
	 */
	private function safeval($val) {
		return is_null($val) ? '' : $val;
	}
}