<?php
/**
 * A template rendering class
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ITemplateEngine {
	/**
	 * Set a template variable
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function assign($name, $value);
	
	/**
	 * Assign array of template variables
	 *
	 * @param array $arr Associative array with name as key and value as value
	 */
	public function assign_array($arr);

	/*
	 * Returns value of template var
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function retrieve($name);		
	
	/**
	 * Renders content
	 *
	 * @param string $file Tempalte file name
	 * @return string Rendered content
	 */
	public function fetch($file);
}
