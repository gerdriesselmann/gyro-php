<?php
/**
 * Wrapper around URL handling and processing
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Url {
	const TEMPORARY = false;
	const PERMANENT = true;
	
	const ABSOLUTE = 'absolute';
	const RELATIVE = 'relative';
	
	const ENCODE_PARAMS = 'encode';
	const NO_ENCODE_PARAMS = 'encodenot';
	
	private $data = array();
	
	/**
	 * Constructor
	 * 
	 * @param string The URL to wrap around
	 */
	public function __construct($url = '') {
		$this->parse($url);
	}
	
	/**
	 * This wraps PHP parse_url.
	 *
	 * @param string $url
	 * @return array
	 */
	protected function do_parse_url($url) {
		// Make http default protocol
		if (strpos($url, '://') === false) {
			$url = 'http://' . $url; 
		}
		$ret = false;
		try {
			$ret = @parse_url($url);
		}
		catch (Exception $ex) {
			// IF APP_THROW_ON_WARNING is enabled, we must catch!
			$ret = array();			
		}
		if ($ret === false) {
			$ret = array();
		}
		return $ret;
	}

	protected function parse($url) {
		$url = trim($url);
		if (!empty($url)) {
			$this->data = $this->do_parse_url($url);
			
			// transform query into associative array
			$query = Arr::get_item($this->data, 'query', '');
			$this->data['query'] = array();

			/*
			// GR: What is disadvantage of this code? 
			// - Will understand arrays, this class however won't (get_query_params(), get_query_param()) - which maybe is a bug of Url class. 
			// - "my value=something" wil become array['my_value' => 'something'], note the underscore
			// - Does not respect setting of arg_seperator.input
			//
			// Left here for testing purposes, though
			$temp = array();
			parse_str($query, $temp);
			foreach($temp as $key => $value) {
				$pname = String::convert(urldecode($key));
				$pvalue = String::convert(urldecode($value));
				if (!empty($pname)) {
					$this->data['query'][$pname] = $pvalue;
				}
			}
			return;
			*/
			
			// Input separator may be a list of chars!
			$sep = ini_get('arg_separator.input');
			$l = String::length($sep);
			if ($l > 1) {
				// We have a list, take first char as separator and replace all others with it 
				$all_seps = $sep;
				$sep = String::substr($all_seps, 0, 1);			
				for ($i = 1; $i < $l; $i++) {
					$query = str_replace(String::substr($all_seps,$i, 1), $sep, $query);
				}
			}
			// print $sep;
			$arrItems = explode($sep, $query);
			foreach($arrItems as $query_item) {
				$arr = explode('=', $query_item, 2);
				$pname = String::convert(urldecode($arr[0]));
				$pvalue = (count($arr) > 1) ? String::convert(urldecode($arr[1])) : '';
				if (!empty($pname)) {
					if (substr($pname, -2) == '[]') {
						$this->data['query'][$pname][] = $pvalue;
					}
					else {
						$this->data['query'][$pname] = $pvalue;
					}
				}
			}
		}
	}
	
	/**
	 * Serialize in a freindly format
	 *
	 * @return unknown
	 */
	public function __sleep() {
		$this->url = $this->build();
		return array('url');
	}
	
	public function __wakeup() {
		$this->parse($this->url); 
	}
	
	/**
	 * Returns true if the URL is empty
	 */
	public function is_empty() {
		return count($this->data) == 0;
	}
	
	/**
	 * Static returns the current URL.
	 * 
	 * @return Url
	 */
	public static function current() {
		static $_url = false;
		if ($_url === false) {
			$_url = new Url(RequestInfo::current()->url_invoked());
		}
		return clone($_url);
	}	
	
	/**
	 * Create new Url instance
	 * 
	 * @param string Path 
	 * @return Url
	 */
	public static function create($url) {
		return new Url($url);
	}
		
	/**
	 * Replaces or adds parameter to query string. Returns this
	 *
	 * @param String Parameter name
	 * @param String Paremeter value
	 * @return Url Reference to self
	 */
	public function replace_query_parameter($name, $value) {
		if ($value === '' || $value === false) {
			unset($this->data['query'][$name]);
		}
		else {
			$this->data['query'][$name] = $value;
		}
		return $this;
	}
	
	/**
	 * Replace an array of paramters at once
	 * 
	 * @param array $arr_params Associativea array
	 * @return Url
	 */
	public function replace_query_parameters($arr_params) {
		foreach($arr_params as $key => $value) {
			$this->replace_query_parameter($key, $value);
		}
		return $this;
	}
	
	/**
	 * Set the path for this url
	 * 
	 * @param string The new path
	 * @return Url Reference to self  
	 */
	public function set_path($path) {
		$this->data['path'] = '/' . ltrim($path, '/');
		return $this;
	}
	
	/**
	 * Return the path only
	 */
	public function get_path() {
		return ltrim(Arr::get_item($this->data, 'path', ''), '/');				
	}
	
	/**
	 * Return full query
	 */
	public function get_query($encode = Url::ENCODE_PARAMS) {
		$sep = html_entity_decode(ini_get('arg_separator.output'), ENT_QUOTES, GyroLocale::get_charset());
		$ret = '';
		foreach($this->get_query_params($encode) as $key => $value) {
			$this->query_reduce($ret, $sep, $key, $value);
		}
		return $ret;
	}
	
	/**
	 * Reduces set of params to query string (a=b&c=d...)
	 * 
	 * @param string $current Recent output 
	 * @param string $sep Separator
	 * @param string $key Name of param
	 * @param mixed $value Value of param 
	 */
	protected function query_reduce(&$current, $sep, $key, $value) {
		if ($key) {
			if (is_array($value)) {
				foreach($value as $v) {
					$this->query_reduce($current, $sep, $key, $v);
				}
			}
			else {
				if ($current) {
					$current .= $sep;
				}
				$current .= $key . '=' . $value;
			}
		}
	}

	/**
	 * Return query paramter
	 */
	public function get_query_param($name, $default = false, $encode = Url::NO_ENCODE_PARAMS) {
		$ret = Arr::get_item($this->get_query_params(Url::NO_ENCODE_PARAMS), $name, $default);
		if ($encode == Url::ENCODE_PARAMS) {
			if (is_array($ret)) {
				array_walk_recursive($ret, array($this, 'array_walk_urlencode'));
			}
			else {
				$this->array_walk_urlencode($ret);
			}
		}
		return $ret;		
	}
	
	/**
	 * Callback to urlencode values
	 */
	protected function array_walk_urlencode(&$value, $key = false) {
		$value = urlencode($value);
	}

	/**
	 * Return query paramters  as associative array
	 */
	public function get_query_params($encode = Url::NO_ENCODE_PARAMS) {
		$ret = Arr::get_item($this->data, 'query', array());
		if ($encode == Url::ENCODE_PARAMS) {
			array_walk_recursive($ret, array($this, 'array_walk_urlencode'));
		}
		return $ret;
	}

	/**
	 * Return scheme (http, ftp etc)
	 */
	public function get_scheme() {
		return Arr::get_item($this->data, 'scheme', 'http');
	}

	/**
	 * Set scheme (http, ftp etc)
	 */
	public function set_scheme($scheme) {
		$this->data['scheme'] = $scheme;
		return $this;
	}
	
	/**
	 * Return host (e.g. "www.example.com")
	 */
	public function get_host() {
		// to_lower() removed, since it is already done in setter
		return Arr::get_item($this->data, 'host', ''); 
	}
	
	/**
	 * Set Host
	 * 
	 * @return Url
	 */
	public function set_host($host) {
		$this->data['host'] = String::to_lower($host);
		return $this;
	}
	
	/**
	 * Set host data from array
	 * 
	 * The array posted should be an associative array with these members: 
	 * 
	 * tld => (semi) top level domain like 'com' or 'co.uk'
	 * sld => second level domain like 'example' in www.example.com
	 * domain => sld.tld - Only if tld and sld are ommitted!
	 * subdomain => rest, e.g. 'www' from www.example.com
	 */
	public function set_host_array($arr_host) {
		$arr_temp = $this->parse_host();
		if (isset($arr_host['subdomain'])) {
			$arr_temp['subdomain'] = $arr_host['subdomain'];
		}
		if (isset($arr_host['domain'])) {
			$arr_temp['domain'] = $arr_host['domain'];
		}
		else {
			if (isset($arr_host['tld'])) {
				$arr_temp['tld'] = $arr_host['tld'];
			}
			if (isset($arr_host['sld'])) {
				$arr_temp['sld'] = $arr_host['sld'];
			}
			unset($arr_temp['domain']);
		}
		
		// Build...
		$arr_build = array();
		if (!empty($arr_temp['subdomain'])) {
			$arr_build[] = $arr_temp['subdomain'];
		}
		if (!empty($arr_temp['domain'])) {
			$arr_build[] = $arr_temp['domain'];
		}
		else {
			if (!empty($arr_temp['sld'])) {
				$arr_build[] = $arr_temp['sld'];
			}
			if (!empty($arr_temp['tld'])) {
				$arr_build[] = $arr_temp['tld'];
			}
		}

		return $this->set_host(implode('.', $arr_build));		
	}
	
	/**
	 * Returns the host split into an array.
	 * 
	 * The array returns has five members
	 * 
	 * tld => (semi) top level domain like 'com' or 'co.uk'
	 * sld => second level domain like 'example' in www.example.com
	 * domain => sld.tld
	 * subdomain => rest, e.g. 'www' from www.example.com
	 * data => Array of parts, like ('www', 'example', 'com')
	 * 
	 * @return Array Associative array
	 */
	public function parse_host() {
		$host = $this->get_host();
		$ret = array(
			'tld' => '',
			'sld' => '',
			'domain' => '',
			'subdomain' => '',
			'data' => explode('.', $host)
		);
		$l_host = strlen($host);  // Cache string length
		if ($l_host > 0) {
			require_once(dirname(__FILE__) . '/data/tld.lst.php');
			$tlds = get_tlds();
			// We do not have utf 8 here, so we can use native string functions,
			// no String::xxxx wrappers. They perform notably faster.
			foreach($tlds as $tld) {
				$l_tld_check = strlen($tld) + 1; // +1 is for the '.' we will add later on
				// A valid domain name is x.[TLD], so the host must be at least by one
				// char longer than the ".[TLD]"  
				if ($l_tld_check >= $l_host) {
					// Impossible match...
					continue;
				} 
				
				// The below is equal to (String::ends_with($host, '.' . $tld))
				if (substr($host, -$l_tld_check, $l_tld_check) === '.' . $tld) {
					$ret['tld'] = $tld;
					$tmp = explode('.', $tld);
					$count_data = count($ret['data']);
					$count_tld = count($tmp);
					$index_domain = $count_data - $count_tld - 1; // -1 is for 0-based
					if ($index_domain >= 0) {
						$ret['sld'] = $ret['data'][$index_domain];
						$arr_subdomain = array();
						for($i = 0; $i < $index_domain; $i++) {
							$arr_subdomain[] = $ret['data'][$i];
						}
						$ret['subdomain'] = implode('.', $arr_subdomain);
						$ret['domain'] = $ret['sld'] . '.' . $ret['tld'];
					}
					break;
				}
			} 
		}
		unset($tlds); // Saves Memory, I think.
		return $ret;
	}
	
	public function set_port($port) {
		$this->data['port'] = ($port) ? Cast::int($port) : $port;
		return $this;
	}
	
	public function get_port() {
		return Arr::get_item($this->data, 'port', '');
	}
	
	/**
	 * Return fragment (stuff after "#")
	 */
	public function get_fragment() {
		return Arr::get_item($this->data, 'fragment', '');
	}

	/**
	 * Set fragment (stuff after "#")
	 * 
	 * @return Url
	 */
	public function set_fragment($fragment) {
		$this->data['fragment'] = $fragment;
		return $this;
	}
	
	
	/**
	 * Returns true if this is a valid URL
	 */
	public function is_valid() {
		$ret = !$this->is_empty();
		$ret = $ret && (preg_match('|^([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+$|i', $this->get_host()) != 0);
		
		if ($ret) {
			$host = $this->parse_host();
			$ret = $ret && !empty($host['tld']);
			$ret = $ret && !empty($host['domain']); 
		}
				
		return $ret; 
	}
	
	/**
	 * Returns this query as a string
	 *
	 * The URL is not ready for outputting it on an HTML page, it must be HTMLescaped before! It is however URL escaped.
	 * 
	 * @return string This Url as a string.   
	 * @exception Throws an exception if hostname is empty
	 */
	public function build($mode = Url::ABSOLUTE, $encoding = Url::ENCODE_PARAMS) {
		$out = '';
		
		if ($mode == Url::ABSOLUTE) {
			$out .= $this->get_scheme();
			$out .= '://';
		
			$host = $this->get_host();
			if (empty($host)) {
				throw new Exception('Url: No Host specified!');
			}
			$out .= $host;
			$port = $this->get_port();
			if ($port) {
				$out .= ':' . $port; 
			}
		}
		
		$path = $this->get_path();
		if (!empty($path)) {
			$path = '/' . $path;
		}
		else if ($mode == Url::RELATIVE) {
			$out .= '/';
		}
		$out .= $path;
				 
		$query = $this->get_query($encoding);
		if (!empty($query)) {
			$out .= '?' . $query;
		}
		
		$anchor = $this->get_fragment();
		if (!empty($anchor)) {
			$out .= '#' . $anchor;
		}
		
		return $out;
	}
	
	/**
	 * Prints this query as a string
	 *
	 * @return Url Reference to self  
	 * @exception Throws an exception if hostname is empty
	 */
	public function output() {
		$out = $this->build(true);
		print $out;
		return $this;
	}
	 	
	/**
	 * Prints this query as a string
	 *
	 * @return void  
	 * @exception Throws an exception if hostname is empty
	 */
	public function __toString() {
		return $this->build();
	}                       
		
	/**
	 * Remove all non-ASCII chars from the path (not the query!)
	 * 
	 * @return url Reference to this 
	 */
	function clean() {
		$ret = Arr::get_item($this->data, 'path', '');
		$this->data['path'] = String::plain_ascii($ret);
		return $this;	
	}
	
	/**
	 * Redirect to this Url
	 * 
	 * @param bool If true, a permanent, else a temporary redirect is done
	 */
	public function redirect($permanent = self::TEMPORARY) {
		if (headers_sent() == false) {
			$address = 'Location: ' . $this->build();
			if ($permanent == self::PERMANENT) {
				Common::send_status_code(301); // Moved Permanently
			}
			else {
				Common::send_status_code(302); // Moved Temporarily
			}
			session_write_close(); // Fixes some issues with Sessions not getting save on redirect
			header($address);
			exit;
		}
		else {
			throw new Exception('Url: Redirect to ' . $this->build() . ' not possible, headers already sent'); 
		}
	}
	
	/**
	 * Remove query parameters
	 * 
	 * @return Url Reference to self
	 */
	public function clear_query() {
		$this->data['query'] = array();
		return $this;
	}
	
	/**
	 * Returns true, if this URL is identical or below the given $path_to_check
	 * 
	 * E.g. Checking an URL of /a/b/c against /a/b would return true, checking against /a/b/c/d would return false
	 */
	public function is_ancestor_of($path_to_check) {
		foreach(array('?', '#') as $remove) {
			$pos = strpos($path_to_check, $remove);
			if ($pos !== false) {
				$path_to_check = substr($path_to_check, 0, $pos);
			}
		}
		
 	 	$path_to_check = trim($path_to_check, "/");
		$current = trim($this->get_path(), '/');
  	
	  	$ret = false; 
	  	if (!empty($current) && !empty($path_to_check) && strpos($current . '/', $path_to_check . '/') === 0) {
	  		$ret = true;
	  	}
	  	else if (empty($current) && empty($path_to_check)) {
	  		$ret = true;
	  	}		
	  	
	  	return $ret;
	}
	
	public static function validate_current() {
		if (!empty($_POST)) {
			return;
		}
		
		$url = Url::current();
		$path = trim($url->get_path());
		
		if ($path == Config::get_value(Config::URL_BASEDIR)) {
			return;
		} 
		
		//$pathclean = trim(str_replace('%20', '', $path), '/'); // created endless circles of redirects
		//$pathclean = trim($path, '/');
		$pathclean = str_replace('%20', '', $path);
		$pathclean = str_replace('//', '/', $pathclean);
		
		$dirs = explode('/', $pathclean);
		$dirsclean = array();
		for ($i = 0; $i < sizeof($dirs); $i++) {
			if ('.' === $dirs[$i]) {
				continue;
			}
			if ('..' === $dirs[$i] && $i > 0 && '..' != $dirsclean[sizeof($dirsclean) - 1]) {
				array_pop($dirsclean);
				continue;
			}
			array_push($dirsclean, $dirs[$i]);
		}
		$pathclean = implode('/', $dirsclean);
		if ($pathclean != $path) {
			$url->set_path($pathclean)->redirect(self::PERMANENT);
			exit();
		} 
		
		$pos = String::strpos($path, '&'); 
		if ($pos !== false) {
			$path = String::left($path, $pos) . '?' . String::substr($path, $pos + 1);
			$url->set_path($path);
			$url->redirect(self::PERMANENT);
			exit();
		}
	}
}
?>
