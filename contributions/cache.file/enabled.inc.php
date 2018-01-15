<?php
class ConfigFileCache {
	const CACHE_DIR = 'FILE_CACHE_DIR';
	const STORE_SESSIONS = 'FILE_CACHE_STORE_SESSIONS';
}

Config::set_value_from_constant(
	ConfigFileCache::CACHE_DIR,
	'APP_FILE_CACHE_DIR',
	Config::get_value(Config::TEMP_DIR)
);

Config::set_feature_from_constant(
	ConfigFileCache::STORE_SESSIONS, 'APP_FILE_CACHE_STORE_SESSIONS',
	true
);


if (Config::has_feature(ConfigFileCache::STORE_SESSIONS)) {
	// Switch to default file based session handler
	Config::set_value(Config::SESSION_HANDLER, '');
	$cache_dir = Config::get_value(ConfigFileCache::CACHE_DIR);
	session_save_path($cache_dir . 'sessions');
}


