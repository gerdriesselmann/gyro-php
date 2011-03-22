<?php
require_once dirname(__FILE__) . '/constantcachemanager.cls.php';

/**
 * Very simple cache manager. Returns the current url as cache id
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class SimpleCacheManager extends ConstantCacheManager {
	public function __construct($duration = 7200) {
		$url = Url::current();
		$cache_id = array();
			
		if ($url->get_host() != Config::get_value(Config::URL_DOMAIN)) {
			$cache_id[] = $url->get_host();
		}
		
		$path = $url->get_path();
		if (empty($path)) {
			$path = '.';
		}
		$cache_id[] = $path;			
		$cache_id[] = $url->get_query();
		parent::__construct($cache_id, $duration, CacheHeaderManagerFactory::create(Config::get_value(Config::CACHEHEADER_CLASS_CACHED)));
	}
}
