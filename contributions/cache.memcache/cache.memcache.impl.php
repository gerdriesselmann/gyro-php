<?php
/**
 * Cache Item for Memcache
 * 
 * @author Gerd Riesselmann
 * @ingroup Memcache
 */
class MemcacheCacheItem implements ICacheItem {
	/**
	 * Item data 
	 * 
	 * @var Associative array 
	 */
	private $item_data;
	
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
 * Cache Persistance using Memcache
 * 
 * @author Gerd Riesselmann
 * @ingroup Memcache
 */
class CacheMemcacheImpl implements ICachePersister {
	/**
	 * Returns true, if item is chaced 
	 */
	public function is_cached($cache_keys) {
		$key = $this->flatten_keys($cache_keys);
		return (GyroMemcache::get($key) !== false);
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return ICacheItem The cache as array with members "content" and "data", false if cache is not found
	 */
	public function read($cache_keys) {
		$key = $this->flatten_keys($cache_keys);
		$ret = GyroMemcache::get($key);
		if ($ret) {
			$ret = new MemcacheCacheItem($ret);
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
		GyroMemcache::set($key, $data, $cache_life_time);
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
	private function do_clear_cachable($cachable) {
		$keys = $cachable->get_all_cache_ids();
		foreach($keys as $key) {
			$this->do_clear($key);
		}		
	}
	
	/**
	 * Clear all cache
	 */
	private function do_clear_all() {
		$this->do_clear(array());
	}
	
	/**
	 * Clear cache for given cache key(s)
	 */
	protected function do_clear($cache_keys) {
		// We have do do a clear on 
		// - App Key
		// - Cache Keys
		// - *
		// This means we increment namespace of last key
		// But first, strip of empty keys from the end of the array
		$cleaned = $this->preprocess_keys($cache_keys, false);
		$ns = $this->get_keys_namespaces($cleaned);
		$n = array_pop($ns);
		if ($n) {
			// See http://code.google.com/p/memcached/wiki/FAQ#Deleting%5Fby%5FNamespace
			// for how this trick works
			GyroMemcache::increment($n, 1);
		}
	}
		
	/**
	 * Transform the given param into a key string
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 */
	protected function flatten_keys($cache_keys) {
		$cache_keys = $this->preprocess_keys($cache_keys);
		$ns_keys = $this->get_keys_namespaces($cache_keys);
		
		$tmp = array();
		foreach($cache_keys as $key) {
			$tmp[] = $key . ':=' . $this->get_namespace_value(array_shift($ns_keys));
		}
		
		return implode('_', $tmp);		
	}
		
	/**
	 * Return array of namespaces for keys
	 * 
	 * See http://code.google.com/p/memcached/wiki/FAQ#Deleting%5Fby%5FNamespace
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return array
	 */
	protected function get_keys_namespaces($cache_keys) {
		$ret = array();
		foreach(Arr::force($cache_keys, true) as $key) {
			$ns_key .= 'g$ns' . $key;
			$ret[] = $ns_key;
		}
		return $ret;
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
	 * Return key to make the current app unique
	 * 
	 * @return string
	 */
	private function get_app_key() {
		return Config::get_url(Config::URL_DOMAIN);
	}

	/**
	 * Get value of namespace counter
	 * 
	 * @param string $ns Namespace 
	 */
	private function get_namespace_value($ns) {
		$ret = GyroMemcache::get($ns);
		if ($ret === false) {
			// This should be a transacton
			$ret = rand(1, 1000);
			if (!GyroMemcache::add($ns, $ret, 0)) {
				$ret = GyroMemcache::get($ns);		
			}
		}
		return $ret;
	}

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired() {
		// Nothing to do, memcache does this for us
	}
	
}