<?php
require_once dirname(__FILE__) . '/parameterizedroute.cls.php';

/**
 * Handle Command invocation (Check tokens!) 
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class CommandsRoute extends ParameterizedRoute {
	/**
	 * Contructor
	 * 
	 * @param object The controller to invoke
	 * @param string The function to invoke on controller
	 * @param string The URL this Token is responsible for
	 * @param mixed Array or single instance of IRouteDecorator 
	 */
	public function __construct($path, $controller, $action, $decorators = null) {
		parent::__construct($path, $controller, $action, $decorators = null);
		$this->prepend_renderdecorator(new CommandsRouteRenderDecorator());		
	}	
}