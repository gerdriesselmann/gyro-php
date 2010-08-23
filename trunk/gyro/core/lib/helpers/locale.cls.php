<?php
/**
 * Manages Encoding and languages
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class GyroLocale {
	const UTF8 = 'UTF-8';
	
	private static $language = APP_LANG;
	private static $charset = APP_CHARSET;
	
	/**
	 * Set an according locale (used for date, and number formatting)
	 * 
	 * Sets language and charset, too
	 * 
	 * @param string $lang Language, like 'en', or 'en_US'
	 * @param string $charset A Charset like "latin1" or "UTF-8"
	 */
	public static function set_locale($lang, $charset = APP_CHARSET) {
		self::set_language($lang);
		self::set_charset($charset);
		
		$locales = self::get_locales($lang);
		$locales_encoded = array();
		$encoding = strtolower(str_replace('-', '', $charset));
		foreach($locales as $l) {
			$locales_encoded[] = $l . '.' . $encoding;
		}
		setlocale(LC_ALL, array_merge($locales_encoded, $locales));
		if (strtolower($lang) == 'en_us') {
			GyroDate::$local_date_order = GyroDate::MONTH_DAY_YEAR;
		}
		else {
			GyroDate::$local_date_order = GyroDate::DAY_MONTH_YEAR;
		}
	}
	
	/**
	 * Returns array of possible locales for a language
	 * 
	 * E.g. for "en" would return "en_US", "en"
	 *
	 * @param string $lang
	 * @return array
	 */
	public static function get_locales($lang) {
		$ret = array();
		if (strlen($lang) == 2) {
			switch ($lang) {
			case 'en':
				$ret[] = 'en_US';
				$ret[] = 'en_GB';
				break;
			default:
				// add xx_XX
				$ret[] = $lang . '_' . strtoupper($lang);
				break;
			}
		}
		$ret[] = $lang;
		return $ret;		
	}
	
	/**
	 * Sets language, but does not affect locale. Returns old lang
	 *
	 * @param string $lang
	 */
	public static function set_language($lang) {
		$ret = self::$language;
		self::$language = $lang;
		return $ret;
	}
	
	/**
	 * Sets charset, but does not affect locale
	 *
	 * @param string $charset
	 */
	public static function set_charset($charset) {
		self::$charset = $charset;
	}
	
	/**
	 * Returns current language
	 *
	 * @return string
	 */
	public static function get_language() {
		return self::$language;
	}

	/**
	 * Returns current charset
	 *
	 * @return string
	 */
	public static function get_charset() {
		return self::$charset;
	}
	
	/**
	 * Returns true, if charset is UTF-8
	 */
	public static function is_utf8() {
		return strtoupper(self::get_charset()) == self::UTF8;			
	}
}

if (!class_exists('Locale')) {
	class Locale extends GyroLocale {}
}