<?php
/**
 * @defgroup CacheHeaders
 * @ingroup Page
 * 
 * Introduces different cache header policies than the core.
 * 
 * The core default caching headers allow the browser to keep local caches of pages without checking back
 * for changes for about 10% of local cache duration time. That is: If a page is stored in local cache for ten
 * hours, the browser won't check for the freshness of that page for one hour after fetching it.
 * 
 * This usually is fine, if pages don't change to much. If however your page is highly dynamic, like a forum, 
 * it's usually better to force the browser to check the freshness of a page on each request. While this 
 * extends the number of requests, it let's your users see always the latest content. If the content
 * has not changed, the browser of course still will get a "304 - Not Modified" response.
 * 
 * @author Gerd Riesselmann
 */


/**
 * CacheHeaders config options
 * 
 * Every option can be set through the according APP_ constant, e.g. 
 * to define cache policy constant APP_CACHEHEADERS_CACHE_POLICY.
 * 
 * @author Gerd Riesselmann
 * @ingroup CacheHeaders
 */
class ConfigCacheHeaders {
	/**
	 * Check for rigied freshness, that is force the browser to check a page on each request.
	 */
	const RIGID_FRESHNESS = 'RIGID';

	/**
	 * Policy for cached ressources. Cached means locally cached in DB or memory, not browser cached
	 * 
	 * Allowed values are 
	 * 
	 * - ConfigCacheHeaders::RIGID_FRESHNESS: Browser will be forced to send always send a request. This is the default. 
	 */
	const CACHE_POLICY = 'CACHEHEADER_CACHE_POLICY';
}

Config::set_value_from_constant(
	ConfigCacheHeaders::CACHE_POLICY,
	'APP_CACHEHEADER_CACHE_POLICY',
	ConfigCacheHeaders::RIGID_FRESHNESS
);

ViewFactory::set_implementation(new ViewFactoryCacheHeaders());
