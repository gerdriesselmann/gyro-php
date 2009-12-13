<?php
/**
 * Implementation of Delegate command 
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 * @deprecated
 */
class DelegateCommandImpl extends CommandDelegate {
	public function __construct($op, $obj, $params) {
		$delegate = null;
		
		$delegate_directory = dirname(dirname(__FILE__));
		$obj_name = $this->get_obj_name($obj);
		$file_name = implode('.', array($op, $obj_name, 'cmd.php'));
		$file_path = implode('/', array($delegate_directory, $obj_name, $file_name));
		if (file_exists($file_path)) {
			require_once($file_path);
			$func = 'create_' . $op  . $obj_name . '_command';
			if (function_exists($func)) {
				$delegate = $func($params, $obj);
			} 
		}
		
		parent::__construct($delegate);
	}		
	
	protected function get_obj_name($obj) {
		$ret = false;
		if (is_object($obj)) {
			if (method_exists($obj, 'tableName')) {
				$ret = $obj->tableName();
			}
			else {
				$ret = get_class($obj);
			}
		}
		else {
			$ret = basename((string)$obj);
		}
		return $ret;
	}
}
?>