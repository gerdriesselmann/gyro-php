<?php
require_once dirname(__FILE__) . '/cache.xcache.impl.php';

/**
 * Cache Persistance using XCache 1.2
 * 
 * @author Gerd Riesselmann
 * @ingroup XCache
 */
class CacheXCache12Impl extends CacheXCacheImpl {
	/**
	 * Clear all cache
	 */
	protected function do_clear_all() {
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
			xcache_inc($n, 1);
		}
	}
	
	/**
	 * Transform the given param into a key string
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 */
	protected function flatten_keys($cache_keys, $strip_empty = true) {
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
	 * Get value of namespace counter
	 * 
	 * @param string $ns Namespace 
	 */
	protected function get_namespace_value($ns) {
		if (xcache_isset($ns)) {
			$ret = xcache_get($ns);
		}
		else {
			// This should be a transacton
			$ret = rand(1, 1000);
			xcache_set($ns, $ret);		
		}
		return $ret;
	}	
}