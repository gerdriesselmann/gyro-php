<?php
require_once dirname(__FILE__) . '/lib/helpers/array.cls.php';

/**
 * Wrappers around include functions
 * 
 * @author Gerd Riesselmann
 * @ingroup Core
 */
class Load {
	const ORDER_OVERLOAD = 'overload';
	const ORDER_DECORATORS = 'decorators';
	
	/**
	 * Rememeber loaded stuff
	 *
	 * @var array
	 */
	private static $loaded = array();
	private static $module_dirs = array();	
	private static $base_dir_cache = array();
	private static $base_dir_subdir_cache = array();

	/**
	 * We support more than one module dir
	 */
	private static $module_base_dirs = array();
	
	/**
	 * Turns a file name into a class name by following this rules
	 * 
	 * - All parts are converted to Camel Casing
	 * - Parts are created by exploding filename at '.','_' or any other whitespace character
	 * - $appendix are added to Parts
	 * 
	 * @attention Existence of class is not checked! Just a string is returned
	 * 
	 * For example
	 * 
	 * @code
	 * filename_to_classname('validate.content.cmd.php', array('some_model', 'Command'), $extension_to_strip = 'cmd')
	 * @endcode
	 * 
	 * will return "ValidateContentSomeModelCommand"
	 * 
	 * @param string $filename
	 * @param array|string $appendix
	 * @param string $extension_to_strip File extension that gets stripped of filename (.php will be stripped always)
	 * 
	 * @return string 
	 */
	public static function filename_to_classname($filename, $appendix = array(), $extension_to_strip = '') {
		$filename = basename($filename, '.php');
		$filename = basename($filename, '.' . $extension_to_strip);
		
		$appendix = Arr::force($appendix, false); 
		foreach($appendix as $t) {
			$filename .= '.' . $t;
		}

		$filename = str_replace('_', '.', $filename);
		$filename = String::plain_ascii($filename, '.', true);
		$cls = '';
		$fragments = explode('.', $filename);
		foreach($fragments as $f) {
			$cls .= ucfirst($f); // $f is ASCII!
		}

		return $cls;
	}
	
	/**
	 * Includes the specified gyro component
	 *
	 * @param mixed $classes Either name of class to load or array of names
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	public static function helpers($classes) {
		$args = func_get_args();
		self::do_load('lib/helpers', $args);
	}

	/**
	 * Includes the specified component
	 *
	 * @param mixed $classes Either name of class to load or array of names
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	public static function components($classes) {
		$args = func_get_args();
		self::do_load('lib/components', $args);
	}
		
	/**
	 * Includes the specified gyro interface
	 *
	 * @param mixed $classes Either name of class to load or array of names
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	public static function interfaces($classes) {
		$args = func_get_args();
		self::do_load('lib/interfaces', $args);
	}

	/**
	 * Includes the specified model
	 *
	 * @param mixed $models Either name of model to load or array of names
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	public static function models($models) {
		$args = func_get_args(); 
		self::do_load('model/classes', $args, 'model', false);
		self::do_load('model/classes', $args, 'facade', false);
	}	

	/**
	 * Includes the specified tool
	 *
	 * @param mixed $tools Either name of tool to load or array of names
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	public static function tools($tools) {
		$args = func_get_args();
		self::do_load('controller/tools', $args);
	}	
	
	/**
	 * Load all files from directory
	 * 
	 * @param mixed $directories Either name of directory to load or array of directories
	 * @return void
	 * @throws Exception if directory does not exist
	 */
	public static function directories($directories) {
		$basedirs = self::get_base_directories(self::ORDER_DECORATORS);
		$arr_directories = self::to_array(func_get_args());
		foreach($arr_directories as $directory) {
			$found = false;
			foreach($basedirs as $basedir) {
				$path = $basedir . $directory;
					if (is_dir($path)) {
						$found = true;
						foreach (gyro_glob($path . '/*.php') as $inc) {
							include_once($inc);
						}
					}
			}
			if (!$found) {
				throw new Exception("Directory $directory not found");
			}
		}
	}
	
	/**
	 * Returns array of files from app, core and modules
	 *
	 * @param string $directory
	 * @param string $pattern
	 */
	public static function get_files_from_directory($directory, $pattern = '*.php') {
		$basedirs = self::get_base_directories_subdirs($directory, self::ORDER_DECORATORS);
		$found = false;
		$ret = array();
		foreach($basedirs as $path) {
			$found = true;
			foreach (gyro_glob($path . $pattern) as $inc) {
				$ret[basename($inc)] = $inc;
			}
		}
		
		if (!$found) {
			throw new Exception("Directory $directory not found");
		}
		
		return $ret;
	}
	
