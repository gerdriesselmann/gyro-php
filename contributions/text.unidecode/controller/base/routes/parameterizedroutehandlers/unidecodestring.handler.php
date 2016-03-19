<?php
/**
 * Handle ansi unidecoded string (the "spu" type)
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup Unidecode
 */
class UnidecodestringParameterizedRouteHandler implements IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key() {
		return "spu";
	}
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params) {
		if ($params === false) {
 			return '[a-zA-Z0-9_-]*?';
 		}
 		else {
 			return '[a-zA-Z0-9_-]{' . preg_quote($params) . '}';
 		}
	}
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value) {
		return GyroString::plain_ascii(ConverterFactory::encode($value, CONVERTER_UNIDECODE), '-', true);
	}
}
