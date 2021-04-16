<?php
/**
 * Convertes from and to JSON
 * Use either directly or through ConverterFactory like this:
 * 
 * @code
 * // To JSON
 * $json_string = ConverterFactory::encode($data, CONVERTER_JSON);
 * // Back from JSON to data structure
 * $data = ConverterFactory::decode($json_string, CONVERTER_JSON);
 * @endcode
 * 
 * @author Gerd Riesselmann
 * @ingroup JSON
 */
class GyroJSON implements IConverter {
	/**
	 * Decode string to data
	 *
	 * @param string $str
	 * @return mixed
	 */	
	public function decode($str, $params = false) {
		$ret = false;
		if (function_exists('json_decode')) {
			// PHP 5.2 or PECL extension
			// There is a bug in json_decode and floating values before PHP 5.2.2
			// http://bugs.php.net/bug.php?id=41403 
			$cur_locale = setlocale(LC_NUMERIC, '0');
			setlocale(LC_NUMERIC, 'C'); // system
			$ret = json_decode($str);
			setlocale(LC_NUMERIC, $cur_locale); // back
			return $ret;
		}
		else {
			// Try PEAR Package Services_JSON
			include_once 'Services/JSON.php';
			if (class_exists('Services_JSON')) {
				$json = new Services_JSON();
				return $json->decode($str);
			}
		}
		throw new Exception(tr('No JSON implementation found', 'ajax'));		
	}
	
	/**
	 * Turns data in a JSON string
	 *
	 * @param mixed $data
	 * @return string
	 */
	public function encode($data, $params = false) {
		if (function_exists('json_encode')) {
			// PHP 5.2 or PECL extension
			// There is a bug in json_encode and floating values before PHP 5.2.2
			// http://bugs.php.net/bug.php?id=41403 
			$cur_locale = setlocale(LC_NUMERIC, '0');
			setlocale(LC_NUMERIC, 'C'); // system
			$ret = json_encode($data);
			setlocale(LC_NUMERIC, $cur_locale); // back
			return $ret;
		}
		// Try PEAR Package Services_JSON
		include_once 'Services/JSON.php';
		if (class_exists('Services_JSON')) {
			$json = new Services_JSON();
			return $json->encode($data);
		}
		throw new Exception(tr('No JSON implementation found', 'ajax'));		
	}
}
?>