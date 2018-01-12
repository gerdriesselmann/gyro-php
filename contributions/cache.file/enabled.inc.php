<?php
class ConfigFileCache {
	const CACHE_DIR = 'FILE_CACHE_DIR';
}

Config::set_value_from_constant(
	ConfigFileCache::CACHE_DIR,
	'APP_FILE_CACHE_DIR',
	Config::get_value(Config::TEMP_DIR)
);

// Switch to default file based session handler
Config::set_value(Config::SESSION_HANDLER, '');
$cache_dir = Config::get_value(ConfigFileCache::CACHE_DIR);
session_save_path($cache_dir . 'sessions');


