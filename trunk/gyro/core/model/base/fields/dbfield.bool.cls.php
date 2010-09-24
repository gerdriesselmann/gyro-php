<?php
/**
 * A boolean field im DB
 * 
 * A Boolean field is an Enum with Values 'TRUE' and 'FALSE'.
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldBool extends DBField {
	/**
	 * Constructor
	 *
	 * @param string $name field name 
	 * @param bool $default_value Default Value
	 * @param int $policy
	 * @return void
	 */
	public function __construct($name, $default_value = false, $policy = self::NONE) {
		parent::__construct($name, $default_value, $policy);
	}
	
		/**
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		if ($value) {
			return $this->quote('TRUE');
		} else { 
			return $this->quote('FALSE');
		}
	}

	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select() {
		return '(' . parent::format_select() . " = 'TRUE')";	
	}

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		if (is_string($value)) {
			return in_array(strtoupper($value), array('TRUE', '1'));
		}
		return !empty($value);
	}	

	/**
	 * Reads value from array (e.g $_POST) and converts it into something meaningfull
	 */
	public function read_from_array($arr) {
		$ret = Arr::get_item($arr, $this->get_field_name(), null);
		if (!$this->is_null($ret)) {
			$ret = !empty($ret);
		}
		return $ret;
	}
}
