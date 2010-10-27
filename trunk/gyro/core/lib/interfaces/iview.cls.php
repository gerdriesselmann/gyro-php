<?php
require_once dirname(__FILE__) . '/irenderer.cls.php';

/**
 * Basic view interface
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IView extends IRenderer {
	/**
	 * If set as policy rendered content is printed also
	 */
	const DISPLAY = 1;
	const NO_CACHE = 2;
	const CONTENT_ONLY = 4;
	
	/**
	 * Pass a variable to the view
	 *
	 * @param string $var The name of the variable
	 * @param mixed $value The value
	 */
	public function assign($var, $value);
	
	/**
	 * Pass an associative array to the view
	 *
	 * @param array $vars
	 */
	public function assign_array($vars);
	
	/**
	 * Retrieve a variable from the view
	 *
	 * @param string $var The name of the variable
	 * @return mixed The Value
	 */
	public function retrieve($var);
	
	/**
	 * Retrieve all variables
	 * 
	 * @return array Associative array 
	 */
	public function retrieve_array();
	
	/**
	 * Copy all variables to other view
	 * 
	 * @param IView $view
	 */
	public function copy_to($view);
}

