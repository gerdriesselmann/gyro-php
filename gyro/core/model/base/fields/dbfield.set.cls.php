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
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value) {
		if (is_null($value)) {
			return parent::format($value);
		}
		
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
	
}
