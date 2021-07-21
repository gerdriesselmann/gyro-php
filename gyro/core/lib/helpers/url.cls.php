<?php
/**
 * Wrapper around URL handling and processing
 *
 * if Config option UNICODE_URLS is set to true, the class
 * accepts unicode domains as valid. Else Unicode domain
 * names will return false when validated.
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
	const PARAMS_UNCHANGED = 'unchanged';

	/**
	 * When parsing, accept only HTTP URLs, and force so
	 */
	const HTTP_ONLY = 0;

	/**
	 * When parsing, try a broader approach and accept all protocols
	 */
	const ALL_PROTOCOLS = 1;
	
	/**
	 * When comparing URLs, ignore only fragment
	 */
	const EQUALS_FULL = 0;
	/**
	 * When comparing URLs, ignore query (and fragment)
	 */
	const EQUALS_IGNORE_QUERY = 1;
	
	
	private $data = array();
	private $support_unicode_domains = false;
	/**
	 * Keep track if an empty query is found during parsing, for URLs
	 * like http://example.com/index.html? (which indicates another
	 * resource than http://example.com/index.html)	 *
	 */
	private $has_empty_query = false;

	/**
	 * Constructor
	 *
	 * @param string $url
	 * @param string $fallback_host Hostname used if no host in given URL
	 * @param int $policy Defines wether the URL is forces to HTTP or not
	 *
	 * @internal param The $string URL to wrap around
	 */
	public function __construct($url = '', $fallback_host = '', $policy = self::HTTP_ONLY) {
		$this->support_unicode_domains = Config::has_feature(Config::UNICODE_URLS);
		$this->parse($url, $fallback_host, $policy);

	}

	/**
	 * This wraps PHP parse_url.
	 *
	 * @param string $url
	 * @param string $fallback_host host to use if URL is relative
	 * @param int $policy IF set to HTTP_ONLY, it will only parse http/https URLs
	 * @return array
	 */
	protected function do_parse_url($url, $fallback_host = '', $policy = self::HTTP_ONLY) {
		// Make http default protocol
		$has_protocol = true;
		if ($policy === self::ALL_PROTOCOLS) {
			$pos_slash = strpos($url, '/');
			$pos_double_colon = strpos($url, ':');
			if ($pos_slash === false) {
				$has_protocol = $pos_double_colon !== false;
			} else {
				$has_protocol == ($pos_double_colon !== false) && $pos_double_colon < $pos_slash;
			}
		} else {
			if (strpos($url, '://') === false) {
				$has_protocol = false;
				if (substr($url, 0, 1) == '/') {
					$url = $fallback_host . $url;
				}
			}
		}
		if (!$has_protocol) {
			$url = 'http://' . $url;
		}
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

	/**
	 * Parse URL into Urls
	 * @param $url
	 * @param string $fallback_host Host do use if URL does not provide any
	 * @param int $policy If set to HTTP_ONLY, it will only parse http/https URLs
	 */
	protected function parse($url, $fallback_host = '', $policy = self::HTTP_ONLY) {
		$url = trim($url);
		$data = array();
		if (!empty($url)) {
			$data = $this->do_parse_url($url, $fallback_host, $policy);
		}
		
		$this->set_scheme(Arr::get_item($data, 'scheme', 'http'));
		$this->set_host(Arr::get_item($data, 'host', $fallback_host));
		$this->set_port(Arr::get_item($data, 'port', ''));
		$this->set_path_internal(Arr::get_item($data, 'path', ''));
		$this->set_fragment(Arr::get_item($data, 'fragment', ''));
		$this->set_user_info(Arr::get_item($data, 'user', ''), Arr::get_item($data, 'pass', ''));

		$query = Arr::get_item($data, 'query', '');
		$this->set_query($query);
		if (empty($query) && GyroString::contains($url, '?')) {
			$this->has_empty_query = true;
		}
	}

	public static function decode_path($path) {
		return str_replace(array('%252F', '%253F', '%2526'), array('%2F', '%3F', '%26'), $path);
	}

	/**
	 * @param $path
	 * @return string
	 */
	public static function encode_path($path) {
		return str_replace(array('%2F', '%3F', '%26'), array('%252F', '%253F', '%2526'), $path);
	}

	/**
	 * Split a query into items and return them as associative array
	 * 
	 * @param string $query
	 * @return UrlQueryItem[]
	 */
	protected function parse_query($query) {
		$ret = array();
		if ($query === '') {
			return $ret;
		}
		
		// Input separator may be a list of chars!
		$sep = ini_get('arg_separator.input');
		$l = GyroString::length($sep);
		if ($l > 1) {
			// We have a list, take first char as separator and replace all others with it 
			$all_seps = $sep;
			$sep = GyroString::substr($all_seps, 0, 1);			
			for ($i = 1; $i < $l; $i++) {
				$query = str_replace(GyroString::substr($all_seps,$i, 1), $sep, $query);
			}
		}
		
		// Now split query and process it
		$arrItems = explode($sep, $query);
		foreach($arrItems as $query_item) {
			$arr = explode('=', $query_item, 2);
			$pname = GyroString::convert($arr[0]);
			$pvalue = (count($arr) > 1) ? $arr[1] : '';

			$has_assignment = GyroString::contains($query_item, '=');
			$ret[] = new UrlQueryItem($pname, $pvalue, $has_assignment);
		}
		
		return $ret;
	}

	/**
	 * Serialize in a friendly format
	 *
	 * @return array
	 * @throws Exception
	 */
	public function __sleep() {
		$this->url = $this->build();
		return array('url');
	}

	/**
	 * Called to unserialize
	 */
	public function __wakeup() {
		$this->parse($this->url); 
	}
	
	/**
	 * Compare this URL to an other
	 * 
	 * @param string|Url $other Other URL
	 * @param int $mode Either EQUALS_FULL or EQUALS_IGNORE_QUERY
	 * @return bool
	 */
	public function equals($other, $mode = self::EQUALS_FULL) {
		$check_against = ($other instanceof Url) ? $other : Url::create($other);
		$ret = true;
		$ret = $ret && $this->get_path() == $check_against->get_path();
		$ret = $ret && $this->get_host() == $check_against->get_host();
		$ret = $ret && $this->get_port() == $check_against->get_port();
		$ret = $ret && $this->get_scheme() == $check_against->get_scheme();
		if ($mode == self::EQUALS_FULL) {
			$ret = $ret && $this->get_query() == $check_against->get_query();
		}
		return $ret;
	}
	
	/**
	 * Returns true if the URL is empty (that is no host is specified)
	 */
	public function is_empty() {
		return $this->data['host'] === '';
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
	 * @param string $url Path
	 * @param int $policy Either HTTP_ONLY or ALL_PROTOCOLS
	 *
	 * @return Url
	 */
	public static function create($url, $policy = self::HTTP_ONLY) {
		return new Url($url, '', $policy);
	}
	
	/**
	 * Create new Url instance. If url's host is empty use the given one
	 * 
	 * @param string $url Path
	 * @param string $host Fallback host
	 * @param int $policy Either HTTP_ONLY or ALL_PROTOCOLS
	 * @return Url
	 */
	public static function create_with_fallback_host($url, $host, $policy = self::HTTP_ONLY) {
		return new Url($url, $host, $policy);
	}
		
	/**
	 * Replaces or adds parameter to query string. Returns this
	 *
	 * @param String Parameter name
	 * @param String Paremeter value
	 * @return Url Reference to self
	 */
	public function replace_query_parameter($name, $value) {
		$query = $this->data['query'];
		// remove old params
		$query = array_filter(
			$query,
			function(UrlQueryItem $item) use ($name) {
				return $item->name != $name;
			}
		);
		$only_unset = $value === '' || $value === false;
		if (!$only_unset) {
			$values = Arr::force($value, false);
			foreach($values as $v) {
				$query[] = new UrlQueryItem($name, $v);
			}
		}
		$this->data['query'] = $query;
		$this->has_empty_query = false;
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
		$path = ltrim($path, '/');
		return $this->set_path_internal($path);
	}

	private function set_path_internal($path) {
		if (GyroString::starts_with($path, '/')) {
			$path = GyroString::substr($path, 1);
		}
		$this->data['path'] = $path;
		return $this;
	}
	
	/**
	 * Return the path only
	 */
	public function get_path() {
		return $this->data['path'];				
	}
	
	/**
	 * Set query as string
	 * 
	 * @param string $query
	 * @return Url
	 */
	public function set_query($query) {
		$this->data['query'] = $this->parse_query($query);
		$this->has_empty_query = false;
		return $this;
	}
	
	/**
	 * Return full query
	 *
	 * @return string
	 */
	public function get_query($encode = Url::ENCODE_PARAMS) {
		/* @var UrlQueryItem[] $params */
		$params = $this->data['query'];
		$s_params = array();
		foreach($params as $param) {
			$s_params[] = $param->build($encode);
		}

		$sep = html_entity_decode(ini_get('arg_separator.output'), ENT_QUOTES, GyroLocale::get_charset());
		return implode($sep, $s_params);
	}

	/**
	 * Return query paramter
	 * @param $name
	 * @param bool $default
	 * @param string $encode
	 * @return mixed
	 */
	public function get_query_param($name, $default = false, $encode = Url::NO_ENCODE_PARAMS) {
		$params = $this->get_query_params($encode);
		$value = Arr::get_item($params, $name, $default);
		if (is_array($value) && !GyroString::contains($name, '[]')) {
			// If there are double parameters, but parameter is no array, return first
			// value to simulate PHP behavior
			$value = Arr::get_item($value, 0, $default);
		}
		return $value;
	}

	/**
	 * @deprecated
	 *
	 * Callback to urlencode values - does not actually walk
	 *
	 * @param string $value The value to encide
	 * @param mixed $key The key, can be used by subclasses
	 */
	protected function callback_urlencode(&$value, $key = false) {
		if (!is_null($value)) {
			$value = str_replace(' ', '+', urlencode(urldecode($value)));
		}
	}

	/**
	 * Return query parameters  as associative array
	 *
	 * @param string $encode Encoding mode
	 * @return array
	 */
	public function get_query_params($encode = Url::NO_ENCODE_PARAMS) {
		$ret = array();
		/* @var UrlQueryItem[] $params */
		$params = Arr::get_item($this->data, 'query', array());
		foreach($params as $param) {
			$pname = $param->name;
			$pvalue = $param->encoded_value($encode);
			if (isset($ret[$pname])) {
				// append to existing
				$existing = $ret[$pname];
				if (!is_array($existing)) {
					$existing = array($existing);
				}
				$existing[] = $pvalue;
				$ret[$pname] = $existing;
			} else {
				$ret[$pname] = $pvalue;
			}
		}
		return $ret;
	}

	/**
	 * Return scheme (http, ftp etc)
	 */
	public function get_scheme() {
		return $this->data['scheme'];
	}

	/**
	 * Set scheme (http, ftp etc)
	 *
	 * @param string $scheme
	 * @return Url
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
		return $this->data['host']; 
	}

	/**
	 * Set Host
	 *
	 * @param string $host
	 * @return Url
	 */
	public function set_host($host) {
		$this->data['host'] = GyroString::to_lower($host);
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
	 *
	 * @param array $arr_host
	 * @return Url
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
	 * @return array Associative array
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
			// no GyroString::xxxx wrappers. They perform notably faster.
			foreach($tlds as $tld) {
				$l_tld_check = strlen($tld) + 1; // +1 is for the '.' we will add later on
				// A valid domain name is x.[TLD], so the host must be at least by one
				// char longer than the ".[TLD]"  
				if ($l_tld_check >= $l_host) {
					// Impossible match...
					continue;
				} 
				
				// The below is equal to (GyroString::ends_with($host, '.' . $tld))
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
					}
					break;
				}
			}
			if (empty($ret['tld'])) {
				//No TLD found in list. Since virtually any TLD domain can be registered nowadays, just split stuff at dots
				$tmp = explode('.', $host);
				$ret['tld'] = array_pop($tmp);
				$ret['sld'] = array_pop($tmp);
				$ret['subdomain'] = implode('.', $tmp);
			}
			$ret['domain'] = $ret['sld'] . '.' . $ret['tld'];
		}
		unset($tlds); // Saves Memory, I think.
		return $ret;
	}

	public function set_user_info($user, $password) {
		if ($user || $password) {
			$this->data['user_info'] = "$user:$password";
		} else {
			$this->data['user_info'] = '';
		}
	}

	public function get_user_info() {
		return $this->data['user_info'];
	}

	public function set_port($port) {
		$this->data['port'] = ($port) ? intval($port) : $port;
		return $this;
	}
	
	public function get_port() {
		return $this->data['port'];
	}
	
	/**
	 * Return fragment (stuff after "#")
	 */
	public function get_fragment() {
		return $this->data['fragment'];
	}

	/**
	 * Set fragment (stuff after "#")
	 *
	 * @param string $fragment
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
		
		$src_host = $this->get_host();
		if ($this->support_unicode_domains) {
			$src_host = idn_to_ascii($src_host,0, INTL_IDNA_VARIANT_UTS46);
		}
		if ($ret && !Validation::is_ip($src_host)) {
			$ret = $ret && (preg_match('|^([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+$|i', $src_host) != 0);
			if ($ret) {
				$host = $this->parse_host();
				$ret = $ret && !empty($host['tld']);
				$ret = $ret && !empty($host['domain']); 
			}
		}
				
		return $ret; 
	}

	/**
	 * Returns this query as a string
	 *
	 * The URL is not ready for outputting it on an HTML page, it must be HTMLescaped before! It is however URL escaped.
	 *
	 * @param string $mode
	 * @param string $encoding
	 *
	 * @return string This Url as a string.
	 * @throws Exception Throws an exception if hostname is empty
	 */
	public function build($mode = Url::ABSOLUTE, $encoding = Url::ENCODE_PARAMS) {
		$out = '';
		
		if ($mode == Url::ABSOLUTE) {
			$out .= $this->get_scheme();
			$out .= '://';
		
			$user = $this->get_user_info();
			if ($user) {
				$out .= $user . '@';
			}
			
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
		
		$out .= '/';
		$out .= $this->get_path();

		$query = $this->get_query($encoding);
		if (!empty($query) || $this->has_empty_query) {
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
	 * @throws Exception Throws an exception if hostname is empty
	 */
	public function output() {
		$out = $this->build(true);
		print $out;
		return $this;
	}

	/**
	 * Prints this query as a string
	 *
	 * @return string
	 * @throws Exception Throws an exception if hostname is empty
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
		$this->data['path'] = GyroString::plain_ascii($ret);
		return $this;	
	}

	/**
	 * Redirect to this Url
	 *
	 * @param bool If true, a permanent, else a temporary redirect is done
	 * @throws Exception
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
			if (Config::has_feature(Config::TESTMODE)) {
				Common::send_backtrace_as_headers();
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
		$this->has_empty_query = false;
		return $this;
	}

	/**
	 * Returns true, if this URL is identical or below the given $path_to_check
	 *
	 * E.g. Checking an URL of /a/b/c against /a/b would return true, checking against /a/b/c/d would return false
	 *
	 * @param string $path_to_check
	 * @return bool
	 */
	public function is_ancestor_of($path_to_check) {
		$path_to_check = trim($this->clean_path_for_comparison($path_to_check), '/');
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

	/**
	 * Returns true, if this URL is identical the given $path_to_check
	 *
	 * Query and fragment are ignored. Other than equals() this function works on a path or even a malformed
	 * URL. It's similar to is_ancestor_of() in how it works.
	 *
	 * @param string $path_to_check
	 * @return bool
	 */
	public function is_same_as($path_to_check) {
 	 	$path_to_check = $this->clean_path_for_comparison($path_to_check);
		$current = ltrim($this->get_path(), '/');

	  	return $current == $path_to_check;
	}

	protected function clean_path_for_comparison($path_to_check) {
		foreach(array('?', '#') as $remove) {
			$pos = strpos($path_to_check, $remove);
			if ($pos !== false) {
				$path_to_check = substr($path_to_check, 0, $pos);
			}
		}

		$path_to_check = ltrim($path_to_check, "/");
		return $path_to_check;
	}

	/**
	 * Validate current URL, fix possible problems by redirecting to canonical form
	 *
	 * If there are no problems, code wull continue
	 *
	 * @throws Exception
	 */
	public static function validate_current() {
		if (!empty($_POST)) {
			return;
		}
		if (RequestInfo::current()->is_console()) {
			return;
		}
		
		$url = Url::current();
		$path = trim($url->get_path());
		
		if ($path == Config::get_value(Config::URL_BASEDIR)) {
			return;
		}


		$pathclean = $path;
		//$pathclean = trim(str_replace('%20', '', $path), '/'); // created endless circles of redirects
		//$pathclean = trim($path, '/');
		//$pathclean = str_replace('%20', '', $path);
		$pathclean = preg_replace('@/+@', '/', $pathclean);

		$dirs = explode('/', $pathclean);
		$dirsclean = array();
		for ($i = 0; $i < sizeof($dirs); $i++) {
			$dir_to_test = $dirs[$i];
			if ('.' === $dir_to_test) {
				continue;
			} else if ('..' === $dir_to_test && $i > 0 && '..' != $dirsclean[sizeof($dirsclean) - 1]) {
				array_pop($dirsclean);
				continue;
			} else {
				$dir_to_test = Url::encode_path(
					rawurlencode(rawurldecode($dir_to_test))
				);
				array_push($dirsclean, $dir_to_test);
			}
		}
		$pathclean = implode('/', $dirsclean);
		$url->set_path($pathclean);

		$test_1 = $url->build(Url::ABSOLUTE, Url::NO_ENCODE_PARAMS);
		$test_2 = Url::current()->build(Url::ABSOLUTE, Url::NO_ENCODE_PARAMS);
		//$test_1 = urldecode($test_1);
		//$test_2 = Url::encode_path(rawurldecode($test_2));
		if ($test_1 !== $test_2) {
			if (Config::has_feature(Config::TESTMODE)) {
				$url->redirect(self::TEMPORARY);
			} else {
				$url->redirect(self::PERMANENT);
			}
			exit();
		}

		$pos = GyroString::strpos($path, '&'); 
		if ($pos !== false) {
			$path = GyroString::left($path, $pos) . '?' . GyroString::substr($path, $pos + 1);
			$url->set_path($path);
			$url->redirect(self::PERMANENT);
			exit();
		}
	}
}

class UrlQueryItem {
	/**
	 * @var string Name of item (a in a=b)
	 */
	public $name;
	/**
	 * @var string Value of item (b in a=b)
	 */
	public $value;
	/**
	 * @var bool If there is assignent operator, to distinct between a= and a
	 */
	private $force_assignment = false;

	/**
	 * UrlQueryItem constructor.
	 * @param string $name
	 * @param string $value
	 * @param bool $force_assignment
	 */
	public function __construct($name, $value, $force_assignment = false) {
		$this->name = $name;
		$this->value = $value;
		$this->force_assignment = $force_assignment;
	}

	/**
	 * Build query item as string
	 *
	 * @param string $encode How to encode params
	 * @return string
	 */
	public function build($encode) {
		$ret = $this->name;
		if ($this->value || $this->force_assignment) {
			$ret .= '=';
		}
		$ret .= $this->encode_string_param($this->value, $encode);
		return $ret;
	}

	/**
	 * Value encoded with given encoding methid
	 *
	 * @param string $encode How to encode params
	 * @return string
	 */
	public function encoded_value($encode) {
		return $this->encode_string_param($this->value, $encode);
	}

	private function encode_string_param($value, $encode) {
		switch ($encode) {
			case Url::ENCODE_PARAMS:
				return str_replace(' ', '+', urlencode(urldecode($value)));
			case Url::NO_ENCODE_PARAMS:
				return GyroString::convert(urldecode($value));
			case Url::PARAMS_UNCHANGED:
			default:
				// Do nothing
				return $value;
		}
	}
}