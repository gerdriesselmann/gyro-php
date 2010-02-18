<?php
/**
 * Handle enum (the "e" type)
 * 
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class EnumParameterizedRouteHandler implements IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key() {
		return "e";
	}
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params) {
		return str_replace(',', '|', preg_quote($params));
	}
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value) {
		return $value;
	}
}
