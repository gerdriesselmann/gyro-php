<?php
Load::directories('controller/base/routes');

/**
 * Base implementation for controllers. To be extended by all controllers
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class ControllerBase implements IController {
	// Return values for action functions
	const OK = 'ok';
	const REDIRECT = 'redirect';
	const NOT_FOUND = 'not found';
	const ACCESS_DENIED = 'denied';
	const INTERNAL_ERROR = 'internal error';
	
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array();
	}

	/**
	 * Activates includes before action to reduce cache memory 
	 */ 
	public function before_action() {
	} 	
	
	/**
	 * Invoked after setting data and before actions are processed
	 */
	public function preprocess($page_data) {
	}

	/**
	 * Invoked after page content has been created
	 */ 
	public function postprocess($page_data) {
	}
	
	/**
	 * Invoked to handle events
	 */
	public function on_event($name, $params, &$result) {
	}
}
?>