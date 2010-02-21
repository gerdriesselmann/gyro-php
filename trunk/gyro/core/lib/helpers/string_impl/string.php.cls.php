<?php
/**
 * String class using php functions
 */
class StringPHP {
	/**
	 * Convert input to current charset
	 * 
	 * @param string $value Input to convert
	 * @return string 
	 */
	public function convert($value) {
		$ret = $value;
		return $ret;
	}	
	
	/**
	 * Character set aware strtolower()
	 * 
	 * @param String Value to convert into lowercase
	 * 
	 * @return String converted string
	 */
	public function to_lower($val) {
		return strtolower($val);
	}	
	
	/**
	 * Character set aware strtoupper()
	 * 
	 * @param String Value to convert into upper case
	 * 
	 * @return String converted string
	 */
	public function to_upper($val) {
		return strtoupper($val);
	}
	
	/**
	 * Character set aware strlen()
	 */
	public function length($val) {
		return strlen($val);
	}

	public function strpos($haystack, $needle, $offset = NULL) {
		return strpos($haystack, $needle, $offset);
	}

	public function stripos($haystack, $needle, $offset = NULL) {
		return stripos($haystack, $needle, $offset);
	}

	public function strrpos($haystack, $needle) {
		if ($haystack == '') {
			return false;
		}
		return strrpos($haystack, $needle);
	}

	/**
	 * Character set aware substr
	 */
	public function substr($val, $start = 0, $length = NULL) {
		if ($length === NULL) {
			$length = $this->length($val);
		}
		return substr($val, $start, $length);
	}
}