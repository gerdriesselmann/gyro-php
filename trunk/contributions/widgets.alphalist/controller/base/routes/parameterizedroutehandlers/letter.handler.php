<?php
/**
 * Handle a single, lowercase letter (sl-type)
 * 
 * Use like this: /route/{letter:sl}
 * 
 * @author Gerd Riesselmann
 * @ingroup AlphaList
 */
class LetterParameterizedRouteHandler implements IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key() {
		return "sl";
	}
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params) {
		return '[a-z]';
	}
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value) {
		return String::to_lower($value);
	}
}
