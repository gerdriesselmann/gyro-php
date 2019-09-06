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

	private static $versions = array(
		'3.4' => array('3.4.1', 'sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo='),
		'2.2' => array('2.2.4', 'sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44='),
		'1.12' => array('1.12.4', 'sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ='),
		'1.11' => array('1.11.3', 'sha384-6ePHh72Rl3hKio4HiJ841psfsRJveeS+aLoaEf3BWfS+gTF0XdAqku2ka8VddikM'),
		'1.10' => array('1.10.2'),
		'1.9' => array('1.9.1'),
		'1.7' => array('1.7.1'),
		'1.6' => array('1.6.4'),
		'1.5' => array('1.5.2'),
		'1.4' => array('1.4.4'),
		'1.3' => array('1.3.2')
	);

	public static function get_path() {
		$cdn = trim(Config::get_value(ConfigJQuery::CDN));
		if (empty($cdn)) {
			return 'js/jquery.js';
		}
		
		// Resolve CDN
		if ($cdn == 'google') { $cdn = self::CDN_GOOGLE; }
		elseif ($cdn == 'ms') { $cdn = self::CDN_MS; }
		elseif ($cdn == 'jquery') { $cdn = self::CDN_JQUERY; }

		$version_data = self::get_version_data();
		$version = $version_data[0];

		$cdn = str_replace('%version%', $version, $cdn);
		$version_min = preg_replace('|\.0$|', '', $version);
		$cdn = str_replace('%version_min%', $version_min, $cdn);
		
		return $cdn;
	}
	
	public static function get_head_data_file() {
		return new HeadDataFile(
			self::get_path(), 
			self::get_subresource_integrity()
		);
	}
	
	private static function get_subresource_integrity() {
		$cdn = trim(Config::get_value(ConfigJQuery::CDN));
		if (empty($cdn)) {
			return '';
		}

		$version_data = self::get_version_data();
		if (count($version_data) > 1) {
			return $version_data[1];
		} else {
			return '';
		}
	}

	private static function get_version_data() {
		$data = Arr::get_item(self::$versions, Config::get_value(ConfigJQuery::VERSION), null);
		if (empty($data)) {
			throw new Exception('Unknown JQuery Version ' . Config::get_value(ConfigJQuery::VERSION));
		}
		return $data;
	}
}
