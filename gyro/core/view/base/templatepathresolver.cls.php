<?php
/**
 * Finds file for given template ressource
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplatePathResolver {
	public static $resolved_paths = array();
	private static $template_paths = array();
	
	public static function resolve($resource, $required_file_extension = 'tpl.php') {
		$key = $resource;
		if (!isset(self::$resolved_paths[$key])) {
			$ret = self::find_template($resource, $required_file_extension);
			// Not found
			if ($ret === false) {
				throw new Exception("Template file $resource not found");
			}
			self::$resolved_paths[$key] = $ret;
		}
		return self::$resolved_paths[$key];
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
			// some kind of protocol like http:// or such
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
				return $path;
			}
		}
		if (file_exists($resource_to_find)) {
			return $resource_to_find;
		}
		
		// Not found
		return false;		
	}
	
	public static function get_template_paths() {
		$lang = strtolower(GyroLocale::get_language());
		if (empty(self::$template_paths[$lang])) {			
			$dirs = Load::get_base_directories(Load::ORDER_OVERLOAD);
			$ret = array();
			foreach($dirs as $dir) {
				$test = $dir . 'view/templates/' . $lang . '/';
				if (file_exists($test)) {
					$ret[] = $test;
				}
				$test = $dir . 'view/templates/default/';
				if (file_exists($test)) {
					$ret[] = $test;
				}
			}
			self::$template_paths[$lang] = $ret;
		}
		return self::$template_paths[$lang];
	}
}
