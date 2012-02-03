<?php
/**
 * Collection of useful functions related to geo coordinates
 * 
 * @ingroup GeoCalc
 * @author Gerd Riesselmann
 */
class GeoCalculator {
	const R = 6371; // Earth radius in km
	
	/**
	 * Calculates distance between to points
	 * 
	 * @param float $lat1 Latitude of point 1;
	 * @param float $lon1 Longitude of point 1;
	 * @param float $lat2 Latitude of point 2;
	 * @param float $lon2 Longitude of point 2;
	 * @return float Distance in km
	 */
	public static function distance($lat1, $lon1, $lat2, $lon2) {
		// http://www.movable-type.co.uk/scripts/latlong.html
		$lat1 = deg2rad($lat1);
		$lat2 = deg2rad($lat2);
		$lon1 = deg2rad($lon1);
		$lon2 = deg2rad($lon2);
		
		$dLat = $lat2 - $lat1;
		$dLon = $lon2 - $lon1; 
		$a = 
			sin($dLat/2) * sin($dLat/2) +
			cos($lat1) * cos($lat2) * 
			sin($dLon/2) * sin($dLon/2); 
		$c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
		$d = self::R * $c;
		return $d;
	}
	
	/**
	 * Compute bounding box
	 * 
	 * @param float $lat Latitude
	 * @param float $lon Longitude
	 * @param float $ns_radius Radius of bound box in north to south direction
	 * @param float $we_radius Radius of bound box in west to east direction
	 * 
	 * @return array Array with two elements 'lat' and 'lon', each containing an array 
	 *               with two elements 'min' and 'max 
	 */
	public static function bounding_box($lat, $lon, $ns_radius, $we_radius) {
		// http://www.movable-type.co.uk/scripts/latlong-db.html
		$d_lat = rad2deg($ns_radius/self::R);
		$d_lon = rad2deg($we_radius/self::R/cos(deg2rad($lat)));
		
		return array(
			'lat' => array('min' => $lat - $d_lat, 'max' => $lat + $d_lat),
			'lon' => array('min' => $lon - $d_lon, 'max' => $lon + $d_lon)
		);
	}

	/**
	 * Compute bounding box of given coordinates
	 *
	 * @code
	 * GeoCalculator::bounding_box_of(array(
	 *   array('lat' => 50.96951, 'lon' => 7.0446),
	 *   array('lat' => 50.92181, 'lon' => 6.9462)
	 * );
	 * @endcode
	 *
	 * @static
	 * @param $arr_coordinates Array of associative array with keys "lat" and "lon"
	 * @return array Array with two elements 'lat' and 'lon', each containing an array
	 *               with two elements 'min' and 'max. False if $arr_coordinates is empty
	 */
	public static function bounding_box_of($arr_coordinates) {
		$first = array_shift($arr_coordinates);
		if ($first == false) {
			return false;
		}
		$lat_min = Arr::get_item($first, 'lat', 0.0);
		$lat_max = $lat_min;
		$lon_min = Arr::get_item($first, 'lon', 0.0);
		$lon_max = $lon_min;

		foreach($arr_coordinates as $c) {
			$lat = Arr::get_item($c, 'lat', 0.0);
			$lon = Arr::get_item($c, 'lon', 0.0);
			$lat_min = min($lat_min, $lat);
			$lat_max = max($lat_max, $lat);
			$lon_min = min($lon_min, $lon);
			$lon_max = max($lon_max, $lon);
		}

		return array(
			'lat' => array('min' => $lat_min, 'max' => $lat_max),
			'lon' => array('min' => $lon_min, 'max' => $lon_max)
		);
	}
}