<?php
/**
 * Configures a HTTP Request
 */
class GyroHttpRequestConfig {
	const GET = "GET";
	const HEAD = "HEAD";
	const POST = "POST";
	const PUT = "PUT";
	const DELETE = "DELETE";

	const BODY_FORM_URLENCODED = 'FORM_URLENCODED';
	const BODY_JSON = "JSON";

	/**
	 * @var string HTTP method. One of GET, POST, HEAD or PULL
	 */
	public $method = self::GET;

	/**
	 * @var mixed Body of request, usually an associative array
	 */
	public $body;

	/**
	 * @var string Content type of body data
	 */
	public $body_mime_type = self::BODY_FORM_URLENCODED;

	/**
	 * @var string User for authentication, if any
	 */
	public $user;
	/**
	 * @var string Password for authentication, if any
	 */
	public $password;

	/**
	 * @var int Timeout in seconds
	 */
	public $timeout_sec = 30;

	/**
	 * @var bool If set to true do not verify SSL integrity (e.g. accept outdated certificates)
	 */
	public $ssl_no_verify = false;
	/**
	 * @var bool If set to true, do not treat HTTP error status codes (400 and above) as errors
	 */
	public $no_error_on_4xx_5xx = false;

	/**
	 * Array of additional HTTP headers
	 *
	 * @var string[] Array of additional headers
	 */
	public $headers = array();

	public function __construct($method) {
		$this->method = $method;
	}

	public static function get() {
		return new GyroHttpRequestConfig(self::GET);
	}

	public static function head() {
		return new GyroHttpRequestConfig(self::HEAD);
	}

	public static function delete() {
		return new GyroHttpRequestConfig(self::DELETE);
	}

	public static function post($data) {
		$ret = new GyroHttpRequestConfig(self::POST);
		$ret->set_body($data);
		return $ret;
	}

	public static function put($data) {
		$ret = new GyroHttpRequestConfig(self::PUT);
		$ret->set_body($data);
		return $ret;
	}

	/**
	 * Create array of curl config options
	 *
	 * @throws Exception
	 */
	public function create_config_array() {
		$ret = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FAILONERROR => 1,
			CURLOPT_REFERER => Config::get_url(Config::URL_SERVER),
			CURLOPT_USERAGENT => Config::get_value(Config::TITLE). ' bot',
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1, // Only use TLS, due to POODLE and others
			CURLOPT_HEADER => $this->headers
		);
		//curl_setopt($curl_handle, CURLOPT_COOKIE, '');
		if (!ini_get('safe_mode')) {
			$ret[CURLOPT_FOLLOWLOCATION] =  1;
		}

		if ($this->no_error_on_4xx_5xx) {
			$ret[CURLOPT_FAILONERROR] = 0;
		}
		if ($this->ssl_no_verify) {
			$ret[CURLOPT_SSL_VERIFYHOST] = 0;
			$ret[CURLOPT_SSL_VERIFYPEER] = 0;
		}
		if ($this->user) {
			$ret[CURLOPT_USERPWD] = $this->user . ':' . $this->password;
		}

		$this->configure_method($ret, $this->method);
		$this->extend_config_options($ret);

		return $ret;
	}

	/**
	 * Allow subclasses to extend options
	 *
	 * @param $options
	 */
	protected function extend_config_options(&$options) {
		// Do nothing by default
	}

	/**
	 * Configure options depending on HTTP method
	 *
	 * @param array $options
	 * @param string $method
	 */
	private function configure_method(&$options, $method) {
		switch ($method) {
			case self::DELETE:
				$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
				break;
			case self::PUT:
				$options[CURLOPT_CUSTOMREQUEST] = "PUT";
				$this->configure_body($options);
				break;
			case self::POST:
				$options[CURLOPT_POST] = true;
				$this->configure_body($options);
				break;
			case self::HEAD:
				//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
				$options[CURLOPT_HEADER] = 1;
				// NOBODY turns GET into HEAD, though it is not documented
				// explicitely
                if (is_null($this->body)) {
                    $options[CURLOPT_NOBODY] = 1;
                } else {
                    $this->configure_body($options);
                }
				break;
			case self::GET:
			default:
				// do nothing, since GET is curl default
		}
	}

	/**
	 * Configures body related options for POST or PUT requests
	 *
	 * @param $options
	 */
	private function configure_body(&$options) {
		switch ($this->body_mime_type) {
			case self::BODY_JSON:
				$options[CURLOPT_POSTFIELDS] = ConverterFactory::encode($this->body, CONVERTER_JSON);
				$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
				break;
			case self::BODY_FORM_URLENCODED:
			default:
				$options[CURLOPT_POSTFIELDS] = http_build_query($this->body);
				$options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/x-www-form-urlencoded';
		}
		// Fixes an issue with NginX. See http://stackoverflow.com/questions/3755786/php-curl-post-request-and-error-417
		$options[CURLOPT_HTTPHEADER][] = 'Expect: ';
	}

	/**
	 * Set body data
	 * @param mixed $data
	 *
	 * @return GyroHttpRequestConfig
	 */
	public function set_body($data) {
		$this->body = $data;
		return $this;
	}

	/**
	 * Set body mime type to one of the BODY_* constants
	 * @param string $type
	 *
	 * @return GyroHttpRequestConfig
	 */

	public function set_body_mime_type($type) {
		$this->body_mime_type = $type;
		return $this;
	}

	/**
	 * Enable or disable forgiving SSL certificate handling
	 * @param bool $yes_no
	 *
	 * @return GyroHttpRequestConfig
	 */
	public function set_ssl_no_verify($yes_no) {
		$this->ssl_no_verify = $yes_no;
		return $this;
	}

	/**
	 * Enable or disable ignoring errors
	 * @param bool $yes_no
	 *
	 * @return GyroHttpRequestConfig
	 */
	public function set_no_error_on_4xx_5xx($yes_no) {
		$this->no_error_on_4xx_5xx = $yes_no;
		return $this;
	}

	/**
	 * Set user and passowrd for authentication
	 *
	 * @param $user
	 * @param $pwd
	 *
	 * @return GyroHttpRequestConfig
	 */
	public function set_auth($user, $pwd) {
		$this->user = $user;
		$this->password = $pwd;

		return $this;
	}

	/**
	 * Set timeput in seeconds
	 *
	 * @param int $seconds
	 * @return GyroHttpRequestConfig
	 */
	public function set_timeout_seconds($seconds) {
		$this->timeout_sec = $seconds;
		return $this;
	}

	/**
	 * Set the HTTP method
	 *
	 * @param $method
	 * @return GyroHttpRequestConfig
	 */
	public function set_method($method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * Sets (and replaces!) the additional HTTP headers
	 *
	 * @param string[] $headers
	 * @return GyroHttpRequestConfig
	 */
	public function set_headers($headers) {
		$this->headers = $headers;
		return $this;
	}

	/**
	 * Add an additional HTTP headers
	 *
	 * @param string $header
	 * @return GyroHttpRequestConfig
	 */
	public function add_header($header) {
		$this->headers[] = $header;
		return $this;
	}

	/**
	 * Add an additional HTTP header, consisting of name and value
	 *
	 * @param string $name Name of header
	 * @param string $value Value of header
	 *
	 * @return GyroHttpRequestConfig
	 */
	public function add_header_by_name($name, $value) {
		$header = $name . ': ' . $value;
		return $this->add_header($header);
	}
}