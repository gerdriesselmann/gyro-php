<?php
/**
 * A integer field in DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldInt extends DBField {
	/**
	 * This field is autoincremented
	 */
	const AUTOINCREMENT = 4; 
	/**
	 * This field is unsigned
	 */
	const UNSIGNED = 2; 
	
	/**
	 * Convenience declaration: Combines AUTOINCREMENT, UNSIGNED, and NOT_NULL
	 */
	const PRIMARY_KEY = 7; // self::AUTOINCREMENT | self::UNSIGNED | self::NOT_NULL
	/**
	 * Convenience declaration: Combines UNSIGNED, and NOT_NULL
	 */
	const FOREIGN_KEY = 3; // self::UNSIGNED | self::NOT_NULL
	
	public function __construct($name, $default_value = 0, $policy = self::NOT_NULL) {
		parent::__construct($name, $default_value, $policy);
	}

	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default() {
		if ($this->has_policy(self::AUTOINCREMENT)) {
			return null;
		}
		return parent::get_field_default();
	}	
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		if ($value === '') {
			$value = null;
		}
		
		$ret = parent::validate($value);
		if ($ret->is_ok() && !is_null($value)) {
			if ($this->has_policy(self::UNSIGNED)) {
				if (!Validation::is_int($value, 0)) {
					$ret->append(tr(
						'%field must be a positive integer', 
						'core', 
						array(
							'%field' => tr($this->get_field_name(), 'global')
						)
					));
				}
			}
			else if (!Validation::is_int($value)) {
				$ret->append(tr(
					'%field must be an integer', 
					'core', 
					array(
						'%field' => tr($this->get_field_name(), 'global')
					)
				));
			}
		}
		
		return $ret;
	}

	/**
	 * Returns true, if field has default value
	 */
	public function has_default_value() {
		$ret = $this->has_policy(self::AUTOINCREMENT) || parent::has_default_value();
		return $ret;
	}

	/**
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		return Cast::int($value);
	}
	
}