<?php
// Disable DB based sessions
class ConfigMemcache {
	const MEMCACHE_HOST = 'MEMCACHE_HOST';
	const MEMCACHE_PORT = 'MEMCACHE_PORT';
	const MEMCACHE_STORE_SESSIONS = 'MEMCACHE_STORE_SESSIONS'; 
}

Config::set_value_from_constant(ConfigMemcache::MEMCACHE_HOST, 'APP_MEMCACHE_HOST', 'localhost');
Config::set_value_from_constant(ConfigMemcache::MEMCACHE_PORT, 'APP_MEMCACHE_PORT', 11211);
Config::set_feature_from_constant(ConfigMemcache::MEMCACHE_STORE_SESSIONS, 'APP_MEMCACHE_STORE_SESSIONS', true);

require_once dirname(__FILE__) . '/lib/components/memcache.cls.php';
GyroMemcache::init();
GyroMemcache::add_server(
	Config::get_value(ConfigMemcache::MEMCACHE_HOST),
	Config::get_value(ConfigMemcache::MEMCACHE_PORT)
);
if (Config::has_feature(ConfigMemcache::MEMCACHE_STORE_SESSIONS)) {
	// Switch Session Handler
	require_once dirname(__FILE__) . '/session.memcache.impl.php';
	Config::set_value(Config::SESSION_HANDLER, 'MemcacheSession');
}


