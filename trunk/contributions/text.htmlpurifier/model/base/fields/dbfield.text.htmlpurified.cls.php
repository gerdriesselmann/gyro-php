<?php
/**
 * A DB Text field holding HTML that automatically gets purified before it is written to DB
 * 
 * This class is rather generic. You can overload it to apply more specialized conversion. 
 * 
 * @ingroup HtmlPurifier
 * @author Gerd Riesselmann
 */
class DBFieldTextHtmlPurified extends DBFieldText {
	/**
	 * Constructor
	 * 
	 * Since TEXT is more likely than varchar, default length is TEXT
	 */
	public function __construct($name, $length = DBFieldText::BLOB_LENGTH_SMALL, $default_value = null, $policy = self::NONE) {
		parent::__construct($name, $length, $default_value, $policy);
	}
	
	/**
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value) {
		if (!is_null($value) && !$value instanceof DBNull) {
			$value = $this->apply_conversion($value);
		}
		return parent::format($value);
	}
	
	/**
	 * To be overloaded: Apply conversion
	 * 
	 * @param string $value
	 * @return string
	 */
	protected function apply_conversion($value) {
		return ConverterFactory::encode($value, CONVERTER_HTMLPURIFIER);
	} 
}