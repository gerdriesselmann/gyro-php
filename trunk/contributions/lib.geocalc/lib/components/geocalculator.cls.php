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
	 * Calculates shortest distance between to points
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
	 * Normalizes Latitude (must be between -90 and 90)
	 *
	 * @param float $lat
	 * @return float
	 */
	public static function normalize_lat($lat) {
		if ($lat > 90.0) {
			return self::normalize_lat(180.0 - $lat); // 100° => 80°
		} else if ($lat < -90.0) {
			return self::normalize_lat(-180.0 - $lat);  // -100°  => -80°
		} else {
			return $lat;
		}
	}
	
	/**
	 * Normalizes longitude (must be between -180 and 180)
	 *
	 * @param float $lon
	 * @return float
	 */
	public static function normalize_lon($lon) {
		if ($lon > 180.0) {
			return self::normalize_lon($lon - 360.0); // 181° => -179°
		} else if ($lon <= -180.0) {
			return self::normalize_lon($lon + 360.0); // -181° => 179°
		} else {
			return $lon;
		}
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
			'lat' => array('min' => self::normalize_lat($lat - $d_lat), 'max' => self::normalize_lat($lat + $d_lat)),
			'lon' => array('min' => self::normalize_lon($lon - $d_lon), 'max' => self::normalize_lon($lon + $d_lon))
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

		$has_only_negative_lons = $lon_min <= 0;
		$has_only_positive_lons = $lon_min >= 0;
		foreach($arr_coordinates as $c) {
			$lat = Arr::get_item($c, 'lat', 0.0);
			$lon = Arr::get_item($c, 'lon', 0.0);
			$has_only_negative_lons = $has_only_negative_lons && ($lon <= 0);
			$has_only_positive_lons = $has_only_positive_lons && ($lon >= 0);

			$lat_min = min($lat_min, $lat);
			$lat_max = max($lat_max, $lat);
			$lon_min = min($lon_min, $lon);
			$lon_max = max($lon_max, $lon);
		}

		if (!$has_only_negative_lons && !$has_only_positive_lons) {
			// Spans the prime meridian OR the 180° median. Check witch rectangle would be smaller
			// Think -178, -179, 179, 178: Min is 178, Max is -178
			// We know that there are at least 2 items, one < 0, and one > 0, and that recent $lon_min is negative
			$distance_lon_prime_meridian = $lon_max - $lon_min;

			$lon_min_180 =  180.0;
			$lon_max_180 = -180.0;;
			array_unshift($arr_coordinates, $first);
			foreach($arr_coordinates as $c) {
				$lon = Arr::get_item($c, 'lon', 180.0);
				if ($lon >= 0 && $lon < $lon_min_180) { $lon_min_180 = $lon; }
				if ($lon <  0 && $lon > $lon_max_180) { $lon_max_180 = $lon; }
			}

			$distance_lon_180_meridian = 180 + $lon_max_180 + 180 - $lon_min_180;
			if ($distance_lon_180_meridian < $distance_lon_prime_meridian) {
				$lon_min = $lon_min_180;
				$lon_max = $lon_max_180;
			}
		}

		return array(
			'lat' => array('min' => $lat_min, 'max' => $lat_max),
			'lon' => array('min' => $lon_min, 'max' => $lon_max)
		);
	}
}