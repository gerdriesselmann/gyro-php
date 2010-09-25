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
	 * Format values that are not NULL
	 * 
	 * @param mixed $value
	 * @return string
	 */
	protected function do_format_not_null($value) {
		return parent::do_format_not_null(
			HtmlText::apply_conversion(HtmlText::STORAGE, $value, $this->get_table()->get_table_name())
		);
	}
}