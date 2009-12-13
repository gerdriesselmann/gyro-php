<?php
/**
 * The commands factory instanciates commands, following the rul for overloading commands.
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
	 * @return ICommand
	 */
	public static function create_command($obj, $cmd_name, $params) {
		$cmd = false;
		if ($obj === '') {
			$obj = 'app';
		}
		$inst_name = '';
		$cmd_file_name = $cmd_name . '.cmd.php';
		
		$cmd_class_name_fragment = Load::filename_to_classname($cmd_name);
		
		if ($obj instanceof IActionSource) {
			// Load instance specific
			$inst_name = $obj->get_action_source_name();
		} 
		else if (is_string($obj)) {
			$inst_name = $obj;			
		}
		
		if (!empty($inst_name)) {
			$file = $inst_name . '/' . $cmd_file_name;
			$class = $cmd_class_name_fragment . String::to_upper($inst_name, 1) . 'Command';
			
			//var_dump($file, $class);
			$cmd = self::do_create_command($obj, $params, $file, $class);
		}
		
		if ($cmd == false) {
			// Load generic
			$file = 'generics/' . $cmd_file_name;
			$class = $cmd_class_name_fragment . 'Command';
			$cmd = self::do_create_command($obj, $params, $file, $class);
		}
		
		return $cmd;
	}
	
	private static function do_create_command($obj, $params, $filename, $classname) {
		if (Load::first_file('behaviour/commands/' . $filename)) {
			if (class_exists($classname)) {
				return new $classname($obj, $params);
			}
		}
		return false;
	}
} 
?>