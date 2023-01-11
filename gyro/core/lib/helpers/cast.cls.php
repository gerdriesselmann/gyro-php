<?php
/**
 * Cast helper routines
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Cast {
	/**
	 * Static. Forces the given param to be integer.
	 * 
	 * @param mixes
	 * @return Integer 
	 */
	public static function int($val) {
		return (is_numeric($val) ? intval($val) : 0);
	}

	/**
	 * Static. Forces the given param to be float.
	 * 
	 * @param mixed The value to convert
	 * @param bool True, if languange specific seperators should be acknowledged
	 * @return Float 
	 */
	public static function float($val) {
		return (is_numeric($val) ? floatval($val) : 0.0);
	}
	
	/**
	 * Static. Converts string retrieved from PHP to date
	 *
	 * @param String Anything that possible can be interpreted as a date
	 * @return date
	 */
	public static function datetime($string) {
		return GyroDate::datetime($string);
	}
	
	/**
	 * Static. Converts given expression to string
	 */
	public static function string($value) {
		if (is_string($value)) {
			return $value;
		}
		if (is_array($value)) {
			return '';
		}
		else if (is_object($value)) {
			if (isset($value->__toString)) {
				return $value->__toString();
			}
			else {
				return strval($value);
			}
		}
		return strval($value);
	}
}
