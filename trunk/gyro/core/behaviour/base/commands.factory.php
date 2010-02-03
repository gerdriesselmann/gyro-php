<?php
/**
 * The commands factory instanciates commands, following the rules for overloading commands.
 * 
 * Commands are overloaded by placing correctly named class files in according directories.
 * 
 * Files are loaded form the following directories in the order given below
 * 
 * 1.) app/behaviour/commands/[type]
 * 2.) [modules]/behaviour/commands/[type]
 * 3.) [core]/behaviour/commands/[type]  
 * 4.) app/behaviour/commands/generics
 * 5.) [modules]/behaviour/commands/generics
 * 6.) [core]/behaviour/commands/generics  
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandsFactory {
	/**
	 * Return the strategy to change status of given object to $new_status 
	 * 
	 * @return ICommand Returns the command or FALSE, if no command was found
	 */
	public static function create_command($obj, $cmd_name, $params) {
		$inst_name = self::get_instance_name($obj);
		
		$key = 'cmdfac#' . $inst_name . '#' . $cmd_name;
		$cls = RuntimeCache::get($key, null);		
		if (is_null($cls)) {
			$cls = self::get_command_class($inst_name, $cmd_name);
			RuntimeCache::set($key, $cls);
		}
		
		if ($cls) {
			return new $cls($obj, $params);
		}
		
		return false;
	}

	/**
	 * Get string for instance requesting command
	 */	
	private static function get_instance_name($obj) {
		if ($obj === '') {
			return 'app';
		}
		if ($obj instanceof IActionSource) {
			// Load instance specific
			return $obj->get_action_source_name();
		} 
		if (is_string($obj)) {
			return $obj;			
		}
		return false;
	}
	
	/**
	 * Try to determine the commands class name
	 */
	private static function get_command_class($inst_name, $cmd_name) {
		$cmd_class_name_fragment = Load::filename_to_classname($cmd_name);
		
		$ret = false;
		if (!empty($inst_name)) {
			$file = $inst_name . '/' . $cmd_name;
			$class = $cmd_class_name_fragment . ucfirst($inst_name) . 'Command';
			$ret = self::do_find_command($file, $class);
		}
		
		if ($ret === false) {
			// Load generic
			$file = 'generics/' . $cmd_name;
			$class = $cmd_class_name_fragment . 'Command';
			$ret = self::do_find_command($file, $class);
		}
		
		return $ret;		
	}
	
	/**
	 * Find given Command using file and class
	 */
	private static function do_find_command($filename, $classname) {
		$ret = false;
		$ok = class_exists($classname); 
		if (!$ok) {
			Load::classes_in_directory('behaviour/commands/', $filename, 'cmd', false);
		}			
		if ($ok || class_exists($classname)) {
			$ret = $classname;
		}
		return $ret;
	}
} 
