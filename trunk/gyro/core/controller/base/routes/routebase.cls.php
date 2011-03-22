<?php
Load::directories('controller/base/cachemanager');
Load::directories('controller/base/cachemanager/headermanager');
Load::directories('controller/base/renderdecorators');
require_once dirname(__FILE__) . '/../renderer/rendererchain.cls.php';

/**
 * Basic route, which handles stub urls.
 * 
 * By handling stubs, a route defined for example 'a/b' will match 'a/b/c', too 
 * (unless there isn't another routes that matches better). The according action
 * shoulds check the PathStack of PageData for the remaining part of the URL.  
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class RouteBase implements IRoute, IDispatcher, IUrlBuilder  {
	protected $controller = null;
	protected $action = '';
	protected $path = '';
	protected $scheme = 'http';
	protected $decorators = null;
	protected $is_directory = false;
	
	/**
	 * Contructor
	 * 
	 * @param string $path The URL this route is responsible for
	 * @param IController $controller The controller to invoke
	 * @param string $action The function to invoke on controller
	 * @param mixed Array or single instance of IRouteDecorator 
	 */
	public function __construct($path, $controller, $action, $decorators = null) {
		$this->controller = $controller;
		$this->action = $action;
		// check if path contains a protocol
		$pos_scheme = strpos($path, '://');
		if ($pos_scheme !== false) {
			$this->scheme = substr($path, 0, $pos_scheme);
			if ($this->scheme == 'https' && !Config::has_feature(Config::ENABLE_HTTPS)) {
				$this->scheme = 'http';
			}
			$path = substr($path, $pos_scheme + 3);
		}
		$this->path = ($path !== '/') ? ltrim($path, '/') : $path;
		$this->is_directory = (substr($path, -1) === '/');
		$this->decorators = is_array($decorators) ? $decorators : array($decorators);
		ActionMapper::register_url($action, $this);
	}
	
	/**
	 * Split a route identifier back into controller and action
	 * 
	 * @param string $route_id
	 * @return array Associative array with two keys 'controller' and 'action'
	 */
	public static function split_route_id($route_id) {
		$tmp = explode('::', $route_id);
		return array(
			'action' => array_pop($tmp),
			'controller' => array_pop($tmp)
		);
	}

	/**
	 * Returns a suitable renderer 
	 *
	 * @param PageData $page_data The page data
	 * @return IRenderer
	 */
	public function get_renderer($page_data) {
		// Default render decorators
		$arr_default_decorator = $this->get_default_render_decorators();
		$arr_overloaded_default_decorators = $page_data->get_render_decorators($this);
		$arr_decorators = array();
		// Alllow cache managers as decorator
		foreach($this->decorators as $decorator) {
			if ($decorator instanceof ICacheManager) {
				$arr_decorators[] = new CacheRenderDecorator($decorator);
			}
			else {
				$arr_decorators[] = $decorator;
			}
		}
		$arr_decorators = array_merge($arr_decorators, $arr_overloaded_default_decorators, $arr_default_decorator);
		return new RendererChain($page_data, $arr_decorators);
	}

	/**
	 * Prepend a RenderDecorator to the list of render decorators
	 * 
	 * @param IRenderDecorator $dec
	 * @return void
	 */
	public function prepend_renderdecorator(IRenderDecorator $dec) {
		array_unshift($this->decorators, $dec);
	}
	
	/**
	 * Append a RenderDecorator to the list of render decorators
	 * 
	 * @param IRenderDecorator $dec
	 * @return void
	 */
	public function append_renderdecorator(IRenderDecorator $dec) {
		$this->decorators[] = $dec;
	}
	
	/**
	 * Returns array of renderdecorators to be append to decortators passed by controller
	 *
	 * @return array
	 */
	protected function get_default_render_decorators() {
		return array(
			new DispatcherInvokeRenderDecorator($this)
		);
	}
	
	/**
	 * Return a string that identifies this Route - e.g for debug purposes
	 */
	public function identify() {
		$ret = '';
		if ($this->controller) {
			$ret .= get_class($this->controller) . '::';
		}
		$ret .= $this->action;
		return $ret;		
	}

	/**
	 * Returns true, if this route is a directory (that is: ends with '/')
	 */
	public function is_directory() {
		return $this->is_directory;
	}
	
	/**
	 * Initialize the data passed
	 * 
	 * @param PageData $page_data
	 */
	public function initialize($page_data) {
		if ($this->scheme != 'any' && Url::current()->get_scheme() != $this->scheme) {
			// redirect to given scheme
			Url::current()->set_scheme($this->scheme)->redirect();
			exit;
		}
		
		$this->initialize_adjust_path($page_data);
		$this->initialize_cache_manager($page_data);
	}	

	/**
	 * Initialize the cache manager
	 * 
	 * @param PageData $page_data
	 */	
	protected function initialize_cache_manager($page_data) {
		// GR: TODO Obsoloete, I guess
		//if ($this->cache_manager) {
		//	$page_data->set_cache_manager($this->cache_manager);
		//}
	}	
		
	/**
	 * Adjust path during initializationh process
	 * 
	 * @param PageData $page_data
	 */
	protected function initialize_adjust_path($page_data)  {
		$pathstack = $page_data->get_pathstack();
		$pathstack->adjust($this->path);		
	}
	
	/**
	 * Weight this token against path
	 */
	public function weight_against_path($path) {
 	 	//print 'WEIGHT: ' . $this->path . ' against ' . $path . ':';

		$ret = self::WEIGHT_NO_MATCH;		
		if (String::starts_with($path, $this->path) == false) {
			return self::WEIGHT_NO_MATCH;
		}
		
		$tmp = new PathStack($path);
		if ($tmp->adjust($this->path)) {
			$ret = $tmp->count_front();
		}
		
		//print $ret .'<br />';
		return $ret;
 	}

	/**
	 * Invoke the action on controller
	 * 
	 * @param PageData $page_data
	 * @return mixed Controller status 
	 */
	public function invoke($page_data) {
		if (empty($this->controller)) {
			throw new Exception(
				tr('No controller on dispatcher %c', 'core', array('%c' => get_class($this)))
			);			
		}
		if (empty($this->action)) {
			throw new Exception(
				tr('No action on dispatcher %c', 'core', array('%c' => get_class($this)))
			);			
		}

		// Check for action
		$funcname = $this->get_action_func_name($this->action);
		
		// Invoke before-action (this is where the controller should do includes
		if (method_exists($this->controller, 'before_action')) {
			$this->controller->before_action();
		}
		// Invoke action
		$status = $this->invoke_action_func($this->controller, $funcname, $page_data);
		if (empty($status) || $status == CONTROLLER_OK) {
			if ($page_data->get_pathstack()->current()) {
				// There still are items in the path, that is the URL couldn't be processed
				$status = CONTROLLER_NOT_FOUND; 
			}
			else {
				$status = CONTROLLER_OK;
			}
		}
		$page_data->status_code = $status;		
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
		$this->check_action_func($controller, $funcname);
		return $this->controller->$funcname($page_data);		
	}
	
	/**
	 * Cehck if given action exists on controller
	 *
	 * @param object $controller The controller to invoke action on
	 * @param string $funcname Name of function
	 * @throws Exception if function does not exist on controller
	 */
	protected function check_action_func($controller, $funcname) {
		if (method_exists($controller, $funcname) == false) {
			throw new Exception(
				tr('Action %a on controller %c not found', 'core', array('%a' => $funcname, '%c' => get_class($this->controller)))
			);
		}
	}
	
	/**
	 * Return fucntion to invoke for given action
	 * 
	 * @param string Name of action
	 * @return string
	 */
	protected function get_action_func_name($action) {
		return 'action_' . $action;
	}
	
	// **************************************
	// IUrlBuilder
	// **************************************
	
	/**
	 * Build the URL
	 * 
	 * @param bool $absolute_or_relative True to build an absolute URL, false to return path only
	 * @param mixed $params Further parameters to use to build URL
	 */
	public function build_url($absolute_or_relative, $params = null) {
		$ret = $this->build_url_base($absolute_or_relative);
		$ret .= preg_replace('|/+|', '/', $this->build_url_path($params));
		$ret = rtrim($ret, '.');
		return $ret;
	}

	/**
	 * Build the URL (except base part)
	 * 
	 * @param mixed $params Further parameters to use to build URL
	 * @return string
	 */
	protected function build_url_path($params) {
		$ret = $this->path;
		if (!is_null($params)) {
			$ret .= '/';
			if (is_array($params)) {
				$ret .= implode('/', $params);
			}
			else {
				$ret .= $params;
			}
		}
		return $ret;
	}	 
	
	protected function build_url_base($absolute_or_relative) {
		if ($this->scheme != 'any' && Url::current()->get_scheme() != $this->scheme) {
			// E.g. a switch from http to https is required. Force absolute
			$absolute_or_relative = self::ABSOLUTE;
		}
		if (Url::current()->get_host() != Config::get_value(Config::URL_DOMAIN)) {
			$absolute_or_relative = self::ABSOLUTE;
		}
		$ret = Config::get_value(Config::URL_BASEDIR);
		if ($absolute_or_relative == self::ABSOLUTE) {
			switch ($this->scheme) {
				case 'https':
					$ret = Config::get_url(Config::URL_BASEURL_SAFE);
					break;
				default:
					$ret = Config::get_url(Config::URL_BASEURL);
					break;
			}
		}
		return $ret;
	}
}
