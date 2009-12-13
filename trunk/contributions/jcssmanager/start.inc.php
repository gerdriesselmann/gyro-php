<?php
class ConfigJCSSManager {
	const CSS_DIR = 'JCSS_CSS_DIR';
	const JS_DIR = 'JCSS_JS_DIR';
	
	const USE_COMPRESSED = 'JCSS_USE_COMPRESSED';
	const ALSO_GZIP = 'JCSS_ALSO_GZIP'; 
}


Config::set_value_from_constant(ConfigJCSSManager::CSS_DIR, 'APP_JCSS_CSS_DIR', 'css/');
Config::set_value_from_constant(ConfigJCSSManager::JS_DIR, 'APP_JCSS_JS_DIR', 'js/');
Config::set_value_from_constant(ConfigJCSSManager::USE_COMPRESSED, 'APP_JCSS_USE_COMPRESSED', !APP_TESTMODE);
Config::set_value_from_constant(ConfigJCSSManager::ALSO_GZIP, 'APP_JCSS_ALSO_GZIP', false);
