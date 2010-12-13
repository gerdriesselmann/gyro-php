<?php
require_once dirname(__FILE__) . '/dbfield.enum.cls.php';

/**
 * A SET datatype as supported by MySQL. Actually a couple of bit flags
 * 
 * The SET datatype is an array that is saved into DB as integer
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldSet extends DBFieldEnum {
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status();
		$arr_value = Arr::force($value, false);
		foreach($arr_value as $val) {
//			if ($val !== '') {
				$ret->merge(parent::validate($val));
//			}
		}
		return $ret;
	}
	
	/**
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		$value = Arr::force($value);
		$ret = 0;
		$cnt = count($this->allowed);
		for ($i = 0; $i < $cnt; $i++) {
			$test = $this->allowed[$i];
			if (in_array($test, $value)) {
				$ret = $ret | pow(2, $i);
			}
		}
		return $ret;		
	}

	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select() {
		return $this->get_field_name() . '+0';
	}	

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		$ret = array();
		$cnt = count($this->allowed);
		for ($i = 0; $i < $cnt; $i++) {
			$test = pow(2, $i);
			if ($value & $test) {
				$ret[] = $this->allowed[$i];
			}
		}
		return $ret;
	}
	
	// ---------------------------------------------------
	// Helper functions for clients 
	// ---------------------------------------------------

	/**
	 * Set value on given set
	 * 
	 * @param array $set The set to modify
	 * @param string $value The value to set
	 */
	public static function set_set_value(&$set, $value) {
		if (!self::set_has_value($set, $value)) {
			self::set_force_array($set);
			$set[] = $value;
		}
	}

	/**
	 * Clear value on given set
	 * 
	 * @param array $set The set to modify
	 * @param string $value The value to set
	 */
	public static function set_clear_value(&$set, $value) {
		$new_set = array();
		self::set_force_array($set);
		foreach($set as $v) {
			if ($v !== $value) {
				$new_set[] = $v;
			}
		} 
		$set = $new_set;
	}

	/**
	 * Returns wether value is in given set or not
	 * 
	 * @param array $set The set to modify
	 * @param string $value The value to set
	 * @return bool
	 */
	public static function set_has_value($set, $value) {
		$ret = false;
		if (!is_null($set) && !$set instanceof DBNull) {
			$ret = in_array($value, $set);
		}
		return $ret;
	}
	
	private static function set_force_array(&$set) {
		if (is_null($set) || $set instanceof DBNull) {
			$set = array();
		}
	}
}
