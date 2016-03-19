<?php
/**
 * A float field in DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldFloat extends DBField {
	const UNSIGNED = 2; 

	public function __construct($name, $default_value = 0, $policy = self::NOT_NULL) {
		parent::__construct($name, $default_value, $policy);
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
				if (!Validation::is_double($value, 0)) {
					$ret->append(tr(
						'%field must be a positive number', 
						'core', 
						array(
							'%field' => $this->get_field_name_translation()
						)
					));
				}
			}
			else if (!Validation::is_double($value)) {
				$ret->append(tr(
					'%field must be a number', 
					'core', 
					array(
						'%field' => tr($this->get_field_name(), 'global'),
					)
				));
			}
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
		return number_format($value, 10, '.', '');
	}	
	
	/**
	 * Reads value from array (e.g $_POST) and converts it into something meaningfull
	 */
	public function read_from_array($arr) {
		$ret = parent::read_from_array($arr);
		if (!empty($ret)) {
			$ret = GyroString::delocalize_number($ret);
		}
		return $ret;
	}

	/**
	 * Returns true if $value is NULL or DBNull
	 */
	protected function is_null($value) {
		return parent::is_null($value) || $value === '';
	}
}