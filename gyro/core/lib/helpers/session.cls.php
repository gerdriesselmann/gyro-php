<?php
/**
 * Covers session handling
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Session {
	/**
	 * Starts a session
	 */
	public static function start() {
		if (!session_id()) {
			//session_cache_limiter('private_no_expire');
			if (!headers_sent()) {
				session_start();
			}
		}
	}
	
	/**
	 * Starts a session, if session coookie is present;
	 */
	public static function start_existing() {
		if (!session_id()) {
			$name  = session_name();
			if (Cookie::exists($name)) {
				session_start();
			}
		}
	}
	
	public static function clear() {
		$_SESSION = array();
		self::restart();		
	}

	/**
	 * Regenerates session id
	 */
	public static function restart() {
		if (!headers_sent()) {
			session_regenerate_id();
		}
	}
	
	/** 
	 * Returns true, if session has been started
	 */	
	public static function is_started() {
		return (session_id() !== '');
	}
	
	/**
	 * Returns session id
	 */
	public static function get_session_id() {
		return session_id();
	}

	/**
	 * Returns TRUE, if cookies are enabled, FALSE otherwise
	 *
	 * @return Boolean
	 */
	public static function cookies_enabled() {
		Session::start();
		if ( isset($_SESSION["cookiesenabled"]) ) {
			return true;
		}

		// Now check for cookies
		// Taken from http://www.articlecity.com/articles/web_design_and_development/article_260.shtml
		if ( isset($_GET["cookietest"]) ) {
			// Check if cookie was successfully set
			if ( empty($_COOKIE["cookietest"]) ) {
				return false;
			}
			else {
				$_SESSION["cookiesenabled"] = true;
				// Delete Cookie
				setcookie("cookietest", "", time() - 3600);
				Url::current().replace_query_paramter('cookietest', '').redirect();
			}
		}
		else {
			setcookie("cookietest", "Just a test to see if cookies are enabled", 0); //time() + 60);
			Url::current().replace_query_paramter('cookietest', '1').redirect();
		}
	}

	/**
	 * Persists given value under given name
	 *
	 * @param String the name for the value
	 * @param mixed The value
	 */
	public static function push($name, $value) {
		Session::start();
		$_SESSION[$name] = $value;
	}

	/**
	 * Persists given value under given name, if slot for name is empty
	 */
	public static function push_if_empty($name, $value) {
		Session::start();
		if (isset($_SESSION[$name]))
			return;

		$_SESSION[$name] = $value;
	}


	/**
	 * Returns persistet value
	 *
	 * @param Name of the value
	 * @return mixed
	 */
	public static function peek($name) {
		Session::start_existing();
		if (isset($_SESSION[$name])) {
			return $_SESSION[$name];
		} else {
			return false;
		}
	}

	/**
	 * Returns persistet value and unregisters it
	 *
	 * @param Name of the value
	 * @return mixed
	 */
	static public function pull($name) {
		Session::start_existing();
		if (isset($_SESSION[$name])) {
			$temp = $_SESSION[$name];
			unset($_SESSION[$name]);
			return $temp;
		}
		else {
			return false;
		}
	}

	/**
	 * Static. Sets the from source
	 *
	 * @param String The from URL
	 * @return void
	 */
	public static function set_from($url) {
		if ( isset($url) ) {
			Session::push('from', $url);
		}
		else 	{
			Session::push('from', new Url(Config::get_url(Config::URL_DEFAULT_PAGE)));
		}
	}

	/**
	 * Static Returns the from source
	 *
	 * @return String An URL
	 */
	public static function get_from($default = false) {
		$ret = Session::peek('from');
		if (empty($ret)) {
			$ret = ($default === false) ? Config::get_url(Config::URL_DEFAULT_PAGE) : $default;
		}
		return $ret;
	}


	public static function set_status($status) {
		Session::push("status", $status);
	}


	public static function get_status() {
		return Session::pull("status");
	}
	
	public function has_status() {
		return (Session::peek('status') != false);
	}
}