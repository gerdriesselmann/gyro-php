<?php
/**
 * Finds file for given template ressource
 * 
 * Template files are resolved top-down, if path is relative, that is 
 * 
 * - application first
 * - modules: latest enabled come first
 * - core 
 * 
 * Absolute paths are left untouched.
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplatePathResolver {
	public static $resolved_paths = array();
	private static $template_paths = array();
	
	/**
	 * Resolve path name
	 * 
	 * If an array is passed, the first item that can be resolved will be returned 
	 * 
	 * @throws Exception If path could not be resolved
	 * 
	 * @param string|array $resource Absolute or relative path or an array
	 * @param string $required_file_extension file extension of template files. Default is "tpl.php" 
	 */
	public static function resolve($resource, $required_file_extension = 'tpl.php') {
		$resource = Arr::force($resource, false);
		foreach($resource as $res) {
			$key = $res;
			if (!isset(self::$resolved_paths[$key])) {
				$ret = self::find_template($res, $required_file_extension);
				self::$resolved_paths[$key] = $ret;
			}
			else {
				$ret = self::$resolved_paths[$key];
			}
			if ($ret !== false) {
				break;
			}
		}
		// Not found
		if ($ret === false) {
			throw new Exception('Template file ' . implode(', ', $resource) . ' not found');
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
			// some kind of protocol like http:// or such
			return $resource;
		}
		$resource_to_find = $resource;
		if ($required_file_extension) {
			$extension = '.' . $required_file_extension;
			if (!GyroString::ends_with($resource_to_find, $extension)) {
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
