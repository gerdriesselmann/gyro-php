<?php
/**
 * A route that contains a reference to an instance
 * 
 * The route adds the type "inst" to the parameterized route.
 * However, it must be the last element and requires to declare it using "%".
 * 
 * Example: https://voting/vote/{instance:inst}%
 */
class InstanceReferenceRoute extends ParameterizedRoute {
	/**
	 * Can be overloaded to support further types
	 * 
	 * @param string $type The type of an expression
	 * @param string $params Optional params 
	 * @return string A regular expression
	 */
 	protected function build_unknown_type_regexp($type, $params) {
 		$ret = '';
 		if ($type == 'inst') {
 			$ret = '.*?'; // Any string
 		}
 		else {
 			$ret = parent::build_unknown_type_regexp($type, $params);
 		}
 		return $ret;
 	}

	/**
	 * Build the URL (except base part)
	 * 
	 * @param mixed $params Further parameters to use to build URL
	 * @return string
	 */
	protected function build_url_path($params) {
		$arr = $params;
		if ($params instanceof IDataObject) {
			// Try to find the inst type
			$regexp = '|\{([^:]*):inst\}|';
			$matches = array();
			$result = preg_match($regexp, $this->path, $matches);
			if ($result) {
				$var_name = $matches[1];
				$arr = array($var_name => $params); 
			}			
		}
		return parent::build_url_path($arr);
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
		if ($value instanceof IDataObject) {
			return parent::replace_path_variable($path, $key, InstanceReferenceSerializier::instance_to_string($value));
		}
		return parent::replace_path_variable($path, $key, $value);
	}
}
