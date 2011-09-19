<?php
define("STRING_SANITIZE_NONE", 0);
define("STRING_SANITIZE_DB", 1);
define("STRING_SANITIZE_HTML", 2);

/**
 * Wraps string functions, calls mb_ functions, if available
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class String {
	const HTML = 'html';
	const XML = 'xml';
	
	public static $impl;
	
	/**
	 * Static. processes a string to strip of HTML and quotes. Avoids injection attacks
	 *
	 * @attention Content is escaped after stripping tags, so you should not call this
	 *            function just to strip tags. Use it to clean user input.
	 *
	 * @param String The text to process
	 * @return String The cleaned text
	 */
	public static function clear_html($val) {
		return htmlspecialchars(strip_tags($val), ENT_QUOTES, GyroLocale::get_charset());
	}

	/**
	 * Static. Preprocesses a string to not contain "<", ">" and other special chars
	 *
	 * @param String The text to process
	 * @return String The cleaned text
	 */
	public static function escape($val, $target = self::HTML) {
		if ($target === self::HTML) {
			return htmlentities(trim($val), ENT_QUOTES, GyroLocale::get_charset());
		}
		else {
			return htmlspecialchars(trim($val), ENT_QUOTES, GyroLocale::get_charset()); 
		}
	}

	/**
	 * Static. Preprocesses a string so &gt; and similar get transformed to real characte4re
	 *
	 * @param String The text to process
	 * @return String The cleaned text
	 */
	public static function unescape($val) {
		return html_entity_decode(trim($val), ENT_QUOTES, GyroLocale::get_charset());
	}
	
	/**
	 * Check if given string matches current encoding
	 * 
	 * @param string $value Value to check
	 * @param string $encoding Encoding to check against. Use FALSE for current encoding
	 * @return bool
	 */
	public static function check_encoding($value, $encoding = false) {
		return self::$impl->check_encoding($value, $encoding); 
	}
	
	/**
	 * Convert input charset
	 * 
	 * @attention 
	 *   Note that charset autodetection usually requries the according charsets to be installed on your system.
	 *   If you use UTF-8, e.g. a UTF-8 locale should be installed. While on Windows, this is usually the case, 
	 *   you may want to check this on Linux by invoking "locale -a" on the command line.
	 * 
	 * @param string $value Input to convert
	 * @param string $from Charset to convert from. If empty, system tries to autodetect it (may fail, though)
	 * @param string $to Charset to convert to, if empty charset set on GyroLocale is used 
	 * @return string 
	 */
	public static function convert($value, $from = false, $to = false) {
		return self::$impl->convert($value, $from, $to);
	}
	
	/**
	 * Static. Convert float value into a currency string according to locale settings
	 *
	 * @param float The numeric value
	 * @param bool True to include unit, false to obey
	 * @return String The formatted string, e.g. "$10.20"
	 */
	public static function currency($dbl, $includeUnit = true) {
		$locale_info = localeconv();
		$thousands_sep = Arr::get_item($locale_info, 'mon_thousands_sep', null);
		if (empty($thousands_sep)) {
			$thousands_sep = Arr::get_item($locale_info, 'thousands_sep', ',');
		}
		$decimal_point = Arr::get_item($locale_info, 'mon_decimal_point', null);
		if (empty($decimal_sep)) {
			$decimal_point = Arr::get_item($locale_info, 'decimal_point', '.');
		}
		$ret = number_format($dbl, 2, $decimal_point, $thousands_sep);

		if ($includeUnit) {
			$currency_symbol = Arr::get_item($locale_info, 'currency_symbol', '$');
			$p_cs_precedes = Arr::get_item($locale_info, 'p_cs_precedes', true);
			$p_sep_by_space = Arr::get_item($locale_info, 'p_sep_by_space', true);

			if ($p_cs_precedes) {
				if ($p_sep_by_space) {
					$currency_symbol .= ' ';
				}
				$ret = $currency_symbol . $ret;
			}
			else {
				if ($p_sep_by_space) {
					$currency_symbol = ' ' . $currency_symbol;
				}
				$ret .= $currency_symbol;
			}
		}
		return $ret;
	}

	/**
	 * Static. Convert integer value into a string according to locale settings
	 *
	 * @param int The numeric value
	 * @return String The formatted string, e.g. "10,200"
	 */
	public static function int($int) 	{
		$int = Cast::int($int);
		if ($int < 10000) {
			return (string)$int;
		}
		else {
			return String::number($int, 0);
		}
	}

	/**
	 * Static. Convert numeric value into a string according to locale settings
	 *
	 * @param mixed The numeric value
	 * @param int Number of decimals
	 * @param boolean If true, C formatting is used
	 * @return String The formatted string, e.g. "10,200.67"
	 */
	public static function number($number, $decimals = 2, $system = false) {
   		$locale_info = ($system) ? false : localeconv();
		$thousands_sep = ($system) ? '' : Arr::get_item($locale_info, 'thousands_sep', ',');
		$decimal_point = ($system) ? '.' : Arr::get_item($locale_info, 'decimal_point', '.');
		return number_format(Cast::float($number), $decimals, $decimal_point, $thousands_sep);
	}

	/**
	 * Turns a string representing a number into a string representing a number in current locale
	 * 
	 * Example: In european countries decimal sep is a comma, so this will turn '2.45' into '2,45'
	 * 
	 * Be aware to not pass a number to this function twice!
	 *
	 * @param string $val
	 * @param string
	 */
	public static function localize_number($val) {
		// Convert C specific float value to locale float
		// e.g. in Germany: 1,000.30 to 1.000,30  
		$locale_info = localeconv();
		$thousands_sep = Arr::get_item($locale_info, 'thousands_sep', ',');
		$decimal_point = Arr::get_item($locale_info, 'decimal_point', '.');
		
		if ($decimal_point != '.') {
			// Strip thousands sep
			$val = str_replace(',', $thousands_sep, $val);
			// Convert decimal sep
			$val = str_replace('.', $decimal_point, $val);
		}				 
		return $val;		
	}
		
	/**
	 * Turns a string representing a number into a string representing a number in C locale
	 * 
	 * Example: In european countries decimal sep is a comma, not a dot, so while user enters 192,123
	 * this mus be transformed to 192.123 so it can be understand correctly by PHP , DB or else
	 * 
	 * Be aware to not pass a number to this function twice!
	 *
	 * @param string $val
	 * @param string
	 */
	public static function delocalize_number($val) {
		// Convert language specific float value to C float
		// e.g. in Germany: 1.000,30 to 1000.30  
		$locale_info = localeconv();
		$thousands_sep = Arr::get_item($locale_info, 'thousands_sep', ',');
		$decimal_point = Arr::get_item($locale_info, 'decimal_point', '.');
		
		if ($decimal_point != '.') {
			// Strip thousands sep
			$val = str_replace($thousands_sep, '', $val);
			// Convert decimal sep
			$val = str_replace($decimal_point, '.', $val);
		}				 
		return $val;		
	}
	
	/**
	 * Return string depending on value of $num
	 * 
	 * If $num is 1, $singuar is returned
	 * If $num is not 1, $plural is returned (%num gets replaced by $num)
	 * If $num is 0, and $none is not FALSE, $none is returned, else $plural is returned (%num gets replaced by $num)
	 *  
	 * @since 0.5.1
	 * 
	 * @return string
	 */
	public static function singular_plural($num, $singular, $plural, $none = false) {
		$plural = str_replace('%num', $num, $plural);
		switch ($num) {
			case 1:
				return $singular;
			case 0:
				return ($none !== false) ? $none : $plural;
			default: 
				return $plural;
		}
	}
	
	/**
	 * Character set aware strtolower()
	 * 
	 * @param String Value to convert into lowercase
	 * @param Integer Number of chars to convert, 0 for all.
	 * 
	 * @return String converted string
	 */
	public static function to_lower($val, $count = 0) {
		if ($count > 0) {
			return self::to_lower(self::substr($val, 0, $count)) . self::substr($val, $count);
		}
		else {
			return self::$impl->to_lower($val);
		}
	}

	/**
	 * Character set aware strtoupper()
	 * 
	 * @param String Value to convert into lowercase
	 * @param Integer Number of chars to convert, 0 for all.
	 * 
	 * @return String converted string
	 */
	public static function to_upper($val, $count = 0) {
		if ($count > 0) {
			return self::to_upper(self::substr($val, 0, $count)) . self::substr($val, $count);
		}
		else {
			return self::$impl->to_upper($val);
		}
	}

	/**
	 * Character set aware strlen()
	 */
	public static function length($val) {
		return self::$impl->length($val);
	}

	public static function strpos($haystack, $needle, $offset = NULL) {
		return self::$impl->strpos($haystack, $needle, $offset);
	}

	public static function stripos($haystack, $needle, $offset = NULL) {
		return self::$impl->stripos($haystack, $needle, $offset);
	}

	public static function strrpos($haystack, $needle) {
		return self::$impl->strrpos($haystack, $needle);
	}
	
	public static function contains($haystack, $needle) {
		return (self::strpos($haystack, $needle) !== false);
	}

	/**
	 * A unicode aware implementation of preg_replace
	 * 
	 * See apply_u_modifier() for a list of supported types and assertions
	 *
	 * @param string $pattern The patter to search for
	 * @param string $replacement The string to replace with
	 * @param string $subject The text to search
	 * @param integer $limit The number of replacements to do
	 * @param integer $count Filled with number of replacements
	 * @return integer Number of replacements
	 */
	public static function preg_replace($pattern, $replacement, $subject, $limit = -1, &$count = false) {
		self::apply_u_modifier($pattern);
		return preg_replace($pattern, $replacement, $subject, $limit, $count);
	}

	public static function preg_replace_callback($pattern, $callback, $subject, $limit = -1, &$count = false) {
		self::apply_u_modifier($pattern);
		return preg_replace_callback($pattern, $callback, $subject, $limit, $count);
	}	
	
	public static function preg_match($pattern, $subject, &$matches = array(), $flags = 0, $offset = 0) {
		self::apply_u_modifier($pattern);
		return preg_match($pattern, $subject, $matches, $flags, $offset);
	}

	public static function preg_match_all($pattern, $subject, &$matches = array(), $flags = 0, $offset = 0) {
		self::apply_u_modifier($pattern);
		return preg_match_all($pattern, $subject, $matches, $flags, $offset);
	}
	
	public static function preg_split($pattern, $subject, $limit = -1, $flags = 0) {
		self::apply_u_modifier($pattern);
		return preg_split($pattern, $subject, $limit, $flags);		
	}
	
	/**
	 * Modifies a regex pattern, if encoding id Unicode
	 * 
	 * Supports the \w and \W, \s and \S and \ and \D classes
	 * Supports the \b assertion, but only as 
	 *  - \b{<} (Word boundary before), 
	 *  - \b{>} (Word boundary after), and 
	 *  - \b{<>} (word boundary within)  
	 * 
	 * @param string $pattern
	 */
	private static function apply_u_modifier(&$pattern) {
		if (GyroLocale::get_charset() !== 'UTF-8') {
			return;
		}
		
		if (!function_exists('_append_u_modifier')) {
			/**
			 * Function to transform a non-unicode-suitable regex into a utf-8-compatible one
			 * 
			 * @param string $regex The regex to transform
			 * @return string
			 */
			function _append_u_modifier($regex) {
				if (GyroLocale::get_charset() == 'UTF-8') {
					$regex = str_replace('\b{<}', '(?<!\w)', $regex);
					$regex = str_replace('\b{>}', '(?!\w)', $regex);
					$regex = str_replace('\b{<>}', '(?!\w)(?<!\w)', $regex);
					$regex = str_replace('\W', '[^\pL\pN]', $regex);
					$regex = str_replace('\w', '[\pL\pN]', $regex);
					$regex = str_replace('\s', '[\pZ]', $regex);
					$regex = str_replace('\S', '[\PZ]', $regex);
					$regex = str_replace('\d', '[\pN]', $regex);
					$regex = str_replace('\D', '[\PN]', $regex);
					$regex .= 'u';
				}
				else {
					$regex = str_replace('\b{<}', '\b', $regex);
					$regex = str_replace('\b{>}', '\b', $regex);
					$regex = str_replace('\b{<>}', '\b', $regex);										
				}
				
				return $regex;
			}
		}
		
		if (is_array($pattern)) {
			$pattern = array_map('_append_u_modifier', $pattern);
		}
		else {
			$pattern = _append_u_modifier($pattern);
		}
	} 
	
	/**
	 * Character set aware substr
	 */
	public static function substr($val, $start = 0, $length = NULL) {
		return self::$impl->substr($val, $start, $length);
	}

	/**
	 * Get substr, but respect word boundaries
	 * 
	 * @param string $val 
	 * @param int $start Start of substr (usually 0)
	 * @param int $max_length Maximum length of substring
	 * @param bool $elipsis Append "..." to the string
	 */
	public static function substr_word($val, $start, $max_length, $elipsis = false) {
		$val .= ' ';
		$ret = self::substr($val, $start, $max_length);
		$pos = self::strrpos($ret, ' ');
		if ($pos === false) {
			// No space found in given substr.
			// Check if a space follows substring
			$test = self::substr($val, $start + $max_length, 1);
			if ($test != '' && $test != ' ') {
				$ret = '';
			}
		}
		else {
			$ret = self::substr($ret, 0, $pos);
		}
		if ($elipsis && $ret) {
			$ret .= '...';
		}

		return $ret;
	}
	
	/**
	 * Get substr, but respect sentence boundaries
	 * 
	 * Calls substr_word, if no sentence ends within given range.
	 * 
	 * @attention This function in general is rather stupid. Should recognize if a dot is used 
	 *   within sentence, e.g. within an URL or as thousands seperator, but will fail on stuff like "etc.".
	 * 
	 * @param string $val 
	 * @param int $start Start of substr (usually 0)
	 * @param int $max_length Maximum length of substring
	 * @param bool $elipsis Append "..." to the string
	 */
	public static function substr_sentence($val, $start, $max_length, $elipsis = false) {
		$val_temp = $val . ' ';
		$pos = false;
		$ret = self::substr($val_temp, $start, $max_length);
		$punctuations = array('?', '!', '.');
		foreach($punctuations as $punc) {
			$pos_temp = self::strrpos($ret, $punc);
			//var_dump($ret, $punc, $pos_temp);
			if ($pos_temp !== false && $pos_temp > $pos) {
				// There is a punctuation character and it seems to be the last (up to now),
				// so check if there is a space following it
				$test = self::substr($val_temp, $pos_temp + 1, 1);
				if ($test === ' ' || $test === '') {
					$pos = $pos_temp;
				} 	
			}
		}
		if ($pos === false) {
			// Check if a punctuation follows substring
			$test = self::substr($val_temp, $start + $max_length, 1);
			if (!in_array($test, $punctuations)) {
				$ret = self::substr_word($val, $start, $max_length, false);
			}
		}
		else {
			$ret = self::substr($ret, 0, $pos + 1);
		}
		
		if ($elipsis && $ret) {
			$ret .= '...';
		}

		return $ret;
	}	
	
	public static function right($val, $count) {
		return self::substr($val, -$count, $count);
	}

	public static function left($val, $count) {
		return self::substr($val, 0, $count);
	}

	/**
	 * Returns true if haystack starts with needlse
	 * 
	 * @attention If needle is en empty string, this function returns false! 
	 */
	public static function starts_with($haystack, $needle) {
		if ($needle !== '') {
			return (strncmp($haystack, $needle, strlen($needle)) == 0);
		}
		return false;
		/*
		if ($needle !== '') {
			return self::substr($haystack, 0, self::length($needle)) == $needle;
		}
		else {
			return false;
		}
		*/
	}

	/**
	 * Returns true if haystack starts with needlse
	 */
	public static function ends_with($haystack, $needle) {
		$lenght_needle = self::length($needle);
		if ($lenght_needle > 0) {
			return self::substr($haystack, -$lenght_needle, $lenght_needle) == $needle;
		}
		return false;
	}

	/**
	 * Removes everything that is not plain ascii and replaces known special chars like umlauts
	 *
	 * Text is converted to lower case, too
	 *
	 * @param string text to clean
	 * @param string if not empty, everything that is not a letter or number is replaced by this
	 */
	public static function plain_ascii($path, $separator = '-', $removewhitespace = true) {
		// Away with html specific stuff
		$ret = rawurldecode($path);
		$ret = html_entity_decode($ret, ENT_QUOTES, GyroLocale::get_charset());
		$ret = strip_tags($ret);
		if ($removewhitespace) {
			$ret = self::$impl->to_lower($ret);
			$replace = array(
				'ä' => 'ae', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'æ' => 'ae',
				'ç' => 'c', 'ć' => 'c', 'ĉ' => 'c', 'č' => 'c',
				'ö' => 'oe','ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'oe',
				'ü' => 'ue', 'ù' => 'u', 'ú' => 'u', 'û' => 'u',
				'ß' => 'ss',
				'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
				'ý' => 'y',
				'ñ' => 'n',
				'î' => 'i', 'ì' => 'i', 'í' => 'i', 'ï' => 'i'
			);
			$ret = strtr($ret, $replace);

			$pattern = str_replace('%sep%', $separator, '*[^a-zA-Z0-9_%sep%]+*');
			$ret = preg_replace($pattern, $separator, $ret);

			// remove duplicated seps, like "Tom & Jerry" => "Tom---Jerry"
			if ($separator != '') {
				$test = $separator . $separator;			
				while(strpos($ret, $test) !== false) {
					$ret = str_replace($test, $separator, $ret);
				}
			}
			$ret = trim($ret, $separator);
		}
		return $ret;
	}

	/**
	 * Returns part of haystack that is before needle
	 * 
	 * If needle is not found, $haystack is returned
	 */
	public static function extract_before($haystack, $needle) {
		$pos = strpos($haystack, $needle);
		$ret = $haystack;
		if ($pos !== false) {
			$ret = self::substr($haystack, 0, $pos);
		}
		return $ret;
	}

	/**
	 * Returns part of haystack that is after needle
	 * 
	 * If needle is not found, $haystack is returned
	 */
	public static function extract_after($haystack, $needle) {
		$pos = strpos($haystack, $needle);
		$ret = $haystack;
		if ($pos !== false) {
			$pos += self::length($needle);
			$ret = self::substr($haystack, $pos);
		}
		return $ret;
	}
	
	/**
	 * Extracts arguments from given string. Arguments are seperated by white space,
	 * but everything quoted by " is regarded as one argumne
	 *
	 * @return array
	 */
	public static function explode_terms($term) {
		// split into array
		$terms = explode(' ', $term);
		$args = array();
		$arg = '';
		$isQuoted = false;

		foreach ($terms as $thisTerm) {
			$thisTerm = trim($thisTerm);
			$appendix = '';
			if ( $thisTerm === '') {
				continue;
			}

			if ($isQuoted === false) {
				$arg = '';
				// We do not append, so check if term starts with "
				if ( self::substr($thisTerm, 0, 1) === '"') {
					$isQuoted = true;
					$arg = '"';
					$thisTerm = self::substr($thisTerm, 1);
				}
			}
			if ($isQuoted === true) {
				$appendix = ' ';
				// Do not make this an else-branch! Really! Don't!
				// if string ends with ", appending is over
				if ( self::substr($thisTerm, -1) === '"') {
					$isQuoted = false;
					$thisTerm = self::substr($thisTerm, 0, strlen($thisTerm) - 1);
					$appendix = '"';
				}
			}
			else {
				// We have a non-quoted term. Check if there are non-character words in it
				// replace non-word characters with space, if there is no space before them
			 	// E.g. 'Saint-John-Cathedral -London' to 'Saint John Cathedral -London'
				$thisTerm = self::preg_replace('|(\S)\W|', '\1 ', $thisTerm);
				$thisTerms = explode(' ', $thisTerm);
				$lastIndex = count($thisTerms) - 1;
				for($i = 0; $i < $lastIndex; $i++) {
					$args[] = $thisTerms[$i]; 
				}  
				$thisTerm = $thisTerms[$lastIndex];
			}

			$arg .= $thisTerm . $appendix;
			if ($isQuoted === false) {
				$args[] = $arg;
			}
		}
		
		$args = array_map('trim', $args);
		for ($i = count($args) - 1; $i >= 0; $i--) {
			if ($args[$i] === '') {
				unset($args[$i]);
			}
		}
		
		return $args;
	}
}

if (function_exists('mb_detect_encoding')) {
	require_once dirname(__FILE__) . '/string_impl/string.mbstring.cls.php';
	String::$impl = new StringMBString();	
}
else {
	require_once dirname(__FILE__) . '/string_impl/string.php.cls.php';
	String::$impl = new StringPHP();		
}
