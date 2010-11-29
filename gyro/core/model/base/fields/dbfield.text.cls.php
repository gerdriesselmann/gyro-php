<?php
/**
 * A text field im DB
 * 
 * @attention A string of length 0 is treated as NULL, too 
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldText extends DBField {
	const BLOB_LENGTH_LARGE = 4294967295; // MYSQL LONGTEXT
	const BLOB_LENGTH_MEDIUM = 16777215; // MYSQL MEDIUMTEXT
	const BLOB_LENGTH_SMALL = 65535; // MYSQL TEXT
	/**
	 * Max length on text
	 *
	 * @var int
	 */
	protected $length = 255;
	
	public function __construct($name, $length = 255, $default_value = null, $policy = self::NONE) {
		parent::__construct($name, $default_value, $policy);
		$this->length = $length;
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status();
		$l = String::length(Cast::string($value));
		if ($l > $this->length) {
			$ret->append(tr(
				'%field may have no more than %num character', 
				'core', 
				array(
					'%field' => $this->get_field_name_translation(),
					'%num' => $this->length
				)
			));
		}
		else if ($l == 0 && !$this->get_null_allowed()) {
			$ret->append(tr(
				'%field may not be empty', 
				'core', 
				array(
					'%field' => $this->get_field_name_translation(),
				)
			));
		}
		return $ret;
	}

	public function get_length() {
		return $this->length;
	}
}