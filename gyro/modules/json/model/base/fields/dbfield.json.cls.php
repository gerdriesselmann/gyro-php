<?php
/**
 * A field to serialize content into JSON, can be anything, e.g. an array
 */
class DBFieldJSON extends DBFieldText {
	public function __construct($name, $length = DBFieldText::BLOB_LENGTH_SMALL, $default_value = null, $policy = self::NONE) {
		parent::__construct($name, $length, $this->to_json($default_value), $policy);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		return parent::validate($this->to_json($value));
	}
	
	/**
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		return parent::do_format_not_null($this->to_json($value));
	}

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		return is_null($value) ? null : $this->from_json($value);
	}
	
	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default() {
		$ret = parent::get_field_default();
		if ($ret) {
			$ret = $this->from_json($ret);
		}
		return $ret;
	}

	private function to_json($v) {
		return ConverterFactory::encode($v,CONVERTER_JSON);
	}

	private function from_json($v) {
		return ConverterFactory::decode($v,CONVERTER_JSON);
	}
}
