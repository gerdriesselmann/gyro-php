<?php
/**
 * A field to hold an email
 * 
 * Field in DB should be defined as VARCHAR(255)
 * 
 * The theoretical maximum length of an email is 320 characters. 255 characters seem sufficient, though 
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup TextFields
 */
class DBFieldTextEmail extends DBFieldText {
	public function __construct($name, $default_value = null, $policy = self::NOT_NULL) {
		parent::__construct($name, 255, $default_value, $policy);
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
			if (!Validation::is_email($value)) {
				$ret->append(tr(
					'%field must be a valid email address', 
					'textfields', 
					array(
						'%field' => tr($this->get_field_name()),
					)
				));
			}
		}
		return $ret;
	}	
}