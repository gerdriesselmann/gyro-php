<?php
/**
 * Cache Item for ACPu
 * 
 * @author Gerd Riesselmann
 * @ingroup ACPu
 */
class ACPuCacheItem implements ICacheItem {
	/**
	 * Item data 
	 * 
	 * @var array Associative array
	 */
	protected $item_data;
	
	/**
	 * Constructor
	 * 
	 * @param array $item_data
	 */
	public function __construct($item_data) {
		if (is_string($item_data)) {
			$item_data = unserialize($item_data);
		}
		$this->item_data = $item_data;
	}
	
	/**
	 * Return creation date 
	 * 
	 * @return datetime
	 */
	public function get_creationdate() {
		return $this->item_data['creationdate'];
	}	
	
	/**
	 * Return expiration date 
	 * 
	 * @return datetime
	 */
	public function get_expirationdate() {
		return $this->item_data['expirationdate'];
	}
	
	/**
	 * Return data associated with this item
	 * 
	 * @return mixed
	 */
	public function get_data() {
		return $this->item_data['data'];
	}
	
	/**
	 * Return the content in plain form
	 * 
	 * @return string
	 */
	public function get_content_plain() {
		$ret = $this->get_content_compressed();
		if ($ret && function_exists('gzinflate')) {
			$ret = gzinflate($ret);
		}
		return $ret;
	}
	
	/**
	 * Return the content gzip compressed
	 * 
	 * @return string
	 */
	public function get_content_compressed() {
		$content = $this->item_data['content'];
		//$content = base64_decode($content);
		return $content;
	}	
}

/**
 * Cache Persistance using ACPu
 * 
 * @author Gerd Riesselmann
 * @ingroup ACPu
 */
class CacheACPuImpl implements ICachePersister {
	/**
	 * Returns true, if item is chaced 
	 */
	public function is_cached($cache_keys) {
		$key = $this->flatten_keys($cache_keys);
		return apcu_exists($key);
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return ICacheItem The cache as array with members "content" and "data", false if cache is not found
	 */
	public function read($cache_keys) {
		$ret = false;
		$key = $this->flatten_keys($cache_keys);
		if (apcu_exists($key)) {
			$ret = new ACPuCacheItem(apcu_fetch($key));
		}
		return $ret;
	}
	
	/**
	 * Store content in cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @param string The cache
	 */
	public function store($cache_keys, $content, $cache_life_time, $data = '', $is_compressed = false) {
		if (!$is_compressed) {
			if (function_exists('gzdeflate')) {
				$content = gzdeflate($content, 9);
			}
		}
		//$content = base64_encode($content);
		$data = array(
			'content' => $content,
			'data' => $data,
			'creationdate' => time(),
			'expirationdate' => time() + $cache_life_time			
		);

		$key = $this->flatten_keys($cache_keys);
		$serialized = serialize($data);
		apcu_store($key, $serialized, $cache_life_time);
	}
	
	/**
	 * Clear the cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string, or an ICachable instance. If NULL, all is cleared
	 */
	public function clear($cache_keys = NULL) {
		if (empty($cache_keys)) {
			$this->do_clear_all();
		}
		else {
			$this->do_clear($cache_keys);
		}
	}
	
	/**
	 * Clear all cache
	 */
	protected function do_clear_all() {
		$app_key = $this->get_app_key();
		$this->clear_by_prefix($app_key);
	}
	
	/**
	 * Clear cache for given cache key(s)
	 */
	protected function do_clear($cache_keys) {
		$key = $this->flatten_keys($cache_keys, false);
		apcu_delete($key);
		$this->clear_by_prefix($key . '_g$c');
	}

	private function clear_by_prefix($prefix) {
		$regex_escaped = preg_quote($prefix, '/');
		$it = new APCUIterator("/^$regex_escaped.*/");
		apcu_delete($it);
	}

	/**
	 * Return key to make the current app unique
	 * 
	 * @return string
	 */
	protected function get_app_key() {
		return 'g$c' . Config::get_url(Config::URL_DOMAIN) . '';
	}

	/**
	 * Strip empty keys from end of $cache_keys 
	 */
	protected function preprocess_keys($cache_keys, $strip_empty = true) {
		$cleaned = array($this->get_app_key());
		foreach(Arr::force($cache_keys, false) as $key) {
			if ($key || $key == '0') {
				$cleaned[] = $key;
			} else if ($strip_empty) {
				break;
			} else {
				$cleaned[] = "{empty}";
			}
		}
		return $cleaned;
	}

	/**
	 * Transform the given param into a key string
	 *
	 * @param $cache_keys
	 * @param bool $strip_empty
	 * @return string
	 */
	protected function flatten_keys($cache_keys, $strip_empty = true) {
		$cache_keys = $this->preprocess_keys($cache_keys, $strip_empty);
		$ret = implode('_g$c', $cache_keys);
		return $ret;		
	}

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired() {
		// Nothing to do, ACPu does this for us
	}
	
}