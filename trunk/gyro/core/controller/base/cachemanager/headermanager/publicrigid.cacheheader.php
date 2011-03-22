<?php
/**
 * Allow client and proxy to cache, but force it to look up ressource on each request
 */
class PublicRigidCacheHeaderManager extends BaseCacheHeaderManager {
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return "private, must-revalidate, max-age=0";
	}
}