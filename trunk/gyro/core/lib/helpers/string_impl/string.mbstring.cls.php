<?php
/**
 * String class using mbstring stuff
 */
class StringMBString {
	/**
	 * Convert input to current charset
	 * 
	 * @param string $value Input to convert
	 * @return string 
	 */
	public function convert($value, $from = false, $to = false) {
		if (empty($to)) { $to = GyroLocale::get_charset(); }
		
		$ret = $value;
		if (empty($from)) {
			if (!mb_check_encoding($value, $to)) {
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
		return mb_strtolower($val, GyroLocale::get_charset());
	}
	
	/**
	 * Character set aware strtoupper()
	 * 
	 * @param String Value to convert into upper case 
	 * 
	 * @return String converted string
	 */
	public function to_upper($val) {
		return mb_strtoupper($val, GyroLocale::get_charset());
	}
	
	/**
	 * Character set aware strlen()
	 */
	public function length($val) {
		return mb_strlen($val, GyroLocale::get_charset());
	}

	public function strpos($haystack, $needle, $offset = NULL) {
		return mb_strpos($haystack, $needle, $offset, GyroLocale::get_charset());
	}

	public function stripos($haystack, $needle, $offset = NULL) {
		return mb_stripos($haystack, $needle, $offset, GyroLocale::get_charset());
	}

	public function strrpos($haystack, $needle) {
		if ($haystack == '') {
			return false;
		}
		return mb_strrpos($haystack, $needle, GyroLocale::get_charset());
	}
	
	/**
	 * Character set aware substr
	 */
	public function substr($val, $start = 0, $length = NULL) {
		if ($length === NULL) {
			$length = $this->length($val);
		}
		return mb_substr($val, $start, $length, GyroLocale::get_charset());
	}
}