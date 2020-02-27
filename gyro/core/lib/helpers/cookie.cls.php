<?php

class GyroCookieConfig {
	const SAME_SITE_NONE = 'None';
	const SAME_SITE_LAX = 'Lax';
	const SAME_SITE_STRICT = 'Strict';

	public $valid_seconds = null;
	public $path = '/';
	public $http_only = true;
	public $domain = '';
	public $ssl_only = false;
	public $same_site = null;

	public function to_array() {
		$ret = array();
		$ret['expires'] = $this->expires();
		$ret['path'] = $this->path;
		$ret['domain'] = $this->domain;
		$ret['secure'] = $this->ssl_only;
		$ret['httponly'] = $this->http_only;
		if ($this->same_site) {
			$ret['samesite'] = $this->same_site;
		}

		return $ret;
	}

	public function expires() {
		return empty($this->valid_seconds) ? null : time() + Cast::int($this->valid_seconds);
	}

	public static function create($valid_seconds) {
		$ret = new GyroCookieConfig();
		$ret->valid_seconds = $valid_seconds;
		return $ret;
	}
}

/**
 * Wraps Cookie Handling
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */ 
class GyroCookie {
	/**
	 * Create Cookie
	 * 
	 * @param string Name of Cookie
	 * @param string Cotent of Cookuie
	 * @param int Number of seconds the cookie will be valid
	 */
	public static function create(
		$name, $content, $valid_seconds = null, $path = '/',
		$http_only = true, $domain = false, $ssl = false
	) {
		$config = GyroCookieConfig::create($valid_seconds);
		$config->path = $path;
		$config->http_only = $http_only;
		$config->domain = $domain;
		$config->ssl_only = $ssl;

		self::create_with_config($name, $content, $config);
	}

	/**
	 * Create Cookie
	 *
	 * @param string Name of Cookie
	 * @param string Cotent of Cookuie
	 * @param GyroCookieConfig $config
	 */
	public static function create_with_config(
		$name, $content, GyroCookieConfig $config
	) {
		if (version_compare(PHP_VERSION, '7.3.0') >= 0) {
			// Supports options signature
			$options = $config->to_array();
			setcookie($name, $content, $options);
		} else {
			// Old signature without samesite
			setcookie(
				$name, $content,
				$config->expires(), $config->path, $config->domain,
				$config->ssl_only, $config->http_only
			);
		}
	}

	/**
	 * Delete cookie
	 * 
	 * @param string Name of cookie to delete
	 */
	public static function delete($name) {
		setcookie($name, '', 1, '/');
	}
	
	/**
	 * Returns true if cookie exists, false otherwise
	 * 
	 * @param string Name of cookie
	 */
	public static function exists($name) {
		return isset($_COOKIE[$name]);
	}
	
	/**
	 * Returns value form given cookie, if exists, false otherwise
	 */
	public static function get_cookie_value($name) {
		return Arr::get_item($_COOKIE, $name, false);
	}
}

if (!class_exists('Cookie')) {
	class Cookie extends GyroCookie {}
}