<?php
require_once dirname(__FILE__) . '/routebase.cls.php';

/**
 * A route to handle the case of no dispatch token beeing found
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class NotFoundRoute extends RouteBase {
	/** 
	 * Constructor
	 */
	public function __construct($path) {
		parent::__construct(
			$path, 
			null, 
			'', 
			new NotFoundRenderDecorator()
		);
	}

	/**
	 * Return a string that identifies this Route - e.g for debug purposes
	 */
	public function identify() {
		return '404-Not-Found';		
	}


	/**
	 * Invokes assigned controller   
	 * 
	 * @param object Page data object
	 * @return mixed Either error code or nothing 
 	 */
	public function invoke($page_data) {
		$page_data->status_code = CONTROLLER_NOT_FOUND;
		$page_data->head->title = tr('Page not found', 'core');		
	}
}
?>