<?php
/**
 * Helper for using JQuery 
 * 
 * @author Gerd Riesselmann
 * @ingroup JQuery
 */
class JQuery {
	const CDN_GOOGLE = 'https://ajax.googleapis.com/ajax/libs/jquery/%version%/jquery.min.js';
	const CDN_MS = 'https://ajax.aspnetcdn.com/ajax/jQuery/jquery-%version_min%.min.js';
	const CDN_JQUERY = 'https://code.jquery.com/jquery-%version%.min.js';

	public static function get_path() {
		$cdn = trim(Config::get_value(ConfigJQuery::CDN));
		if (empty($cdn)) {
			return 'js/jquery.js';
		}
		
		// Resolve CDN
		if ($cdn == 'google') { $cdn = self::CDN_GOOGLE; }
		elseif ($cdn == 'ms') { $cdn = self::CDN_MS; }
		elseif ($cdn == 'jquery') { $cdn = self::CDN_JQUERY; }

		$version = '';
		switch(Config::get_value(ConfigJQuery::VERSION)) {
			case '1.3': 
				$version = JQUERY_VERSION_1_3;
				break;
			case '1.4': 
				$version = JQUERY_VERSION_1_4;
				break;
			case '1.5': 
				$version = JQUERY_VERSION_1_5;
				break;
			case '1.6': 
				$version = JQUERY_VERSION_1_6;
				break;
			case '1.7':
				$version = JQUERY_VERSION_1_7;
				break;
			case '1.9':
				$version = JQUERY_VERSION_1_9;
				break;
			case '1.10':
				$version = JQUERY_VERSION_1_10;
				break;
			case '1.11':
				$version = JQUERY_VERSION_1_11;
				break;
		}
		if (empty($version)) {
			throw new Exception('Unknown JQuery Version ' . Config::get_value(ConfigJQuery::VERSION));
		}
		
		$cdn = str_replace('%version%', $version, $cdn);
		$version_min = preg_replace('|\.0$|', '', $version);
		$cdn = str_replace('%version_min%', $version_min, $cdn);
		
		return $cdn;
	}
}
