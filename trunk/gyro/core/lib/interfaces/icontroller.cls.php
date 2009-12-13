<?php
require_once dirname(__FILE__) . '/ieventsink.cls.php';

define ('CONTROLLER_OK', 'ok');
define ('CONTROLLER_REDIRECT', 'redirect');
define ('CONTROLLER_NOT_FOUND', 'not found');
define ('CONTROLLER_ACCESS_DENIED', 'denied');
define ('CONTROLLER_INTERNAL_ERROR', 'internal error');

/**
 * Controler interface
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IController extends IEventSink {
	/**
	 * Return array of IRoute which are handled by this controller
	 * 
	 * @return array Array of IRoute that handle url calls
	 * 
	 * \code
 	 * return array(
	 *   new ExactMatchRoute('.', $this, 'page_index', 'Welcome To TripGuru'),
	 *   new ExactMatchRoute('help', $this, 'page_help', 'Help on Finding your Trip'),
	 * );
 	 * \endcode
	 * 
	 * Callback function has signature action_[name]($data).
 	 */
	public function get_routes();
		
	/**
	 * Invoked after setting data and before actions are processed
	 */
	public function preprocess($page_data);

	/**
	 * Invoked after page content has been created
	 */ 
	public function postprocess($page_data);
}
