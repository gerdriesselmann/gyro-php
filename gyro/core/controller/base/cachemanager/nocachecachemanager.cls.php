<?php
require_once dirname(__FILE__) . '/constantcachemanager.cls.php';

/**
 * Cache manager to disable caching
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class NoCacheCacheManager extends ConstantCacheManager {
	/**
	 * Constructor
	 *
	 * @param int $duration Cache duration , defaults to 2 hours
	 * @param ICacheHeaderManager $header_manager Defaults to NoCacheCacheHeaderManager
	 */
	public function __construct($duration = 7200, $header_manager = false) {
		parent::__construct('', $duration, $header_manager);
	}
}