	/**
	 * Includes the specified command
	 *
	 * @param mixed $commands Either name of command to load or array of commads
	 * @return void
	 * @throws Exception if command could not be loaded
	 */
	public static function commands($commands) {
		$args = func_get_args();
		self::do_load('behaviour/commands', $args, 'cmd');
	}
	
	/**
	 * Add a new module directory
	 * 
	 * @param $dir Absolute path to directory
	 */
	public static function add_module_base_dir($dir) {
//		if (substr($dir, -1, 1) != '/') {
//			$dir .= '/'; 
//		}
		self::$module_base_dirs[] = rtrim(realpath($dir), '/') . '/';		
	}	
	
	/**
	 * Adds module path to internal path repository 
	 * 
	 * You may either pass a name or an absoulte path. If given a name, Gyro expects the module to be located
	 * beneath the Gyro modules directory
	 * 
	 * If a file enabled.inc.php exists beneath the module's root directory, it is included here. Use this to enable 
	 * other modules, this modules depends on
	 * 
	 * @param mixed $modules Either name or directory of module to load or array of names and directories
	 * @return void
	 * @throws Exception if directory does not exist
	 */
	public static function enable_module($modules) {
		$modules = self::to_array($modules);
		foreach($modules as $module) {
			$mod_name = $module;
			if (substr($module, 0, 1) != '/') {
				foreach(self::$module_base_dirs as $module_base_dir) {
					$module = $module_base_dir . $mod_name;
					if (file_exists($module)) {
						break;
					}
				}
			}
			if (substr($module, -1, 1) != '/') {
				$module .= '/'; 
			}
			// Path is resolved.
			if (!in_array($module, self::$module_dirs)) {
				// Don't load twice if it's alright
				if (!is_dir($module)) {
					throw new Exception("Module $module not found");
				}
				if (file_exists($module . 'enabled.inc.php')) {
					include_once($module . 'enabled.inc.php');
				}
				self::$module_dirs[$mod_name] = $module;
			}
		}
		self::$base_dir_cache = array();
	}
	
	/**
	 * Returns absolute path to given module base dir 
	 * 
	 * @param string $module
	 * @return string
	 */
	public static function get_module_dir($module) {
		return Arr::get_item(self::$module_dirs, $module, false);
	}
	
	/**
	 * An array of all modules loaded
	 *
	 * @return array
	 */
	public static function get_loaded_modules() {
		return array_keys(self::$module_dirs);
	}
	
	/**
	 * Returns true, if given module was loaded
	 *
	 * @param string $modname
	 */
	public static function is_module_loaded($modname) {
		return in_array($modname, self::get_loaded_modules());
	}
	
	/**
	 * Include files on all directories 
	 *
	 * @param mixed $fiels Either filename array of filenames
	 * @param $order Either ORDER_OVERLOAD or ORDER_DECORATORS
	 * @return bool True on success
	 */
	public static function files($files, $order = self::ORDER_OVERLOAD) {
		$files = self::to_array($files);
		$ret = true;
		foreach($files as $file) {
			$ret = $ret && self::do_include_file($file, false, $order);	
		}
		return $ret;
	}

	/**
	 * Include file, search all directories, but stop if file was found 
	 *
	 * @param mixed $files Either filename array of filenames
	 * @return bool True, if all files where found, false otherwise
	 */
	public static function first_file($files) {
		$files = self::to_array(func_get_args());
		$ret = true;
		foreach($files as $file) {
			$ret = $ret && self::do_include_file($file, true);	
		}
		return $ret;
	}
	
	/**
	 * Include file  
	 *
	 * @param string $file
	 * @param bool $include_first_only IF true, function returns on first file found
	 * @return bool True, if something was found, false otherwise
	 */
	private static function do_include_file($file, $include_first_only = false, $order = self::ORDER_OVERLOAD) {
		$found = false;
		$basedirs = self::get_base_directories($order);
		foreach($basedirs as $basedir) {
			$path = $basedir . $file;
			if (file_exists($path)) {
				include_once $path;
				$found = true;
				if ($include_first_only) {
					break; // Stop here
				}
			}
		}
		return $found;
	}
	
	/**
	 * Load a class from given directory respecting overloading
	 * 
	 * @param string $directory Subdirectory of component
	 * @param mixed $classes Either name of class to load or array of names
	 * @param string $extension File extension of class files, e.g. 'cls' will include files of name *.cls.php
	 * 
	 * @return void
	 * 
	 * @throws Exception if class is not found
	 */
	public static function classes_in_directory($directory, $classes, $extension = 'cls', $required = true) {
		return self::do_load($directory, $classes, $extension, $required);
	}
	
