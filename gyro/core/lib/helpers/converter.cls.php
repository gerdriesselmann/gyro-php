<?php
/**
 * Generic Converter Factory
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class ConverterFactory {
	const HTML = 'html';
	const HTML_EX = 'htmlex';
			
	/**
	 * Array of user registered converters 
	 *
	 * @var array
	 */
	private static $registered_converters = array();
	
	/**
	 * Create a converter for given type
	 *
	 * @param string $type The type of converter to create
	 * @return IConverter
	 */
	public static function create($type) {
		$ret = false;
		switch ($type) {
			case self::HTML:
				require_once dirname(__FILE__) . '/converters/html.converter.php';
				$ret = new ConverterHtml();
				break;
			case self::HTML_EX:
				require_once dirname(__FILE__) . '/converters/htmlex.converter.php';
				$ret = new ConverterHtmlEx();
				break;
			default:
				$ret = Arr::get_item(self::$registered_converters, $type, false);
				break;
		}
		return $ret;
	}
	
	/**
	 * Encode teyt with converter of given type
	 *
	 * @param string $value Text to encode
	 * @param string $type Type of conversion
	 * @return string Converted text
	 */
	public static function encode($value, $type, $params = false) {
		$converter = self::create($type);
		if ($converter) {
			return $converter->encode($value, $params);
		}
		return false;
	}

	/**
	 * Decode teyt with converter of given type
	 *
	 * @param string $value Text to decode
	 * @param string $type Type of conversion
	 * @return string Converted text
	 */
	public static function decode($value, $type, $params  = false) {
		$converter = self::create($type);
		if ($converter) {
			return $converter->decode($value, $params);
		}
		return false;		
	}
	
	/**
	 * Register a converter
	 *
	 * @param string $type Type of conversion
	 * @param IConverter $converter
	 * @return void
	 */
	public static function register_converter($type, IConverter $converter) {
		self::$registered_converters[$type] = $converter;
	}
}
