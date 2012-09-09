<?php
/**
 * Allow client and proxy to cache, but force it to look up ressource on each request
 */
class PublicNoRevalidateCacheHeaderManager extends BaseCacheHeaderManager {
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return "public, max-age=$max_age";
	}
}