	/**
	 * Perform including
	 *
	 * @param string $directory Subdirectory of component
	 * @param mixed $classes Either name of class to load or array of names
	 * @param string $extension File extension of class files, e.g. 'cls' will include files of name *.cls.php
	 * @return void
	 * @throws Exception if item could not be loaded
	 */
	private static function do_load($directory, $classes, $extension = 'cls', $required = true) {
		$extension = '.' . $extension . '.php';
		$basedirs = self::get_base_directories_subdirs($directory);
		$classes = self::to_array($classes);
		$ret = false;
		foreach($classes as $class) {
			// Build relative path
			$class_path = strtolower($class) . $extension;
			// Check cache
			if (self::is_loaded($directory, $class_path)) {
				continue;
			}
			
			$found = false;
			// Test app dir
			foreach($basedirs as $basedir) {
				$path = $basedir . $class_path;
				if (file_exists($path)) {
					self::mark_loaded($directory, $class_path, $path);
					include_once $path;
					$found = true;
					$ret = true;
					break;
				}
			}
			if (!$found && $required) {
				// Exception on error
				$msg = "Classfile $class on path $directory not found";
				throw new Exception($msg);
			}
		}
		return $ret;
	}
	
	/**
	 * Find all existing subdirs in base dirs
	 */
	private static function get_base_directories_subdirs($subdirectory, $order = self::ORDER_OVERLOAD) {
		$key = $subdirectory . '#' . $order;
		$ret = Arr::get_item(self::$base_dir_subdir_cache, $key, false);
		if ($ret === false) {
			$ret = array();
			$basedirs = self::get_base_directories($order); 
			foreach($basedirs as $basedir) {
				$path = $basedir . $subdirectory . '/';
				if (file_exists($path)) {
					$ret[] = $path;
				}
			}
			self::$base_dir_subdir_cache[$key] = $ret;
		}
		return $ret;
	} 
	
	/**
	 * Returns array of all directories that can contain coponents
	 * 
	 * Dependend on $order the order of directories is: 
	 * 
	 * ORDER_OVERLOAD: application directory, module directories in descending order 
	 *                 of initialization, and the gyro core dir.
	 * ORDER_DECORATOR: core directory, module directories in order of initialization, 
	 *                  application directoy 
	 * 
	 * @param $order Either ORDER_OVERLOAD or ORDER_DECORATORS
	 * 
	 * @return array 
	 */
	public static function get_base_directories($order = self::ORDER_OVERLOAD) {
		$ret = Arr::get_item(self::$base_dir_cache, $order, false);
		if ($ret !== false) {
			return $ret;
		}
		
		$ret = self::$module_dirs;
		switch($order) {
			case self::ORDER_OVERLOAD:
				$ret = array_reverse($ret);
				array_unshift($ret, APP_INCLUDE_ABSPATH);
				$ret[] = GYRO_CORE_DIR;
				break;
			case self::ORDER_DECORATORS:
				array_unshift($ret, GYRO_CORE_DIR);
				$ret[] = APP_INCLUDE_ABSPATH;				
				break;
			default:
				throw new Exception('Invalid sort order in Load::get_base_directories');
				break;
		}

		self::$base_dir_cache[$order] = $ret;
		return $ret;
	}
	
	/**
	 * Mark a ressource as loaded 
	 *
	 * @param string $directory
	 * @param string $class
	 * @param string $path The path an item was found
	 * @return void
	 */
	private static function mark_loaded($directory, $class, $path) {
		self::$loaded[$directory][$class] = $path;
	}
	
	/**
	 * Test if a ressource was loaded 
	 *
	 * @param string $directory
	 * @param string $class
	 * @return bool True if ressource was loaded already, false otherwise
	 */
	private static function is_loaded($directory, $class) {
		return isset(self::$loaded[$directory][$class]);
	}
	
	public static function autoload($class_name) {
		$class_name = strtolower($class_name);
		if (substr($class_name, 0, 3) == 'dao') {
			self::models(substr($class_name, 3));
		}
	}
	
	
	/**
	 * Converts params into one array 
	 *
	 * @param array $params
	 * @return array
	 */
	private static function to_array($params) {
		$params = Arr::force($params);
		$ret = array();
		foreach($params as $p) {
			$ret = array_merge($ret, Arr::force($p));
		}
		return $ret;
	}
}

if (!function_exists('__autoload')) {
	function __autoload($class_name) {
		Load::autoload($class_name);
	}
}