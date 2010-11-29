<?php
/**
 * An overloaded PageView to serve other cache headers
 * 
 * @author Gerd Riesselmann
 * @ingroup CacheHeaders
 */
class CacheHeadersPageView extends PageViewBase {
	/**
	 * Send cache control headers for cache
	 *
	 * @param $lastmodified A timestamp 
	 * @param $expires A timestamp
	 * @param $max_age Max age in seconds
	 */
	protected function send_cache_headers($lastmodified, $expires, $max_age = 600, $etag = '') {
		$max_age = intval($max_age);
		Common::header('Pragma', '', false);
		Common::header('Last-Modified', GyroDate::http_date($lastmodified), false);
		Common::header('Expires', GyroDate::http_date($expires), false);
		Common::header('Etag', $etag, true);
		switch (Config::get_value(ConfigCacheHeaders::CACHE_POLICY)) {
			case ConfigCacheHeaders::RIGID_FRESHNESS:
			default:
				Common::header('Cache-Control', "private, must-revalidate, no-cache, max-age=0, pre-check=0", false);		
		}		
	}
}
