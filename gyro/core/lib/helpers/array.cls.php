<?php
/**
 * Encapsulates some convient array functions
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Arr {
	/**
	 * Return value for key, if available, else return default value
	 */
	public static function get_item($arr, $key, $default) {
		if (isset($arr[$key])) {
			return $arr[$key];
		}
		return $default;	
	}

	/**
	 * Return value for key, if available, else return default value
	 * 
	 * Key may contain [] to indicate array. item[0] would be retrieved as $arr['item']['0']
	 * 
	 * @param array $arr 
	 * @param string|array $key
	 * @param mixed $default  
	 */
	public static function get_item_recursive($arr, $key, $default) {
		$ret = $default;
		$arr_keys = self::extract_array_keys($key);
		
		if (count($arr_keys) > 0) {
			$last_key = array_pop($arr_keys);
			foreach($arr_keys as $key) {
				$arr = self::get_item($arr, $key, array());
			}
			$ret = self::get_item($arr, $last_key, $default);
		}
		
		return $ret;
	}

	/**
	 * Set value for key
	 * 
	 * Key may contain [] to indicate array. item[0] would be retrieved as $arr['item']['0'] 
	 * 
	 * @param array $arr 
	 * @param string|array $key
	 * @param mixed $value 
	 */
	public static function set_item_recursive(&$arr, $key, $value) {
		$ret = false;
		
		$arr_keys = self::extract_array_keys($key);
		if (count($arr_keys) == 0) {
			return false;
		}
		
		$last_key = array_pop($arr_keys);		
		$arr_work =& $arr;
		
		while (is_array($arr_work)) {
			$cur_key = array_shift($arr_keys);
			if (is_null($cur_key)) {
				break;
			}
			if (!isset($arr_work[$cur_key])) {
				$arr_work[$cur_key] = array();
			}
			$arr_work =& $arr_work[$cur_key];
		}
		if (is_array($arr_work)) {
			$arr_work[$last_key] = $value;
			$ret = true;
		}
		
		return $ret;
	}

	/**
	 * Unset entry in array 
	 * 
	 * Key may contain [] to indicate array. item[0] would be retrieved as $arr['item']['0'] 
	 * 
	 * @param array $arr 
	 * @param string|array $key
	 */
	public static function unset_item_recursive(&$arr, $key) {
		$ret = false;
		
		$arr_keys = self::extract_array_keys($key);
		if (count($arr_keys) == 0) {
			return false;
		}
		
		$last_key = array_pop($arr_keys);		
		$arr_work =& $arr;
		while (is_array($arr_work)) {
			$cur_key = array_shift($arr_keys);
			if (is_null($cur_key)) {
				break;
			}
			if (!isset($arr_work[$cur_key])) {
				break;
			}
			else {
				$arr_work =& $arr_work[$cur_key];
			}
		}
		if (is_array($arr_work)) {
			unset($arr_work[$last_key]);
			$ret = true;
		}
		
		return $ret;
	}
	
	/**
	 * Turn a string like a[b][c] into an array [a, b, c]
	 *
	 * @param string|array $key
	 * @return array
	 */
	public static function extract_array_keys($key) {
		$ret = array();
		if (is_array($key)) {
			$ret = $key;
		}
		else {
			$regex = '|\[(.*?)\]|';
			$ret = preg_split($regex, $key, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		}
		return $ret;
	}
	
	/**
	 * Static. Reads given array into given clean array
	 *
	 * The associative array returned contains only members with keys that are defined in $clean.
	 * The values are taken form $source if available else they are taken from $clean.
	 * 
	 * Values from $source processed with strip_tags 
	 *
	 * @param Array The clean array
	 * @param Array The array to read from
	 */
	public static function clean(&$clean, &$source) {
		foreach($source as $key => $value) 	{
			if (array_key_exists($key, $clean))
				$clean[$key] = strip_tags($value);
		}	
	}

	/**
	 * Implode associate array
	 * 
	 * @param string String to put between elements of array
	 * @param array The array to implode
	 * @param string String to put between array key and value. If empty, function will fall back to normal implode
	 * 
	 * @return string Imploded array as string
	 */	
	public static function implode($glue, $pieces, $assign = '=') {
		$ret = '';
		if (empty($assign)) {
			$ret = implode($glue, $pieces);
		}
		else {
			$bIsFirst = true;
			foreach($pieces as $key => $value) {
				if ($bIsFirst == false) {
					$ret .= $glue;
				}
				$bIsFirst = false;
				$ret .= $key . $assign . $value;			
			}	
		}
		return $ret;
	}
	
	/**
	 * Implodes the array using $glues, but appends the last element using $glue_tail
	 * 
	 * Example: 
	 * 
	 * @code
	 * $arr = array('cats', 'dogs', 'squirrels');
	 * print 'It\'s raining ' . Arr::implode_tail(', ', ', and ', $arr);
	 * // Outputs: It's raining cats, dogs, and squirrels.
	 * $arr = array('cats', 'dogs');
	 * print 'It\'s raining ' . Arr::implode_tail(', ', ', and ', $arr);
	 * // Outputs: It's raining cats, and dogs.
	 * $arr = array('cats');
	 * print 'It\'s raining ' . Arr::implode_tail(', ', ', and ', $arr);
	 * // Outputs: It's raining cats.
	 * @endcode
	 * 
	 * @since 0.5.1
	 * 
	 * @param string $glue
	 * @param string $glue_tail
	 * @param array $pieces
	 * @return string
	 */
	public static function implode_tail($glue, $glue_tail, $pieces) {
		$ret = '';
		$last = array_pop($pieces);
		$ret .= implode($glue, $pieces);
		if (!is_null($last)) {
			if ($ret !== '') {
				$ret .= $glue_tail;
			}
			$ret .= $last;
		}
		return $ret;
	}

	/**
	 * Force element into an array
	 * 
	 * If passed an array, it returns the array, else it will return array($value)
	 * 
	 * @param mixed $value
	 * @param bool $allow_empty If set to false, empty values will convert to an empty array 
	 */
	public static function force($value, $allow_empty = true) {
		if (is_array($value)) {
			return $value;
		}
		$ret = array();
		if ($allow_empty || !empty($value)) {
			$ret[] = $value;
		}
		return $ret;
	}
	
	/**
	 * Forces numeric array keys to strings
	 *
	 * @param array $arr
	 * @return array
	 */
	public static function force_keys_to_string($arr) {
		$ret = array();
		foreach($arr as $key => $value) {
			if (is_array($value)) {
				$value = self::force_keys_to_string($value);
			}
			if (is_numeric($key)) {
				$key = '__string_casted_' . $key; 
			}
			$ret[$key] = $value;
		}
		return $ret;
	}
	
	/**
	 * Reverts results of force_keys_to_string
	 *
	 * @param array $arr
	 * @return array
	 */
	public static function unforce_keys_from_string($arr) {
		$ret = array();
		foreach($arr as $key => $value) {
			if (is_array($value)) {
				$value = self::unforce_keys_from_string($value);
			}
			$key = str_replace('__string_casted_', '', $key); 
			$ret[$key] = $value;
		}
		return $ret;
	}
	
	/**
	 * Remove all occurences of a given value from array 
	 * 
	 * @param array $arr The array to modify
	 * @param mixed $value The value to unset
	 */
	public static function remove(&$arr, $value) {
		foreach(array_keys($arr, $value) as $key) {
			unset($arr[$key]);
		}
	}
	
	/**
	 * Remove all occurences of a given value from array recursivley 
	 * 
	 * @param array $arr The array to modify
	 * @param mixed $value The value to unset
	 */
	public static function remove_recursive(&$arr, $value) {
		self::remove($arr, $value);
		foreach($arr as $key => $item) {
			if (is_array($item)) {
				self::remove_recursive($item, $value);
				$arr[$key] = $item;
			}
		}
	}
}