<?php
/**
 * Handles on type for paramterized routed
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IParameterizedRouteHandler {
	/**
	 * Returns the key that is used to identify this handler in route declaration, e.g. "s" or "ui>"
	 * 
	 * @return string
	 */
	public function get_type_key();
	
	/**
	 * Return regex to validate path  
	 */
	public function get_validate_regex($params);
	
	/**
	 * Preprocess a value before URL is build
	 */
	public function preprocess_build_url($value);
}
