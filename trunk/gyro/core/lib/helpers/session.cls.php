<?php
// Force cookie usage
ini_set('session.use_cookies', 1);	 
ini_set('session.use_only_cookies', 1);
ini_set('session.bug_compat_42', 1);
ini_set('session.use_trans_sid', 0);

/**
 * Covers session handling
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Session {
	const STARTED_BY_GYRO = '_GYSS__';
	const FINGERPRINT = '_GYRO_FINGERPRINT_';
	
	private static $handler;
	
	/**
	 * Set session handler instance
	 * 
	 * @param ISessionHandler $handler
	 */
	public static function set_handler($handler) {
		self::$handler = $handler;
	}

	/**
	 * Initialize the session handler by calling session_set_save_handler()
	 * 
	 * @return void
	 */
	private static function init_handler() {
		if (self::$handler) {
			session_set_save_handler(
				array(self::$handler, 'open'), 
				array(self::$handler, 'close'), 
				array(self::$handler, 'read'), 
				array(self::$handler, 'write'), 
				array(self::$handler, 'destroy'), 
				array(self::$handler, 'gc')
			);					
		}
	}
	
	/**
	 * Starts a session
	 */
	public static function start($id = false) {
		if (!self::is_started()) {
			if (!headers_sent()) {
				self::do_start($id);
			}
		}
	}
	
	/**
	 * Starts a session, if session coookie is present;
	 */
	public static function start_existing() {
		if (!self::is_started()) {
			$name = session_name();
			if (Cookie::exists($name)) {
				self::do_start();
			}
		}
	}
	
	/**
	 * Starts a session, but keeps headers untouched
	 * 
	 * PHP sends a couple of headers when starting a session. Since
	 * Gyro handles headers on its own, they come in the way.
	 * 
	 * @return void
	 */
	private static function do_start($id = false) {
		$headers = Common::get_headers();
		// This prevents headers sent on my system (PHP/5.2.4-2ubuntu5.10 with Suhosin-Patch)
		// But I have no clue, how this behaves on other platforms or other PHP versions.
		// At least it is not documented... 
		if (!self::is_started()) {		
			session_cache_limiter('');
		} 
		self::do_start_and_verify($id);
		Common::header_restore($headers);
		// Cookie header may have gone lost.... So send it manually
		$cookie_params = session_get_cookie_params();
		if (!isset($cookie_params['httponly'])) {
			$cookie_params['httponly'] = false;
		} 
		$lifetime = $cookie_params['lifetime'];
		$expire = empty($lifetime) ? null : time() + $lifetime;
		setcookie(
			session_name(), session_id(), $expire, 
			$cookie_params['path'], $cookie_params['domain'], 
			$cookie_params['secure'], $cookie_params['httponly']
		);
	}
	
	/**
	 * This finally starts a session...
	 */
	private static function do_start_and_verify($id = false) {
		if ($id) {
			session_id($id);
		}
		self::init_handler();
		session_start();
		if (!isset($_SESSION[self::STARTED_BY_GYRO])) {
			// This session is new
			if (empty($id)) {
				// It is new and only maybe created on demand of the application
				// Change ID, so we can be sure app has control over session id 
				session_regenerate_id(true);
			}
			$_SESSION[self::STARTED_BY_GYRO] = true;
		}	
	} 
	
	public static function clear() {
		$_SESSION = array();
		self::restart();		
	}
	
	public static function end() {
		session_destroy();
	}

	/**
	 * Regenerates session id
	 */
	public static function restart($id = false) {
		if (!headers_sent()) {
			if ($id) {
				$backup = array();
				if (self::is_started()) {
					$backup = $_SESSION;
					self::end();
				}
				self::do_start($id);
				$_SESSION = $backup;
			}
			else {
				session_regenerate_id(true);
			}
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
		self::start();
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
		self::start();
		$_SESSION[$name] = $value;
	}

	/**
	 * Persists given value under given name, if slot for name is empty
	 */
	public static function push_if_empty($name, $value) {
		self::start();
		if (!isset($_SESSION[$name])) {
			$_SESSION[$name] = $value;
		}
	}
	
	/**
	 * Adds $value to array $name
	 */
	public static function push_to_array($name, $value) {
		self::start();
		$arr = self::peek($name);
		if (!is_array($arr)) {
			$arr = array();
		}	
		$arr[] = $value;
		$_SESSION[$name] = $arr;
	}

	/**
	 * Adds $value to array $name witj key $key
	 */
	public static function push_to_array_assoc($name, $value, $key) {
		self::start();
		$arr = self::peek($name);
		if (!is_array($arr)) {
			$arr = array();
		}	
		$arr[$key] = $value;
		$_SESSION[$name] = $arr;
	}
	
	/**
	 * Returns persistet value
	 *
	 * @param Name of the value
	 * @return mixed
	 */
	public static function peek($name) {
		self::start_existing();
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
		self::start_existing();
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
			self::push('from', $url);
		}
		else 	{
			self::push('from', new Url(Config::get_url(Config::URL_DEFAULT_PAGE)));
		}
	}

	/**
	 * Static Returns the from source
	 *
	 * @return String An URL
	 */
	public static function get_from($default = false) {
		$ret = self::peek('from');
		if (empty($ret)) {
			$ret = ($default === false) ? Config::get_url(Config::URL_DEFAULT_PAGE) : $default;
		}
		return $ret;
	}


	public static function set_status($status) {
		self::push("status", $status);
	}


	public static function get_status() {
		return self::pull("status");
	}
	
	public function has_status() {
		return (self::peek('status') != false);
	}
}