<?php
/**
 * A field to hold an international phone number conforming to E.123
 *
 * @see https://en.wikipedia.org/wiki/E.123
 * 
 * Field in DB should be defined as VARCHAR(30)
 * 
 * The theoretical maximum length of an international phone number is 15, but you never know
 *
 * @author Gerd Riesselmann
 * @ingroup TextFields
 */
class DBFieldTextPhone extends DBFieldText {
	public function __construct($name, $default_value = null, $policy = self::NOT_NULL) {
		parent::__construct($name, 30, $default_value, $policy);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = parent::validate($value);
		if ($ret->is_ok() && Cast::string($value) !== '') {
			if (!Validation::is_e123_phone($value)) {
				$ret->append(tr(
					'%field must be a valid international phone number in format +## #### ####',
					'textfields', 
					array(
						'%field' => tr($this->get_field_name()),
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
		// Remove all space
		$cleaned = str_replace(' ', '', $value);
		return $this->quote($cleaned);
	}
}