<?php
/**
 * Collects and processes placeholders
 * 
 * @author Gerd Riesselmann
 * @ingroup Placeholders
 */
class TextPlaceholders {
	/**
	 * Placeholders
	 * 
	 * @var array
	 */
	private static $placeholders = array();
	
	/**
	 * Add a placeholder to list of placeholders
	 * 
	 * @params ITextPlaceholder $placeholder
	 * @params bool $to_front If true, placehodler is prepended to list, else it is appended
	 */
	public static function add(ITextPlaceholder $placeholder, $to_front = false) {
		if ($to_front) {
			array_unshift(self::$placeholders, $placeholder);
		}
		else {
			self::$placeholders[] = $placeholder;
		}
	} 
	
	/**
	 * Apply placeholders to given text
	 * 
	 * @params string $text
	 * @return string
	 */
	public static function apply($text) {
		$ret = $text;
		foreach(self::$placeholders as $placeholder) {
			$ret = $placeholder->apply($ret);
		}
		return $ret;
	}
}