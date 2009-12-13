<?php
require_once dirname(__FILE__) . '/simplecachemanager.cls.php';

/**
 * Caches only when logged in, uses current URL
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class SuccessCacheManager extends SimpleCacheManager {
	/**
	 * Page data
	 *
	 * @var PageData
	 */
	protected $page_data;

	public function initialize($page_data) {
		$this->page_data = $page_data;
	}	

	/**
	 * Return a chache id
	 */
	public function get_cache_id() {
		$ret = false;
		if ($this->page_data->successful()) {
			$ret = parent::get_cache_id();
		}
		return $ret; 
	}
}
