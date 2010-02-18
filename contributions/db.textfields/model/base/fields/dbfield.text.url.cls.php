<?php
/**
 * A field to hold an URL
 * 
 * Value gets validated and prefixed with http://, if it hasn't a scheme already. 
 *  
 * Field in DB should be defined as VARCHAR(255)
 * 
 * URLs of course can be much longer than 255 chars, up to 4000 e.g. is Apache default. This however
 * hardly makes sense.  
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup TextFields
 */
class DBFieldTextUrl extends DBFieldText {
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
			if (!Validation::is_url($value)) {
				$ret->append(tr(
					'%field must be an URL', 
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
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value) {
		if (Cast::string($value) === '') {
			return 'NULL';
		}
		else {
			return $this->quote(Url::create($value)->build(Url::ABSOLUTE, Url::NO_ENCODE_PARAMS));
		}
	}
}