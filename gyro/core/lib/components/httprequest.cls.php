<?php
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
class HttpRequest {
	const NONE = 0;
	const SSL_NO_VERIFY = 1;
	const NO_ERROR_ON_4XX_5XX = 2;

	/**
	 * Read content from given url
	 *
	 * @param string|Url $url URL to invoke
	 * @param Status $err Set to errors if any
	 * @param int $timeout Timeout in seconds
	 * @param int $policy Policy. Either NONE or SSL_NO_VERIFY
	 * @param array $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 */
	public static function get_content ($url, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$options = self::get_default_opts($policy);
		$ret = self::execute_curl($url, $options, $timeout, $err, $info);
		return $ret;
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
	 * @param array $info Set to CURL info array. See http://www.php.net/manual/de/function.curl-getinfo.php
	 * @return String The content of the file or NULL, if file was not found
	 */
	public static function get_content_with_auth($url, $user, $pwd, $err = null, $timeout = 30, $policy = self::NONE, &$info = false) {
		$options = self::get_default_opts($policy);
		$options[CURLOPT_USERPWD] = "$user:$pwd";
		$ret = self::execute_curl($url, $options, $timeout, $err, $info);
		return $ret;
	}
	
	/**
	 * Fetch only head
	 *
	 * @param Url|string $url
	 * @param Status $err
	 * @param int $timeout
	 * @return string Content fetched or false on error
	 */
	public static function get_head($url, $err = null, $timeout = 30, $policy = self::NONE) {
		$options = self::get_default_opts($policy);
		//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
		$options[CURLOPT_HEADER] = 1;
		// NOBODY turns GET into HEAD, though it is not documented
		// explicitely
		$options[CURLOPT_NOBODY] = 1;
		$ret = self::execute_curl($url, $options, $timeout, $err);
		return $ret;		
	}
	
	/**
	 * Starts a header-request and returns true if site exists otherwise returns false.
	 *
	 * @return boolean
	 */
	public static function site_exists($url) {
		$ret = self::get_head($url);
		return !empty($ret);
	}

	/**
	 * Fet standard curl options for the given curl handle.
	 *
	 * @param int $timeout
	 * @return array
	 */
	private static function get_default_opts($policy) {
		$ret = array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FAILONERROR => 1,
			CURLOPT_REFERER => Config::get_url(Config::URL_SERVER),
			CURLOPT_USERAGENT => Config::get_value(Config::TITLE). ' bot',
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_FRESH_CONNECT => 1
		);
		//curl_setopt($curl_handle, CURLOPT_COOKIE, '');
		if (!ini_get('safe_mode')) {
			$ret[CURLOPT_FOLLOWLOCATION] =  1;
		}
		
		if (Common::flag_is_set($policy, self::NO_ERROR_ON_4XX_5XX)) {
			$ret[CURLOPT_FAILONERROR] = 0;
		}
		if (Common::flag_is_set($policy, self::SSL_NO_VERIFY)) {
			$ret[CURLOPT_SSL_VERIFYHOST] = 0;
			$ret[CURLOPT_SSL_VERIFYPEER] = 0;
		}
		
		return $ret;
	}
	
	/**
	 * Execute a curl request 
	 *
	 * @param Url|string $url
	 * @param array $options
	 * @param int $timeout
	 * @param Status $status
	 * @return string Content fetched or false on error
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
}
