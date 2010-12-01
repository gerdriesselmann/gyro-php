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
		GyroHeaders::remove('Pragma');
		GyroHeaders::set('Last-Modified', GyroDate::http_date($lastmodified), true);
		GyroHeaders::set('Expires', GyroDate::http_date($expires), true);
		GyroHeaders::set('Etag', $etag, true);
		switch (Config::get_value(ConfigCacheHeaders::CACHE_POLICY)) {
			case ConfigCacheHeaders::RIGID_FRESHNESS:
			default:
				GyroHeaders::set('Cache-Control', "private, must-revalidate,max-age=0", true);		
		}		
	}
}
