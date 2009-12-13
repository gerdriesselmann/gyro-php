<?php
/**
 * Simple template that just includes template file
 * 
 * Based upon techniques discussed here: http://www.sitepoint.com/article/beyond-template-engine/
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplateEngineSimple implements ITemplateEngine {
	/**
	 * Template vars
	 *
	 * @var array
	 */
	protected $vars = array();
	
	/**
	 * Set a template variable
	 *
	 * @param string $name
	 * @param mixed $value
	 */
	public function assign($name, $value) {
		$this->vars[$name] = $value;
	}
	
	/**
	 * Assign array of template variables
	 *
	 * @param array $arr Associative array with name as key and value as value
	 */
	public function assign_array($arr) {
		$this->vars = array_merge($this->vars, $arr);
	}

	/*
	 * Returns value of template var
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function retrieve($name) {
		return Arr::get_item($this->vars, $name, false);		
	}
	
	/**
	 * Renders content
	 *
	 * @return string Rendered content
	 */
	public function fetch($file) {
		$file = $this->resolve_path($file);
		
		extract($this->vars);
		ob_start(); 
		include($file);  
		$contents = ob_get_contents();
		ob_end_clean();
		
		return $contents; 		
	}
	
	/**
	 * Resolve template path
	 *
	 * @param string $file
	 * @return string
	 */
	protected function resolve_path($file) {
		return TemplatePathResolver::resolve($file, 'tpl.php');
	}
}
