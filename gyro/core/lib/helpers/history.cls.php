<?php
define ('HISTORY_NUMBER_OF_ITEMS', 2);

/**
 * Keeps track of pages called
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class History {
	/**
	 * Keep history in memory for manual modifications
	 *
	 * @var array|null
	 */
	private static $current = null;

	public static function clear() {
		self::store(array());
	}
	
	/**
	 * Put page into history
	 */
	public static function push($url) {
		$arr = self::get_current();
		array_unshift($arr, $url);
		if (count($arr) > HISTORY_NUMBER_OF_ITEMS) {
			array_pop($arr);
		}
		self::store($arr);
	}
	
	/**
	 * Remove url from history
	 *
	 * @param string|Url $url
	 */
	public static function remove($url) {
		if (empty($url)) {
			return;
		}
		$arr = self::get_current();
		$new = array();
		if (count($arr) > 0) {
			if (!$url instanceof Url) {
				$url = Url::create($url);
			}
			$compare = $url->build();
			while($cur = array_shift($arr)) {
				if (!$cur instanceof Url) {
					$cur = Url::create($cur);
				}
				if ($cur->build() !== $compare) {
					$new[] = $cur;
				}
			}
			self::store($new);
		}
	}
	
	/**
	 * Retrieve page from history
	 * 
	 * @param Integer Index of page to retrieve, 0-based. Index counts back in time, so 0 is last, 1 is page before last etc
	 * @param Mixed Either Url or String: Default page to return if history is empty
	 * @return Url  
	 */
	public static function get($index, $defaultpage = false) {
		$val = ($defaultpage === false) ? Config::get_url(Config::URL_DEFAULT_PAGE) : $defaultpage;
		$arr = self::get_current();
		$index = Cast::int($index);
		if ( ($index >= 0) && (count($arr) > $index) ) {
			$val = $arr[$index];
		}
		return self::make_url($val);
	}
	
	/**
	 * Read last page from history and redirect to it
	 * 
	 * If history is empty, $defaultpage is invoked (or current page, if empty)
	 * 
	 * @param Integer Index of page to go to, 0-based. Index counts back in time, so 0 is last, 1 is page before last etc
	 * @param Status|String Optional message to display on page redirected to
	 * @param Mixed Either Url or String: Default page to go to if history is empty 
	 */ 
	public static function go_to($index, $message = '', $defaultpage = false) {
		$url = self::get($index, $defaultpage);
		if ($message instanceof Status) {
			$message->persist();
		} else if (!empty($message)) {
			$msg = new Message($message);
			$msg->persist();
		}
		$url->redirect();
		exit;
	}
	
	/**
	 * Convert param in valid Url instance
	 */
	private static function make_url($val) {
		if ($val instanceof Url) {
			return $val;
		} else if (is_string($val) && !empty($val)) {
			return Url::create($val);
		} else {
			return Url::current();
		}
	}

	/**
	 * Read current from session, if not yet known
	 */
	private static function get_current() {
		if (is_null(self::$current)) {
			$arr = array();
			if (Session::is_started()) {
				$arr = Session::peek('history');
				if (!is_array($arr)) {
					$arr = array();
				}
			}

			self::$current = $arr;
		}

		return self::$current;
	}

	/**
	 * Store history in session, if session exists
	 *
	 * @param array $arr
	 */
	private static function store($arr) {
		self::$current = $arr;
		if (Session::is_started()) {
			Session::push('history', $arr);
		}
	}
}
?>