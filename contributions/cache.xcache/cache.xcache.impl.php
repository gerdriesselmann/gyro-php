<?php
/**
 * Cache Item for XCache
 * 
 * @author Gerd Riesselmann
 * @ingroup XCache
 */
class XCacheCacheItem implements ICacheItem {
	/**
	 * Item data 
	 * 
	 * @var Associative array 
	 */
	protected $item_data;
	
	/**
	 * Constructor
	 * 
	 * @param array $item_data
	 */
	public function __construct($item_data) {
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
		return $this->item_data['content'];
	}	
}

/**
 * Cache Persistance using XCache
 * 
 * @author Gerd Riesselmann
 * @ingroup XCache
 */
class CacheXCacheImpl implements ICachePersister {
	/**
	 * Returns true, if item is chaced 
	 */
	public function is_cached($cache_keys) {
		$key = $this->flatten_keys($cache_keys);
		return xcache_isset($key);
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
		if (xcache_isset($key)) {
			$ret = new XCacheCacheItem(xcache_get($key));
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
		$data = array(
			'content' => $content,
			'data' => $data,
			'creationdate' => time(),
			'expirationdate' => time() + $cache_life_time			
		);
		$key = $this->flatten_keys($cache_keys);
		xcache_set($key, $data, $cache_life_time);
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
		else if ($cache_keys instanceof ICachable) {
			$this->do_clear_cachable($cache_keys);
			foreach($cache_keys->get_dependend_cachables() as $dependance) {
				$this->do_clear_cachable($dependance);
			}
		}
		else {
			$this->do_clear($cache_keys);
		}
	}
	
	/**
	 * Clear chache for given ICachable 
	 */
	protected function do_clear_cachable($cachable) {
		$keys = $cachable->get_all_cache_ids();
		foreach($keys as $key) {
			$this->do_clear($key);
		}		
	}
	
	/**
	 * Clear all cache
	 */
	protected function do_clear_all() {
		xcache_unset_by_prefix($this->get_app_key());
	}
	
	/**
	 * Clear cache for given cache key(s)
	 */
	protected function do_clear($cache_keys) {
		$key = $this->flatten_keys($cache_keys);
		xcache_unset($key);
		xcache_unset_by_prefix($key . '_g$c');
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
		if ($strip_empty) {
			foreach(Arr::force($cache_keys, false) as $key) {
				if ($key || $key == '0') {
					$cleaned[] = $key;
				} 
				else {
					break;
				}
			}		
		}
		else {
			$cleaned = array_merge($cleaned, Arr::force($cache_keys, true));
		}
		return $cleaned;
	}
		
	/**
	 * Transform the given param into a key string
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 */
	protected function flatten_keys($cache_keys) {
		$cache_keys = $this->preprocess_keys($cache_keys);
		$ret .= implode('_g$c', $cache_keys);
		return $ret;		
	}

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired() {
		// Nothing to do, xcache does this for us
	}
	
}