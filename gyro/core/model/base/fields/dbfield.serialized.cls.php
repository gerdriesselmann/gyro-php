<?php
require_once dirname(__FILE__) . '/dbfield.text.cls.php';

/**
 * A field to serialize content, can be anything, e.g. an array
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldSerialized extends DBFieldText {
	public function __construct($name, $length = DBFieldText::BLOB_LENGTH_SMALL, $default_value = null, $policy = self::NONE) {
		/* @TODO Should default value be serialized here? */
		parent::__construct($name, $length, serialize($default_value), $policy);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		return parent::validate(serialize($value));
	}
	
	/**
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		return parent::do_format_not_null(serialize($value));
	}

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		return is_null($value) ? null : unserialize($value);
	}
	
	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default() {
		$ret = parent::get_field_default();
		if ($ret) {
			$ret = unserialize($ret);
		}
		return $ret;
	}	
}
