<?php
/**
 * Handle int (the "i" type)
 * 
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class IntParameterizedRouteHandler implements IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key() {
		return "i";
	}
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params) {
		return '[+-]?[0-9][0-9]*';
	}
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value) {
		return $value;
	}
}
