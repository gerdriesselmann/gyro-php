<?php
/**
 * An implementation of cache as files
 *  
 * @author Gerd Riesselmann
 * @ingroup FileCache
 */
class CacheFileImpl implements ICachePersister {
	private $cache_dir;

	private $ext = '.cache';
	private $divider = '__';

	public function __construct() {
		$app_dir = GyroString::plain_ascii(Config::get_url(Config::URL_DOMAIN));
		$cache_base_dir = Config::get_value(ConfigFileCache::CACHE_DIR);
		$this->cache_dir = "{$cache_base_dir}cache/$app_dir/";
	}

	/**
	 * Returns true, if item is cached
	 */
	public function is_cached($cache_keys) {
		$item = $this->read($cache_keys);
		if ($item) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Read from cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string
	 * @return ICacheItem|false The cache as array with members "content" and "data", false if cache is not found
	 */
	public function read($cache_keys) {
		$file_name = $this->build_file_name($cache_keys, true);
		if (file_exists($file_name)) {
			$content = @file_get_contents($file_name);
			if ($content === false) {
				return false;
			} else {
				$item = new FileCacheItem($content);
				if ($item->get_expirationdate() > time()) {
					return $item;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
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

		$file = $this->build_file_name($cache_keys, true);
		$serialized = serialize($data);
		file_put_contents($file, $serialized);
	}
	
	/**
	 * Clear the cache
	 * 
	 * @param Mixed A set of key params, may be an array or a string. If NULL, all is cleared
	 */
	public function clear($cache_keys = NULL) {
		if (!empty($cache_keys)) {
			$file_name = $this->build_file_name($cache_keys, false);
			$this->safe_unlink($file_name);
			$dir_name = $this->build_dir_name($cache_keys, false);
			$this->remove_wildcard($dir_name . '*');
		} else {
			$this->safe_unlink($this->cache_dir. $this->ext);
			$this->remove_wildcard($this->cache_dir. '*' . $this->ext);
		}
	}

	private function safe_unlink($file) {
		try {
			if (file_exists($file)) {
				@unlink($file);
			}
		} catch (Exception $ex) {
			// Ignore
		}
	}

	/**
	 * Transform the given param into an array of keys
	 *
	 * @param $cache_keys
	 * @param $strip_empty
	 * @return array
	 */
	private function extract_keys($cache_keys, $strip_empty) {
		$ret = array();
		if (is_array($cache_keys)) {
			$ret = array_values($cache_keys);
		} else if (is_string($cache_keys) || is_numeric($cache_keys)) {
			$ret = array($cache_keys);
		}
		if ($strip_empty) {
			for ($i = count($ret) - 1; $i >= 0; $i--) {
				if ($ret[$i] === '') {
					unset($ret[$i]);
				} else {
					break;
				}
			}
		}

		return $ret;
	}

	/**
	 * Each cache key becomes a directory
	 * @param array $cache_keys
	 * @param bool $strip_empty
	 * @return string
	 */
	private function build_dir_name($cache_keys, $strip_empty) {
		$dirs = $this->extract_keys($cache_keys, $strip_empty);
		$dirs[] = ''; // This makes an empty array and an array of 1 empty string distinct
		$dirs = array_map(function($v) { return GyroString::plain_ascii($v); }, $dirs);
		$path = implode($this->divider, $dirs);
		return $this->cache_dir . $path;
	}

	/**
	 * Each cache key becomes a directory, accept the last, which is assigned an extension .cache
	 * @param array $cache_keys
	 * @param bool $strip_empty
	 * @return string
	 */
	private function build_file_name($cache_keys, $strip_empty) {
		return $this->build_dir_name($cache_keys, $strip_empty) . $this->ext;
	}

	/**
	 * Removes expired cache entries
	 */
	public function remove_expired() {
		// Do nothing, since we can not tell without opening al files
	}

	private function remove_wildcard($pattern) {
		array_map(
			function($file) {
				$this->safe_unlink($file);
			},
			glob($pattern)
		);
	}
}


/**
 * Cache Item for file cache
 *
 * @author Gerd Riesselmann
 * @ingroup FileCache
 */
class FileCacheItem implements ICacheItem {
	/**
	 * Item data
	 *
	 * @var array Cache entry + meta data
	 */
	protected $item_data;

	/**
	 * Constructor
	 *
	 * @param array|string $item_data
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