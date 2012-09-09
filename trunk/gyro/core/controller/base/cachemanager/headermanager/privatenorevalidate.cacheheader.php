<?php
/**
 * Allow client and proxy to cache, but force it to look up ressource on each request
 */
class PrivateNoRevalidateCacheHeaderManager extends BaseCacheHeaderManager {
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return "private, max-age=$max_age";
	}
}