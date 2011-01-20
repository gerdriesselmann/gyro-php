<?php
/**
 * A DB Text field holding HTML that automatically gets converted on storage
 */
class DBFieldTextHtml extends DBFieldText {
	/**
	 * Constructor
	 * 
	 * Since TEXT is more likely than varchar, default length is TEXT
	 */
	public function __construct($name, $length = self::BLOB_LENGTH_SMALL, $default_value = null, $policy = self::NONE) {
		parent::__construct($name, $length, $default_value, $policy);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status();
		if (!$this->is_null($value)) {
			$value = $this->convert_value($value);
		}
		if ($value !== '') {
			// This test for empty HTML, like <p><span></span></p>, which will not be allowed
			// @TODO what about <img />?
	 		$test = String::preg_replace('|\W|ms', '', strip_tags($value)); 
	 		if ($test === '' && !$this->get_null_allowed()) {
				$ret->append(tr(
					'%field may not be empty', 
					'core', 
					array(
						'%field' => $this->get_field_name_translation(),
					)
				));
			}
		}
		if($ret->is_ok()) {
			$ret->merge(parent::validate($value));
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
		return parent::do_format_not_null(
			$this->convert_value($value)
		);
	}
	
	/**
	 * Apply storage conversion
	 */
	protected function convert_value($value) {
		return HtmlText::apply_conversion(HtmlText::STORAGE, $value, $this->get_table()->get_table_name());
	}
}