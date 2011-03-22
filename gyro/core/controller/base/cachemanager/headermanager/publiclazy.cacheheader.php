<?php
/**
 * This cache header manager sends cache headers that let both clients and proxies 
 * keep an item in cache until it expires, without revalidation
 */
class PublicLazyCacheHeaderManager extends BaseCacheHeaderManager {
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return "public, max-age=$max_age, must-revalidate";
	}
}