<?php
/**
 * Caches content client side, but not server side
 */
class BinariesCacheManager extends ConstantCacheManager {
		/**
	 * Constructor
	 *
	 * @param int $duration Cache duration , defaults to value set in ConfigBinaries::CLIENT_CACHE_DURATION
	 * @param ICacheHeaderManager $header_manager Defaults to class et in ConfigBinaries::CACHEHEADER_CLASS
	 */
	public function __construct($duration = false, $header_manager = false) {
		if (empty($duration)) {
			$duration = Config::get_value(ConfigBinaries::CLIENT_CACHE_DURATION);
		}
		if (empty($header_manager)) {
			$header_manager = CacheHeaderManagerFactory::create(Config::get_value(ConfigBinaries::CACHEHEADER_CLASS));
		}
		parent::__construct('', $duration, $header_manager);
	}	
}