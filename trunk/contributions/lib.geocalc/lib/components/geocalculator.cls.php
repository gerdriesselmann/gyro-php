<?php
/**
 * Collection of useful functions related to geo coordinates
 * 
 * @ingroup GeoCalc
 * @author Gerd Riesselmann
 */
class GeoCalculator {
	/**
	 * Convert degrees into radion
	 * 
	 * @param float $degree A degree value (latitude or longitude)
	 * @return float Radian value
	 */
	public static function to_radian($degree) {
		return $degree * pi() /180;
	}
	
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
		$lat1 = self::to_radian($lat1);
		$lat2 = self::to_radian($lat2);
		$lon1 = self::to_radian($lon1);
		$lon2 = self::to_radian($lon2);
		
		$R = 6371; // km
		$dLat = $lat2 - $lat1;
		$dLon = $lon2 - $lon1; 
		$a = 
			sin($dLat/2) * sin($dLat/2) +
			cos($lat1) * cos($lat2) * 
			sin($dLon/2) * sin($dLon/2); 
		$c = 2 * atan2(sqrt($a), sqrt(1-$a)); 
		$d = $R * $c;
		return $d;
	}
}