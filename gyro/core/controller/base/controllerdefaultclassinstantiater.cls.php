<?php
/**
 * Class to detect controller classed and create one instance of each 
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ControllerDefaultClassInstantiater implements IClassInstantiater {
	public function get_all() {
		$ret = array();
		$dirs = Load::get_base_directories();
		foreach($dirs as $dir) {
			$ret = array_merge($ret, $this->instantiate_direcory($dir));
		}
		return $ret;
	} 
	
	protected function instantiate_direcory($directory) {
		$ret = array();
		foreach (gyro_glob($directory . 'controller/*.controller.php') as $inc) {
			include_once($inc);
			// Detect controller name from filename
			// ".", "-" and "_" get translated to camel case:
			// index.base.controller.php => IndexBaseController
			$controllername = basename($inc, '.php');
			$controllername = strtr($controllername, '.-_', '   ');
			$controllername = ucwords($controllername);
			$controllername = str_replace(' ', '', $controllername);
			if (class_exists($controllername)) {
				$ret[] = new $controllername;
			}
			else {
				throw new Exception(tr('Controller %c not found', 'core', array('%c' => $controllername))); 			  	
			}
		}
		return $ret;
	}
}
