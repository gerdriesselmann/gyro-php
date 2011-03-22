<?php
/**
 * This cache header manager sends cache headers that let clients but not proxies 
 * keep an item in cache until it expires, without revalidation
 */
class PrivateLazyCacheHeaderManager extends BaseCacheHeaderManager {
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return "private, max-age=$max_age, must-revalidate";
	}
}
