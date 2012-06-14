<?php
/**
 * Wraps Cookie Handling
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */ 
class Cookie {
	/**
	 * Create Cookie
	 * 
	 * @param string Name of Cookie
	 * @param string Cotent of Cookuie
	 * @param int Number of seconds the cookie will be valid
	 */
	public static function create($name, $content, $valid_seconds = null, $path = '/', $http_only = true, $domain = false, $ssl = false) {
		$expire = empty($valid_seconds) ? null : time() + Cast::int($valid_seconds); 
		setcookie($name, $content, $expire, $path, $domain, $ssl, $http_only);
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
