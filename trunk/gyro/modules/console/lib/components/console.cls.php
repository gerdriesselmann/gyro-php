<?php
/**
 * Helper class for console invoking
 * 
 * @author Gerd Riesselmann
 * @ingroup Console
 */
class Console {
	/**
	 * Init the system to be ready for console
	 */
	public static function init() {
		if (!self::is_console_request()) {
			throw new Exception('Initialing console, but no console invoked'); 
		}	
		// Build $_GET and $_POST array
		$argv = Arr::force($_SERVER['argv']);
		$action = Arr::get_item($argv, 1, '');
		parse_str(Arr::get_item($argv, 2, ''), $query);
		$method = strtoupper(Arr::get_item($argv, 3, 'GET'));
		
		if (empty($action)) {
			$cmd = Arr::get_item($argv, 0, 'no command set');
			throw new Exception("Usage: $cmd route [arguments] [get|post]");
		}
	
		// Fake Post and Get array
		if ($method == 'POST') {
			$_POST = $query;
			$_GET = array();
		}
		else {
			$_POST = array();
			$_GET = $query;
		}
		$_GET[Config::get_value(Config::QUERY_PARAM_PATH_INVOKED)] = $action;
		
		// Disable HTTPS
		Config::set_feature(Config::ENABLE_HTTPS, false);
		
		return $action;
	}
	
	/**
	 * Returns true, if PHP was invoked from the command line, not by the web server
	 * 
	 * @return bool
	 */
	public static function is_console_request() {
		return RequestInfo::current()->is_console();
	}
	
	/**
	 * Run console with given action
	 * 
	 * @param string $action
	 * @return Status
	 */
	public static function invoke($action) {
		$call = CONSOLE_PHP_INVOCATION . ' ' . APP_INCLUDE_ABSPATH . 'run_console.php ' . $action;
		Load::commands('generics/execute.shell');
		$cmd = new ExecuteShellCommand($call);
		return $cmd->execute();
	}
}
