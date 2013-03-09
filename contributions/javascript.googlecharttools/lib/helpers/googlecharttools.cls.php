<?php
/**
 * Helper for using Google Chart Tools
 * 
 * @author Gerd Riesselmann
 * @ingroup GoogleChartTools
 */
class GoogleChartTools {
	const CORE_CHART = 'corechart';
	const GEO_CHART = 'geochart';
	const TABLE = 'table';


	private static $packages = array();

	public static function enable($package) {
		self::$packages[] = $package;
	}

	public static function prepare($page_data) {
		if (count(self::$packages) > 0) {
			$page_data->head->add_js_file('https://www.google.com/jsapi');
			$packages_quoted = array_map(function($v) { return "'$v'"; }, self::$packages);
			$packages_quoted = array_unique($packages_quoted);
			$packages_string = implode(', ', $packages_quoted);
			$lang = strtolower(GyroLocale::get_language());
			$page_data->head->add_js_snippet("google.load('visualization', '1', {'packages':[$packages_string], 'language': '$lang'});");
		}
	}
}
