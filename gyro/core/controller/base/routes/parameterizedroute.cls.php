<?php

require_once dirname(__FILE__) . '/routebase.cls.php';

/**
 * Allows defining routes that contain wildcards
 *
 * A simple example: 
 * user/{id:ui>}/{page:ui}.html!
 * 
 * This will extract $id and $page from the URL and pass it as named variables to the 
 * according controller action:
 * 
 * public function action_user($page_data, $id, $page = 0);
 *
 * You may also use parameters that are not handled by your actions. They will be silently ignored, when the action
 * is called.
 *
 * The wildcards are of type name[:type][:params] where type is optional and defauls to string. Params default to FALSE
 *
 * The following types are supported:
 *
 * i: integer
 * ui: unsigned integer
 * ui>: integer > 0
 * s: string (default). You can specify a length like so: s:2 - String of two ANSI characters
 * sp: ASCII string. Value gets converted to plain ascii before substituting it. You can specify a length like so: sp:2.
 * e: enum. Usage is e:value1,value2,value3,..,valueN
 *
 *
 * Two meta types are available:
 *
 * _class_: Gets replaced by the class name of an instance passed as parameter
 * _ model_: Gets replaced by te model name of an instance passed as parameter
 *
 * Both meta types cannot be bound to variables. You must use the string type "s"
 *
 * It is allowed fo the action function to only handle some, but not all parameter. You should validate the URL using
 *
 * @code
 * ActionHandler::validate_against_current()
 * @endcode
 *
 * to avoid different URL for the same content.
 * 
 * The parametrized route supports optional elements, but only at the end of the query
 * 
 * A terminating ! means this part of the URL is optional. Be sure to define a default value in the 
 * controller action called. 
 * 
 * You may use the % to indicate arbitrary further path elements, or * if you want arbitrary many elements 
 * that additionally are optional.
 * 
 * This definition would match any URL that starts with "user" and an ID:
 *
 * @code
 * user/{id:ui>}/{path:s}*
 * @endcode
 * 
 * For example 
 * - user/5/profile/images would be translated to id = 5 and path = 'profile/images'
 * - user/5/ would be translated to id = 5 and path = ''
 * 
 * This definition would behave different:
 *
 * @code
 * user/{id:ui>}/{path:s}%
 * @endcode
 * 
 * For example 
 * - user/5/profile/images would be translated to id = 5 and path = 'profile/images'
 * - user/5/ would be causing a 404 not found, unless you defined another matching route
 * 
 * If you use the exclamation mark, only one element is allowed
 * 
 * @code
 * user/{id:ui>}/{path:s}!
 * @endcode
 * 
 * - user/5/profile would be translated to id = 5 and path = 'profile'
 * - user/5/profile/images would be causing a 404 not found, unless you defined another matching route
 * - user/5/ would be translated to id = 5 and path = ''
 *
 * Using the ActionMapper, variables are replaced by either an object's property of the same name or an
 * array member of same key. If a variable name end on (), it is treated as a function, if ActionMapper is passed
 * an object.
 *
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ParameterizedRoute extends RouteBase {
	/**
	 * Associative array with parameter naem as key and parameter value as value.
	 * 
	 * E.g. Given a route /user/{id:ui>} is invoked as /user/200. 
	 * $params will contain the key-value-pair "id" => 200
	 *  
	 * @var array
	 */
	private $params = array();
	/**
	 * Associative array with parameter name as key and parameter type as value.
	 * 
	 * E.g. Given a route /user/{id:ui>} is invoked as /user/200. 
	 * $types will contain the key-value-pair "id" => "ui>"
	 *  
	 * @var array
	 */
	private $types = array();
	
	/**
	 * Associative array with type as key and IParameterizedRouteHandler as value
	 * @var array
	 */
	private static $handlers = array();
	
	/**
	 * Weight this token against path
	 */
	public function weight_against_path($path) {
		$def_pathstack = new PathStack($this->path); // Definition
		$url_pathstack = new PathStack($path);   // URL to process

		$ret = self::WEIGHT_NO_MATCH;
		// Do some simple plausibility check
		$simple_check = true;
		$cf_delta = $url_pathstack->count_front() - $def_pathstack->count_front(); 
		$lastchar = substr(rtrim($this->path, '/'), -1);
		switch ($lastchar) {
			case '!':
				$simple_check = ($cf_delta == 0 || $cf_delta == -1);
				break;
			case '%':
				$simple_check = ($cf_delta >= 0);
				break;
			case '*':
				$simple_check = ($cf_delta >= -1);
				break;
			default:
				$simple_check = ($cf_delta == 0);
				break;
		}
		
		if ($simple_check) {
			$ret = self::WEIGHT_FULL_MATCH;
			$count = 0;		
			while ($def_elem = $def_pathstack->shift()) {
				$count++;
				$url_elem = '';
				switch (substr($def_elem, -1)) {
					case '*':
					case '%':
						//$ret = $count;
						$url_elem = $url_pathstack->implode_front();
						$url_pathstack->clear_front();
						break;
					default:
						$url_elem = $url_pathstack->shift();
						break;
				}
								
				if ($this->validate($def_elem, $url_elem) == false) {
					$ret = self::WEIGHT_NO_MATCH;
					break;
				}
			}
		}

		return $ret;
 	}

	/**
	 * Initialize the data passed
	 * 
	 * @param PageData $page_data
	 */
	protected function initialize_adjust_path($page_data) {
		// GR, * processes complete URL into variable, no need for path stack 
		$page_data->get_pathstack()->clear_front();
	}	
	
	/**
	 * Return array of types per param
	 *  
	 * @return array
	 */
	protected function get_types() {
		return $this->types;
	}
	
	/**
	 * Return handler for given type
	 * 
	 * @return IParameterizedRouteHandler  
	 */
	protected function get_handler($type) {
		return Arr::get_item(self::$handlers, $type, false);
	}
	
	/**
	 * Return handler for given key
	 * 
	 * @return IParameterizedRouteHandler  
	 */
	protected function get_handler_for_key($key) {
		$types = $this->get_types();
		$type = Arr::get_item($types, $key, 's');
		return $this->get_handler($type);
	}
	
	/**
	 * Add a value and a type
	 */
	private function add_value_and_type($name, $value, $type) {
		$this->params[$name] = $value;
 		$this->types[$name] = $type;		
	}
	 	
 	/**
 	 * Validate if a given portion of the input path matches the definition of the defined path
 	 *
 	 * @param string $this_path_elem Element of definition path
 	 * @param string $element_to_validate Element of input path
 	 * @return boolean
 	 * @throws Exception If definition is illegal
 	 */
 	private function validate($this_path_elem, $element_to_validate) {
 		// Catch simple cases
 		if ($this_path_elem == '*') {
 			return true;
 		}
 		if (strpos($this_path_elem, '{') === false) {
 			return ($this_path_elem == $element_to_validate);
 		}
 		$optional = (substr($this_path_elem, -1) === '!'); 		
 		if ($optional && $element_to_validate === false) {
 			return true;
 		}
 
 		$tag = '#\{[^\}]*\}#';
		// Split on tags - we have ANSI data, so use native functions
		$texts = preg_split($tag, $this_path_elem,  -1);
		// Find all tags
		$tags = '';
		preg_match_all($tag, $this_path_elem, $tags);
		$tags = $tags[0];
		// we now have two arrays, one for text between tags ($texts),
		// and one for the tags itself ($tags)
 		
 		$names = array();
 		$types = array();
 		$regexp = $this->build_validate_regexp($texts, $tags, $names, $types);
 		$matches = array();
 		if (preg_match($regexp, $element_to_validate, $matches)) {
 			if (count($matches) == count($names) + 1) {
 				foreach($names as $key => $name) {
 					$this->add_value_and_type($name, $matches[$key + 1], array_shift($types));
 				}
 				return true;
 			}
 		}
 		return false;
 	}
 	
 	/**
 	 * Build a regular expression to validate any string against the rules passed as 
 	 * array of texts and tokens
 	 * 
 	 * @param array $arr_texts Array of hard coded texts
 	 * @param array $arr_expr Array of expressions like id:ui>
 	 * @param array $arr_names Array to contain the names of the expressions passed
 	 * @return string The regular expression  
 	 */
 	private function build_validate_regexp($arr_texts, $arr_expr, &$arr_names, &$arr_types) {
 		$ret = '';
 		$c = count($arr_expr);
 		for($i = 0; $i < $c; $i++) {
 			$ret .= preg_quote(array_shift($arr_texts));
 			$expr = trim($arr_expr[$i], '{}');
 			$name = '';
 			$reg_ex = $this->build_expresssion_regexp($expr, $name, $type);
 			$ret .= '(' . $reg_ex . ')';
 			$arr_names[] = $name;
 			$arr_types[] = $type;
 		}
 		$last = array_shift($arr_texts);
 		switch ($last) {
 			case '*':
 			case '%':
 			case '!':
 				break; 				
 			default:
 				$ret .= preg_quote($last);
 				break;
 		}
 		$ret = '#^' . $ret . '$#';
 		return $ret;
 	}
 	
 	/**
 	 * Build a regular expression for a given expression
 	 * 
 	 * @param string $expression The expression to turn into a regex
 	 * @param string $name The name of the expression to return
 	 * @return string A regular expression
 	 */ 	
 	private function build_expresssion_regexp($expression, &$name, &$type) {
 		$arr_expression = explode(':', $expression);
 		$name = Arr::get_item($arr_expression, 0, '');
 		$type = Arr::get_item($arr_expression, 1, 's');
 		$params = Arr::get_item($arr_expression, 2, false);
 		if (empty($name)) {
 			throw new Exception(tr('Illegal expression name found: %i', 'core', array('%i' => $expression)));
 		}
 		
 		$handler = $this->get_handler($type);
 		if ($handler) {
			return $handler->get_validate_regex($params); 			
 		}
 		else {
 			return $this->build_unknown_type_regexp($type, $params);	
 		}
 	}

	/**
	 * Can be overloaded to support further types
	 * 
	 * @param string $type The type of an expression
	 * @param string $params Optional params 
	 * @return string A regular expression
	 */
 	protected function build_unknown_type_regexp($type, $params) {
 		throw new Exception(tr('Illegal expression type found: %t', 'core', array('%t' => $type)));
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
		return $this->call_user_func_named($controller, $funcname, $page_data, $this->params);		
	}

	/**
	 * Invoke given function on controller with given params
	 *
	 * @param IController $controller
	 * @param string $function
	 * @param PageData $page_data
	 * @param array $params
	 * @return mixed
	 */
	protected function call_user_func_named($controller, $function, $page_data, $params) {
		$reflect = new ReflectionMethod($controller, $function);
    	$params['page_data'] = $page_data;
    	$real_params = array();
	    foreach ($reflect->getParameters() as $i => $param) {
	        $pname = $param->getName();
    	    if (array_key_exists($pname, $params)) {
        	    $real_params[] = $params[$pname];
        	}
			else if (array_key_exists($pname . '()', $params)) {
				// function calls
				$real_params[] = $params[$pname . '()'];
			}
        	else if ($param->isDefaultValueAvailable()) {
            	$real_params[] = $param->getDefaultValue();
        	}
        	else {
            	throw new Exception('Call to function ' . $function . ' missing parameter ' . $pname);
        	}
	    }
    	return call_user_func_array(array($controller, $function), $real_params);
	}

	// **************************************
	// IUrlBuilder
	// **************************************
	
	/**
	 * Build the URL (except base part)
	 * 
	 * @param mixed $params Further parameters to use to build URL
	 * @return string
	 */
	protected function build_url_path($params) {
		$path = $this->path;
		$variables = $this->extract_path_variables($path);
		if (is_object($params)) {
			$path = $this->replace_path_variable($path, '_class_', get_class($params));
			if ($params instanceof IDBTable) {
				$path = $this->replace_path_variable($path, '_model_', $params->get_table_name());
			}
			unset($variables['_class_']);
			unset($variables['_model_']);
			foreach($variables as $v) {
				if (substr($v, -2) == '()') {
					$func_name = substr($v, 0, -2);
					if (method_exists($params, $func_name)) {
						$path = $this->replace_path_variable($path, $v, $params->$func_name());
					}
				} else {
					$path = $this->replace_path_variable($path, $v, $params->$v);
				}
			}
		} else if (is_array($params)) {
			foreach($variables as $v) {
				if (array_key_exists($v, $params)) {
					$path = $this->replace_path_variable($path, $v, $params[$v]);
				}
			}
		}

		// replace optional
		$reg_optional_asterix = '#\{[^\}]*\}\*#';
		$reg_optional_exklamation = '#\{[^\}]*\}\!#';
		$path = preg_replace($reg_optional_asterix, '', $path);
		$path = preg_replace($reg_optional_exklamation, '', $path);
		
		return $path;
	}

	/**
	 * Get all variables from path
	 *
	 * @param string $path
	 * @return array
	 */
	protected function extract_path_variables($path) {
		$tag = '#\{([^\}]*)\}#';
		$tags = array();
		$ret = array();
		preg_match_all($tag, $path, $tags);
		foreach($tags[1] as $t) {
			$ret[] = array_shift($php54_strict_requires_a_variable_here = explode(':', $t));
		}
		return array_unique($ret);
	}

	/**
	 * Replace a variable in the path with a given value
	 *
	 * @param string $path
	 * @param string $key
	 * @param mixed $value
	 * @return string
	 */
	protected function replace_path_variable($path, $key, $value) {
		if (is_object($value) || is_array($value)) {
			return $path;
		}

		// Replace otional type with string type. Reduces RegExp complexity,
		// since we now can force a ":" after key and before type
		$path = str_replace('{' . $key . '}', '{' . $key . ':s}', $path);

		// Figure out type of $key
		$key = preg_quote($key);
		$reg = '#\{' . $key . ':([^:\}]*)[:]?.*?\}#';
		$matches = array();
		if (preg_match($reg, $path, $matches)) {
			$handler = $this->get_handler($matches[1]);
			if ($handler) {
				$value = $handler->preprocess_build_url($value);
			}
		}
		
		$reg = '#\{' . $key . ':[^}]*\}[*%]#';
		$replace = implode('/', array_map(array($this, 'preprocess_replace_value'), explode('/', Cast::string($value))));
		$path = preg_replace($reg, $replace, $path);
		$reg = '#\{' . $key . ':[^}]*\}!?#';
		$replace = $this->preprocess_replace_value($value);
		$path = preg_replace($reg, $replace, $path); 

		return $path;
	}
	
	/**
	 * Preprocess a value before it gets inserted into URL
	 */
	protected function preprocess_replace_value($value) {
		return str_replace(array('%2F', '%3F', '%26'), array('%252F', '%253F', '%2526'), urlencode(Cast::string($value)));
	}
	
	// ---------------------------------------------
	// Static part
	// ---------------------------------------------
	
	/**
	 * Autoload handlers
	 */
	public static function init_handlers() {
		$files = Load::get_files_from_directory('controller/base/routes/parameterizedroutehandlers', '*.handler.php');
		foreach($files as $file => $inc) {
			include_once $inc;
			$cls = Load::filename_to_classname($file, 'ParameterizedRouteHandler', 'handler');
			if (!class_exists($cls)) {
				throw new Exception('ParameterizedRoute: ' . $inc . ' does not define class ' . $cls);	
			}
			self::add_handler(new $cls());
		}
	}
	
	/**
	 * Add a handler
	 */
	public static function add_handler(IParameterizedRouteHandler $handler) {
		self::$handlers[$handler->get_type_key()] = $handler;
	} 
}

ParameterizedRoute::init_handlers();
