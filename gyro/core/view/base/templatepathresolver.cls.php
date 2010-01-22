<?php
/**
 * Finds file for given template ressource
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplatePathResolver {
	public static $resolved_paths = array();
	
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
			self::$resolved_paths[$resource] = $resource;
			return $resource;
		}
		if (strpos($resource, ':') !== false && strpos($resource, '::') === false) {
			// some kind of protocol..
			self::$resolved_paths[$resource] = $resource;
			return $resource;
		}
		$resource_to_find = $resource;
		if ($required_file_extension) {
			$extension = '.' . $required_file_extension;
			if (!String::ends_with($resource_to_find, $extension)) {
				$resource_to_find .= $extension; 
			}
		}
		$paths = self::get_template_paths(); 
		foreach($paths as $path) {
			$path .= $resource_to_find;
			if (file_exists($path)) {
				self::$resolved_paths[$resource] = $path;
				return $path;
			}
		}
		if (file_exists($resource_to_find)) {
			self::$resolved_paths[$resource] = $resource_to_find;
			return $resource_to_find;
		}
		
		// Not found
		self::$resolved_paths[$resource] = $false;
		return false;		
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
