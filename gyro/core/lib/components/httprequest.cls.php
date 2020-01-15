<?php
require_once __DIR__ . '/httprequestconfig.cls.php';

/**
 * Download files using http protocols. 
 * 
 * @note The class uses CURL, so the php bindings for PHP should be installed. 
 * See http://www.php.net/manual/en/curl.installation.php
 *  
 * @author Gerd Riesselmann
 * @author Natalia Wehler
 * @ingroup Lib
 */
class GyroHttpRequest {
	const NONE = 0;
	const SSL_NO_VERIFY = 1;
	const NO_ERROR_ON_4XX_5XX = 2;
	const SEND_JSON = 4;

	/**
	 * Execute request with given configuration
	 *
	 * @param string|Url $url URL to invoke
	 * @param GyroHttpRequestConfig $config
	 * @param Status|null $err Set to errors if any
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return string The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function request($url, GyroHttpRequestConfig $config, $err = null, &$info = false) {
		$option = $config->create_config_array();
		$ret = self::execute_curl($url, $option, $config->timeout_sec, $err, $info);
		return $ret;
	}

	/**
	 * Read content from given url
	 *
	 * @param string|Url $url URL to invoke
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function get_content($url, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::get()->set_timeout_seconds($timeout);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * Read content from given url using authentication
	 *
	 * @param string|Url $url URL to invoke
	 * @param string $user Username for authentication
	 * @param string $pwd Password for authentication
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function get_content_with_auth($url, $user, $pwd, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::get()->set_timeout_seconds($timeout);
		$config->set_auth($user, $pwd);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * POST to given URL
	 *
	 * @param string|Url $url URL to invoke
	 * @param array $fields Associative array of values
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function post_content($url, $fields, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::post($fields)->set_timeout_seconds($timeout);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * POST content to given url using authentication
	 *
	 * @param string|Url $url URL to invoke
	 * @param array $fields Associative array of values
	 * @param string $user Username for authentication
	 * @param string $pwd Password for authentication
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function post_content_with_auth($url, $fields, $user, $pwd, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::post($fields)->set_timeout_seconds($timeout);
		$config->set_auth($user, $pwd);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * PUT to given URL
	 *
	 * @param string|Url $url URL to invoke
	 * @param array $fields Associative array of values
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function put_content($url, $fields, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::put($fields)->set_timeout_seconds($timeout);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * PUT content to given url using authentication
	 *
	 * @param string|Url $url URL to invoke
	 * @param array $fields Associative array of values
	 * @param string $user Username for authentication
	 * @param string $pwd Password for authentication
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function put_content_with_auth($url, $fields, $user, $pwd, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::put($fields)->set_timeout_seconds($timeout);
		$config->set_auth($user, $pwd);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * DELETE from given url
	 *
	 * @param string|Url $url URL to invoke
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function delete_content($url, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::delete()->set_timeout_seconds($timeout);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * DELETE from given url using authentication
	 *
	 * @param string|Url $url URL to invoke
	 * @param string $user Username for authentication
	 * @param string $pwd Password for authentication
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array|bool $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 * @throws Exception
	 */
	public static function delete_content_with_auth($url, $user, $pwd, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$config = GyroHttpRequestConfig::delete()->set_timeout_seconds($timeout);
		$config->set_auth($user, $pwd);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * Fetch only head
	 *
	 * @param Url|string $url
	 * @param Status $err
	 * @param int $timeout
	 * @return string Content fetched or false on error
	 * @throws Exception
	 */
	public static function get_head($url, $err = null, $timeout = 30, $policy = self::NONE) {
		$config = GyroHttpRequestConfig::head()->set_timeout_seconds($timeout);
		self::configure_policy($config, $policy);
		return self::request($url, $config, $err, $info);
	}

	/**
	 * Starts a header-request and returns true if site exists otherwise returns false.
	 *
	 * @param string|Url $url
	 * @return boolean
	 * @throws Exception
	 */
	public static function site_exists($url) {
		$ret = self::get_head($url);
		return !empty($ret);
	}

	/**
	 * Execute a curl request
	 *
	 * @param Url|string $url
	 * @param array $options
	 * @param int $timeout
	 * @param $err
	 * @param bool $info
	 * @return string Content fetched or false on error
	 * 
	 * @throws Exception
	 */
	private static function execute_curl($url, $options, $timeout, $err, &$info = false) {
		$address = $url;
		if ($url instanceof Url) {
			$address = $url->build();
		}
		$options[CURLOPT_URL] = $address;
		if ($timeout > 0) {
			$options[CURLOPT_TIMEOUT] =  $timeout;
		}
		
		$ret = false;
		$ch = NULL;
		
		$status = ($err) ? $err : new Status();
		$err_no = 0;
		try {
			$ch = curl_init();
			if (curl_setopt_array($ch, $options)) {
				$ret = curl_exec ($ch);
				$info = curl_getinfo($ch);
				if ($ret === false) {
					$status->append($address . ': ' . curl_error($ch));
					$err_no = curl_errno($ch);
				}				
			}
		}
		catch (Exception $e) {
			// Do nothing special here
			if ($ch) {
				$status->append($address . ': ' . curl_error($ch));
				$err_no = curl_errno($ch);
				$info = curl_getinfo($ch);
			}
			else {
				$status->append($address . ': ' . $e->getMessage());
				$err_no = 999;
			}
			$ret = false;
		}
		if ($ch) {
			@curl_close ($ch);
		}
		if (Config::has_feature(Config::LOG_HTTPREQUESTS)) {
			$log = array($address, $err_no, $status->to_string(Status::OUTPUT_PLAIN));
			Load::components('logger');
			Logger::log('httprequests', $log);
		}
		return $ret;
	}
	
	private static function configure_policy(GyroHttpRequestConfig $config, $policy) {
		$config->set_no_error_on_4xx_5xx(
			Common::flag_is_set($policy, self::NO_ERROR_ON_4XX_5XX)
		);
		$config->set_ssl_no_verify(
			Common::flag_is_set($policy, self::SSL_NO_VERIFY)
		);
		if (Common::flag_is_set($policy, self::SEND_JSON)) {
			$config->set_body_mime_type(GyroHttpRequestConfig::BODY_JSON);
		}		
	}
}

if (!class_exists('HttpRequest')) {
	class HttpRequest extends GyroHttpRequest {}
}