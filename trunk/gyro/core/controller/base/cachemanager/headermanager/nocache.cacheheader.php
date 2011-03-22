<?php
/**
 * This cache header manager sends cache headers that forbid the client to cache
 */
class NoCacheCacheHeaderManager implements ICacheHeaderManager {
	/**
	 * Send cache headers
	 * 
	 * @param string $content
	 * @param timestamp $expirationdate
	 * @param timestamp $lastmodifieddate
	 */
	public function send_headers(&$content, $expirationdate, $lastmodifieddate)	{
		GyroHeaders::set('Pragma', 'no-cache', false);
		GyroHeaders::set('Cache-Control', 'no-cache,no-store', false);
		GyroHeaders::set('Last-Modified', '', false);
		GyroHeaders::set('Expires', GyroDate::http_date(time() - GyroDate::ONE_DAY), false);	
	}	
}
