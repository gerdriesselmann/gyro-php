<?php
/**
 * Caches content client side, but not server side
 * 
 * @author Gerd Riesselmann
 * @ingroup Mime
 */
class MimeCacheManager extends ConstantCacheManager {
	/**
	 * Constructor
	 *
	 * @param int $duration Cache duration , defaults to one month
	 * @param ICacheHeaderManager $header_manager Defaults to PrivateLazyCacheHeaderManager
	 */
	public function __construct($duration = false, $header_manager = false) {
		if (empty($duration)) {
			$duration = GyroDate::ONE_MONTH;
		}
		if (empty($header_manager)) {
			$header_manager = new PrivateLazyCacheHeaderManager();
		}
		parent::__construct('', $duration, $header_manager);
	}	
}