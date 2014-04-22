<?php
require_once dirname(__FILE__) . '/dataobjectbase.cls.php';

/**
 * A Dataobject sub class with build in cache
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DataObjectCached extends DataObjectBase {
	public function __clone() {
		$this->clear_cache();
		parent::__clone();
	}

	/**
	 * Reads a value from cache. If not already set, cache is populated with result of 
	 * function $this->$callback 
	 *
	 * @param string $key
	 * @param string $callback
	 */
	protected function get_from_cache($key, $callback, $params = false) {
		$ret = $this->get_cache_item($key);
		if (is_null($ret)) {
			$ret = $this->$callback($params);
			$this->set_cache_item($key, $ret);
		}
		return $ret;
	}
	
	/**
	 * Remove all items from cache
	 */
	protected function clear_cache() {
		RuntimeCache::remove($this->compute_cache_item_key(''));
	}
	
	/**
	 * Clear given cache item
	 *
	 * @param string $key
	 */
	protected function clear_cache_item($key) {
		RuntimeCache::remove($this->compute_cache_item_key($key));
	}
	
	/**
	 * Returns item form cache or NULL if not set
	 *
	 * @param string $key
	 * @return mixed
	 */
	protected function get_cache_item($key) {
		return RuntimeCache::get($this->compute_cache_item_key($key), null);
	}

	/**
	 * Sets cache item
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	protected function set_cache_item($key, $value) {
		RuntimeCache::set($this->compute_cache_item_key($key), $value);
	}
	
	/**
	 * Copute global cache key as recurive array string
	 *
	 * @param string $key
	 * @return string
	 */
	protected function compute_cache_item_key($key) {
		$ret = $this->to_string();
		if ($ret) {
			if ($key) {
				$pos = strpos($key, '[');
				if ($pos === 0) {
					$ret .= $key;
				}
				else if ($pos === false) {
					$ret .= '[' . $key . ']';
				}
				else {
					$ret .= '[' . substr($key, 0, $pos) . ']' . substr($key, $pos);
				}
			}
		}
		else {
			$ret = $key;
		}
		return $ret;
	}
	
	/**
     * fetches next row into this objects var's
     *
     * returns true on success false on failure
     *
     * Example
     * $object = new mytable();
     * $object->name = "fred";
     * $object->find();
     * $store = array();
     * while ($object->fetch()) {
     *   echo $this->ID;
     *   $store[] = $object; // builds an array of object lines.
     * }
	 *
     * @return boolean True on success
     */
    public function fetch() {
 		$this->clear_cache();
 		return parent::fetch();   	
    }	
}
