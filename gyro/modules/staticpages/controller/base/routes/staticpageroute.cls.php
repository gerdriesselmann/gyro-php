<?php
/**
 * This routes a static page to action_static() 
 */
class StaticPageRoute extends ExactMatchRoute {
	protected $org_page;
	protected $org_action;
	
	/**
	 * Contructor
	 * 
	 * @param string $prefix Part before static page, e.g. a directory
	 * @param string $page The static page this route is responsible for
	 * @param string $postfix Part after page, e.g. ".html"
	 * @param IController $controller The controller to invoke
	 * @param string $action The function to invoke on controller
	 * @param mixed $decorators Array or single instance of IRouteDecorator
	 */
	public function __construct($prefix, $page, $postfix, $template, $controller, $action, $decorators = null) {
		$this->org_page = $template;
		$this->org_action = $action;
		$page_action = 'static_' . $page;

		$path = $prefix . $page . $postfix;
		if (STATICPAGES_ENFORCE_DIR && empty($postfix)) {
			$append_no_slash = false;
			$last_char = substr($page, -1, 1);
			$append_no_slash = $append_no_slash ||  $last_char === '/';
			if (!$append_no_slash) {
				$pos_dot = strrpos($page, '.');
				if ($pos_dot !== false) {
					$pos_slash = strrpos($page, '/');
					$has_ending = $pos_dot > $pos_slash; // if $pos_slash is false, this is still true
					$append_no_slash = $append_no_slash || $has_ending;
				}
			}
			if (!$append_no_slash) {
				$path .= '/';
			}
		}
		parent::__construct($path, $controller, $page_action, $decorators);
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
		$funcname = $this->get_action_func_name($this->org_action);
		$this->check_action_func($controller, $funcname);
		return $this->controller->$funcname($page_data, $this->org_page);		
	}	
}