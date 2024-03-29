<?php
/**
 * Class that returns informations about current request
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class RequestInfo {
	const ABSOLUTE = 'absolute';
	const RELATIVE = 'relative';	
	
	/**
	 * Cached current request info 
	 *
	 * @var RequestInfo
	 */
	private static $_current = null;
	protected $data = array();
	
	public function __construct($server_array) {
		$this->data = $server_array;	
	}
	
	/**
	 * Returns info about current request
	 * 
	 * @return RequestInfo
	 */
	public static function current() {
		if (is_null(self::$_current)) {
			self::$_current = new RequestInfo($_SERVER);
		}
		return clone(self::$_current);
	}
	
	/**
	 * Create instance of RequestInfo 
	 *
	 * @param array $data_array Array that follows rules for $_SERVER
	 * @return RequestInfo
	 */
	public static function create($data_array) {
		return new RequestInfo($data_array);
	}

	/**
	 * Returns true, if request was done using HTTPS
	 *
	 * @return bool
	 */
	public function is_ssl() {
		$https = Arr::get_item($this->data, 'HTTPS', false);
		if ($https === false) {
			// Not set, e.g. by nginx
			return (Arr::get_item($this->data, 'SERVER_PORT', '') == '443');
		} else {
			return $https != 'off';
		}
	}
	
	/**
	 * Returns true, if current request was done form command line
	 *
	 * @return bool
	 */
	public function is_console() {
		$protocol = Arr::get_item($_SERVER, 'SERVER_PROTOCOL', false);
		return empty($protocol);		 
	}
	
	/**
	 * Returns the URL invokes as a string, either absolute or relative
	 *
	 * @param string $type
	 */
	public function url_invoked($type = self::ABSOLUTE) {
		$ret = '';
		if ($this->is_console()) {
			$ret = $this->compute_cl_invoked($type);
		}
		else {
			$ret = $this->compute_url_invoked($type);
		}
		return $ret;
	}
	
	/**
	 * Computes URL invokes
	 *
	 * @param string $type
	 * @return string
	 */
	protected function compute_url_invoked($type) {
		$ret = '';
		// This is the original URL passed to IIS 
		// http://martin.bz.it/post/2008/03/Url-Rewriting-PHP-und-IIS.aspx
		$ret .= Arr::get_item($this->data, 'HTTP_X_REWRITE_URL', '');
		if ($ret === '') {
			// This is the original URL, passed to apache
			$ret .= Arr::get_item($this->data, 'REQUEST_URI', '');
		}
		if ($ret === '') {
			// No rewriting, no request uri, build URL by hand 
			$ret .= Arr::get_item($this->data, 'SCRIPT_NAME', '');
			if ($ret === '') {
				$ret .= Arr::get_item($this->data, 'PHP_SELF', '');
			}
			$query = Arr::get_item($this->data, 'QUERY_STRING', '');
      		if ($query !== '') {
         		$ret .= '?' . $query;
			}
		}		
		if ($type == self::ABSOLUTE) {
			$prefix = $this->is_ssl() ? 'https://' : 'http://';
			// Check proxy forwarded stuff
			$prefix .= Arr::get_item(
				$_SERVER, 'HTTP_X_FORWARDED_HOST', Arr::get_item(
					$_SERVER, 'HTTP_HOST', Config::get_value(Config::URL_DOMAIN)
				)
			);
			$ret = $prefix . $ret;
		}
		return $ret;
	}

	/**
	 * Computes commadn line invoked
	 *
	 * @param string $type
	 * @return string
	 */
	protected function compute_cl_invoked($type) {
		$ret = 'http://local/php ';
		$ret .= implode(' ', Arr::get_item($_SERVER, 'argv', array()));
		return $ret;				
	}
	
	/**
	 * Returns request method (GET, POST, PUT, DELETE, HEAD etc.) 
	 *
	 * @return string
	 */
	public function method() {
		return strtoupper(Arr::get_item($this->data, 'REQUEST_METHOD', 'GET'));
	}

	/**
	 * Returns if this request was forwarded or not
	 *
	 * @return bool
	 */
	public function is_forwarded() {
		return isset($this->data['HTTP_X_FORWARDED_FOR']);
	}
	
	/**
	 * Client IP Address
	 *
	 * By default returns IP the request was forwarded for, if any.
	 *
	 * @attention Note the IP may be empty!
	 * 
	 * @return string
	 */
	public function remote_address($use_forwarded_if_available = true) {
		$ret = '';
		// Check for X-Forwarded-For use REMOTE_ADDR as fallback
		$in = Arr::get_item($this->data, 'REMOTE_ADDR', '');
		if ($use_forwarded_if_available) {
			$in = Arr::get_item($this->data, 'HTTP_X_FORWARDED_FOR', $in);
		}
		if ($in) {
			// May be comma separeted list (in case of proxies involved)
			$arr_ips = explode(',', $in);
			foreach($arr_ips as $ip) {
				if (Validation::is_ip($ip)) {
					$ret = $ip;
					break;
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Client Host, if resolvable. If not, remote IP is returned
	 * 
	 * @return string
	 */
	public function remote_host($use_forwarded_if_available = true) {
		$ret = '';
		$read_from_forwarded = $use_forwarded_if_available && $this->is_forwarded();
		if (!$read_from_forwarded) {
			// Do not read host, if forwarded, This is included in remote_address() fallback
			$ret = Arr::get_item($this->data, 'REMOTE_HOST', '');
		}
		if ($ret === '') {
			$ret = $this->remote_address($use_forwarded_if_available);
			if ($ret) {
				$ret = @gethostbyaddr($ret);
			}
		}
		return $ret;
	}
	
	/**
	 * Returns user agent
	 *
	 * @return string
	 */
	public function user_agent() {
		return $this->header_value('User-Agent');
	}

	/**
	 * Returns value of given HTTP request header
	 * @param string $name
	 * @return string
	 */
	public function header_value($name) {
		$translated_name = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
		return trim(Arr::get_item($this->data, $translated_name, ''));
	}

	/**
	 * Referer as string
	 *
	 * @return string
	 */
	public function referer() {
		return Arr::get_item($this->data, 'HTTP_REFERER', '');
	}
}
