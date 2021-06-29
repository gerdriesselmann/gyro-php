<?php
Load::components('eventsource');

/**
 * The Router
 *
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class RouterBase implements IEventSink {
	private $path_invoked = '';

	/**
	 * Array of controllers
	 */
	protected $controllers = array();

	/**
	 * The dipatch token invoked
	 *
	 * @var IRoute
	 */
	protected $current_route = null;

	/**
	 * Constructor. Loads all controllers
	 */
	public function __construct($class_instantiater) {
		$potential_controllers = $class_instantiater->get_all();
		foreach ($potential_controllers as $controller) {
			if ($controller instanceof IController) {
				$this->controllers[] = $controller;
			}
		}
		EventSource::Instance()->register($this);
	}

	/**
	 * Initialize page data
	 */
	public function initialize($page_data) {
		$path = $this->get_path();
		//if (GyroString::ends_with($path, '/')) {
		//	$path = trim($path, '/');
		//}
		$this->path_invoked = $path;
		$page_data->set_path($this->path_invoked);
		$page_data->router = $this;
	}

	/**
	 * Looks for a handler of given url and invokes it
	 *
	 * @return IRoute
	 */
	public function route() {
		$token = $this->find_route($this->path_invoked);
		if (empty($token)) {
			$token = new NotFoundRoute($this->path_invoked);
		}
		else {
			// SImualte Apache behaviout that a becomes a/ if a/ is defiend, but a not
			$path_current = Url::current()->get_path();
			$current_is_dir = (substr($path_current, -1) === '/');
			$route_is_dir = $token->is_directory();

			if ($route_is_dir && !$current_is_dir) {
				Url::current()->set_path($path_current . '/')->redirect(Url::PERMANENT);
			}
			else if (!$route_is_dir && $current_is_dir) {
				Url::current()->set_path(rtrim($path_current, '/'))->redirect(Url::PERMANENT);
			}
		}
		$this->current_route = $token;
		return $token;
	}

	/**
	 * Returns the action invoked
	 */
	public function get_route_id() {
		$ret = '';
		if ($this->current_route) {
			$ret = $this->current_route->identify();
		}
		return $ret;
	}

	/**
	 * Prepares all controllers
	 *
	 * @param string Data of current page
	 */
	public function preprocess($page_data) {
		foreach ($this->controllers as $controller) {
			$controller->preprocess($page_data);
		}
	}

	/**
	 * Do what's necessary after page was processed, e.g. create sidebar items...
	 */
	public function postprocess($page_data) {
		foreach ($this->controllers as $controller) {
			$controller->postprocess($page_data);
		}
	}

	/**
	 * Try to find matching controller
	 */
	protected function find_route($path) {
		if ($path === '') {
			return null;
		}
		$best_matching_route = null;
		$best_weight = IRoute::WEIGHT_NO_MATCH;

		foreach ($this->controllers as $controller) {
			$routes = $controller->get_routes();
			foreach ($routes as $route) {
				$weight = $route->weight_against_path($path);
				//print $route->identify() . ': ' . $weight . '..' . $best_weight . '<br />';
				if ($weight < $best_weight) {
					$best_matching_route = $route;
					$best_weight = $weight;
				}
			}
		}

		return $best_matching_route;
	}

	/**
	 * Returns the current path, preprocessed
	 *
	 * If index page is invoked, '.' is returned
	 *
	 * @return string The current path, e.g. path/to/page
	 */
	protected function get_path() {
		// Check if index.php was invoked directly
		//if (RequestInfo::current()
		//	->url_invoked(RequestInfo::RELATIVE) == Config::get_url(Config::URL_BASEDIR) . 'index.php'
		//) {
		//	return '';
		//}

		//$path = Arr::get_item($_GET, Config::get_value(Config::QUERY_PARAM_PATH_INVOKED), '.');

		// Switching to Url::current solves both the problem of detecting "index.php" from above
		// and some strange behaviour on (F)CGI machines, where
		// http://www.example.com/blah%3Fa%3Db gets passed as http://www.example.com/blah?a=b
		// whereas at the same time, the SERVER array contains the (correct!) original %3Fa%3Db,
		// which leads to confusion and may cause duplicate content URLs.
		$path = Url::current()->get_path();
		if ($path === '') {
			$path = '.';
		}
		return $path;
	}

	/**
	 * Invoke event on all controllers
	 */
	public function on_event($name, $params, &$result) {
		$ret = new Status();
		foreach ($this->controllers as $controller) {
			$ret->merge($controller->on_event($name, $params, $result));
		}
		if ($name == 'debugblock' && $params == 'properties') {
			$result['Route'] = $this->current_route ? $this->current_route->identify() : '-';
			$controllers = array_map(
				function($c) { return get_class($c); },
				$this->controllers
			);
			$result['Controllers'] = implode(', ', $controllers);
		}
		return $ret;
	}
}
