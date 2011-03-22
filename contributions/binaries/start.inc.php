<?php
/**
 * Binaries config options
 * 
 * @since 0.6.0
 * 
 * Every option can be set through the according APP_ constant, e.g. 
 * to define default client cache duratation use APP_BINARIES_CLIENT_CACHE_DURATION
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class ConfigBinaries {
	/**
	 * Time in seconds binaries should be cached at client side
	 * 
	 * @since 0.5.1
	 */
	const CLIENT_CACHE_DURATION = 'BINARIES_CLIENT_CACHE_DURATION';
	
	/**
	 * CacheHeaderManager policy for binaries 
	 * 
	 * Class name without CacheHeaderManager, e.g. PrivateLazy for PrivateLazyCacheHeaderManager
	 */
	const CACHEHEADER_CLASS = 'BINARIES_CACHEHEADER_CLASS';
}


Config::set_value_from_constant(ConfigBinaries::CLIENT_CACHE_DURATION, 'APP_BINARIES_CLIENT_CACHE_DURATION', 30 * GyroDate::ONE_DAY);
Config::set_value_from_constant(ConfigBinaries::CACHEHEADER_CLASS, 'APP_BINARIES_CACHEHEADER_CLASS', 'PrivateLazy');
