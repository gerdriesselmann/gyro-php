<?php
require_once dirname(__FILE__) . '/constantcachemanager.cls.php';

/**
 * Cache manager to disable caching
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class NoCacheCacheManager extends ConstantCacheManager {
	public function __construct() {
		parent::__construct('', -GyroDate::ONE_DAY);
	}
}
