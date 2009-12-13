<?php
/**
 * Router for route invoked in Console
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class ConsoleRouter extends RouterBase {
	protected $path;
	
	public function __construct($path, $class_instantiater) {
 	 	$this->path = $path;
		parent::__construct($class_instantiater);
	}
	
 	
 	/**
 	 * Returns the current path, preprocessed
 	 * 
 	 * If index page is invoked, '.' is returned
 	 * 
 	 * @return string The current path, e.g. path/to/page
 	 */
 	protected function get_path() {
		$path = $this->path; 
		if (empty($path) || $path == '/') {
			$path = '.';
		}
		return $path;  		
 	}
}
