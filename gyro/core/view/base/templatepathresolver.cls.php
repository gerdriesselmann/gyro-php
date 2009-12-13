<?php
/**
 * Finds file for given template ressource
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplatePathResolver {
	public static function resolve($resource, $required_file_extension = 'tpl.php') {
		$ret = self::find_template($resource, $required_file_extension);
		// Not found
		if ($ret === false) {
			throw new Exception("Template file $resource not found");
		}
		return $ret;
	}

	public static function exists($resource, $required_file_extension = 'tpl.php') {
		$path = self::find_template($resource, $required_file_extension);
		return ($path !== false);		
	}
	
	private static function find_template($resource, $required_file_extension = 'tpl.php') {
		if (substr($resource, 0, 1) == '/') {
			// absolute path
			return $resource;
		}
		if (strpos($resource, ':') !== false && strpos($resource, '::') === false) {
			// some kind of protocol..
			return $resource;
		}
		if ($required_file_extension) {
			$extension = '.' . $required_file_extension;
			if (!String::ends_with($resource, $extension)) {
				$resource .= $extension; 
			}
		}
		$paths = self::get_template_paths(); 
		foreach($paths as $path) {
			$path .= $resource;
			if (file_exists($path)) {
				return $path;
			}
		}
		// Not found
		if (!file_exists($resource)) {
			return false;
		}
		return $resource;		
	}
	
	public static function get_template_paths() {
		$lang = String::to_lower(GyroLocale::get_language());	
		$dirs = Load::get_base_directories();
		$ret = array();
		foreach($dirs as $dir) {
			$ret[] = $dir . 'view/templates/' . $lang . '/';
			$ret[] = $dir . 'view/templates/default/';
		}
		return $ret; 
	}
}
