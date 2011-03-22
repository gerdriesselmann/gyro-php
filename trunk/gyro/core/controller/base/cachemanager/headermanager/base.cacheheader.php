<?php
/**
 * This cache header manager implements the basics of client side caching
 */
class BaseCacheHeaderManager implements ICacheHeaderManager {
	/**
	 * Send cache headers
	 * 
	 * @param string $content
	 * @param timestamp $expirationdate
	 * @param timestamp $lastmodifieddate
	 */
	public function send_headers(&$content, $expirationdate, $lastmodifieddate)	{
		// POST, DELETE etc should not be cached
		if (!in_array(RequestInfo::current()->method(), array('GET', 'HEAD'))) {
			return;
		}
		
		$etag = md5($content);
		
		// Send 304, if applicable
		Common::check_if_none_match($etag);
		Common::check_not_modified($lastmodifieddate); // exits if not modified
		
		
		GyroHeaders::set('Pragma', '', true);
		$max_age = $expirationdate - time();
		GyroHeaders::set('Cache-Control', $this->get_cache_control($expirationdate, $max_age), true);
		GyroHeaders::set('Last-Modified', $lastmodifieddate ? GyroDate::http_date($lastmodifieddate) : '', false);
		GyroHeaders::set('Expires', $expirationdate ? GyroDate::http_date($expirationdate) : '', false);
		GyroHeaders::set('Etag', $etag, true);			
		if (Config::has_feature(Config::TESTMODE)) {
			GyroHeaders::set('X-Gyro-CacheHeader-Class', get_class($this), true);
		}
	}
	
	protected function check_304($etag) {
		
	}
	
	/**
	 * Returns cache control header's content
	 * 
	 * @param timestamp $expirationdate
	 * @param int $max_age Expiratin date minus current timestamp, already preprocessed
	 */
	protected function get_cache_control($expirationdate, $max_age) {
		return 'no-cache,no-store';
	}
}
