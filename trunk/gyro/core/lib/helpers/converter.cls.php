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
	
	/**
	 * Creates a chain of converters, that is a set of converters that get 
	 * executed one after the other.
	 * 
	 * @since 0.5.1
	 * 
	 * You may pass an array that contains either key-value pairs where the key
	 * is the name of a converter and the value is its parameters, or a simple
	 * array, whereas the value is the name of a converter. This also can be mixed.
	 * 
	 * @code
	 * $chain = ConverterFactory::create_chain(
	 *   ConverterFactory::HTML_EX => array('h' => 3),
	 *   CONVERTER_TIDY
	 * );
	 * @endcode
	 * 
	 * @return IConverter
	 */
	public static function create_chain($arr_converters) {
		require_once dirname(__FILE__) . '/converters/chain.converter.php';
		$ret = new ConverterChain();

		foreach($arr_converters as $name_or_index => $params_or_name) {
			$has_index = is_numeric($name_or_index); 
			$name = ($has_index) ? $params_or_name : $name_or_index;
			$params = ($has_index) ? false : $params_or_name;
			
			$converter = self::create($name);
			if (!$converter instanceof IConverter)  {
				throw new Exception("Unknown Covnerter $name");
			}
			$ret->append($converter, $params);
		}
		
		return $ret;
	}
}
