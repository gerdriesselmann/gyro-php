<?php
/**
 * Interface for cache header managers
 * 
 * Cache header managers are resposible for sending HTTP cache headers
 */
interface ICacheHeaderManager {
	/**
	 * Send cache headers
	 * 
	 * @param string $content
	 * @param timestamp $expirationdate
	 * @param timestamp $lastmodifieddate
	 */
	public function send_headers(&$content, $expirationdate, $lastmodifieddate);
}