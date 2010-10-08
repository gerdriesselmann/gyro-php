<?php
/**
 * This routes a static page to action_static() 
 */
class StaticPageRoute extends ExactMatchRoute {
	protected $page;
	protected $action;
	
	/**
	 * Contructor
	 * 
	 * @param string $prefix Part before static page, e.g. a directory
	 * @param string $page The static page this route is responsible for
	 * @param string $prefix Part after page, e.g. ".html"
	 * @param IController $controller The controller to invoke
	 * @param string $action The function to invoke on controller
	 * @param mixed Array or single instance of IRouteDecorator 
	 */
	public function __construct($prefix, $page, $postfix, $controller, $action, $decorators = null) {
		$this->page = $page;
		$this->action = $action;
		$page_action = 'static_' . String::plain_ascii($page, '_');
		parent::__construct($prefix . $page . $postfix, $controller, $page_action, $decorators);			
	}
	
	/**
	 * Invokes given action function on given controller
	 *
	 * @param IController $controller The controller to invoke action upon
	 * @param string $funcname The function to invoke
	 * @param PageData $page_data
	 * @throws Exception if function does not exist on controller
	 * @return mixed Status
	 */
	protected function invoke_action_func($controller, $funcname, $page_data) {
		$funcname = $this->action;
		$this->check_action_func($controller, $funcname);
		return $this->controller->$funcname($page_data, $this->page);		
	}	
}