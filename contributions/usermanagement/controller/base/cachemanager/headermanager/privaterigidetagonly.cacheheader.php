<?php
/**
 * This cache header manager allows the client to store a page
 * but forces it to revalidate, which though is checked only 
 * against the etag
 */
class PrivateRigidEtagOnlyCacheHeaderManager extends PrivateRigidCacheHeaderManager {
	/**
	 * Send cache headers
	 * 
	 * @param string $content
	 * @param timestamp $expirationdate
	 * @param timestamp $lastmodifieddate
	 */
	public function send_headers(&$content, $expirationdate, $lastmodifieddate)	{
		parent::send_headers($content, $expirationdate, false);
	}	
}
