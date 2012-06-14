<?php
Load::components('geocalculator', 'georectangle');
 
/**
 * Class repersenting a coordinate
 * 
 * @ingroup GeoCalc
 * @author Gerd Riesselmann
 */
class GeoCoordinate {
	/**
	 * Latitude
	 * @var double
	 */
	public $lat;
	/**
	 * Longitude
	 * @var double
	 */
	public $lon;
	
	/**
	 * Contructor
	 * 
	 * @param double $lat Latitude
	 * @param double $lon Longitude
	 */
	public function __construct($lat, $lon) {
		$this->lat = GeoCalculator::normalize_lat($lat);
		$this->lon = GeoCalculator::normalize_lon($lon);
	} 
	
	/**
	 * Renders this Coordinate in the form Latitude(divider)Longitude
	 * 
	 * @param string $divider 
	 * @param int $precision Number of digits, passed to String::number() to format values
	 * @param bool $system True to use C locale to format values. False to use current locale. Passed to String::number()
	 * 
	 * @return string
	 */
	public function to_string($divider = ', ', $precision = 4, $system = false) {
		return 
			$this->lat_to_string($precision, $system) .
			$divider .
			$this->lon_to_string($precision, $system);
	}

	public static function from_string($val, $divider = ', ', $system = false) {
		$latlon = explode($divider, $val);
		$lat = array_shift($latlon);
		$lat = ($system) ? floatval($lat) : String::delocalize_number($lat);
		$lon = array_shift($latlon);
		$lon = ($system) ? floatval($lon) : String::delocalize_number($lon);
		return new GeoCoordinate($lat, $lon);
	}

	/**
	 * Renders this Coordinate's Latitude to string
	 * 
	 * @param int $precision Number of digits, passed to String::number() to format values
	 * @param bool $system True to use C locale to format values. False to use current locale. Passed to String::number()
	 * 
	 * @return string
	 */
	public function lat_to_string($precision = 4, $system = false) {
		return String::number($this->lat, $precision, $system);
	}
	
	/**
	 * Renders this Coordinate's Longitude to string
	 * 
	 * @param int $precision Number of digits, passed to String::number() to format values
	 * @param bool $system True to use C locale to format values. False to use current locale. Passed to String::number()
	 * 
	 * @return string
	 */
	public function lon_to_string($precision = 4, $system = false) {
		return String::number($this->lon, $precision, $system);
	}
	
	/**
	 * Calculates shortest distance between this and other points
	 * 
	 * @param GeoCoordinate $other Coordinate of point
	 * @return float Distance in km or FALSE if one of the coordinates is invalid
	 */
	public function distance_to(GeoCoordinate $other) {
		$ret = false;
		if ($this->is_valid() && $other->is_valid()) {
			$ret = GeoCalculator::distance($this->lat, $this->lon, $other->lat, $other->lon);
		}
		return $ret;
	}

	/**
	 * FInd center between two coordinates
	 *
	 * @param GeoCoordinate $other
	 * @return bool|GeoCoordinate
	 */
	public function center_of(GeoCoordinate $other) {
		$ret = false;
		if ($this->is_valid() && $other->is_valid()) {
			$ret = new GeoCoordinate(
				$this->lat + ($other->lat - $this->lat) / 2.0,
				$this->lon + ($other->lon - $this->lon) / 2.0
			);
		}
		return $ret;
	}

	/**
	 * Returns true if this coordinate is inside the rectangle defined by $coord1 und $coord2
	 *
	 * @param GeoCoordinate $coord1
	 * @param GeoCoordinate $coord2
	 * @return bool
	 */
	public function is_within($coord1, $coord2) {
		$ret = false;
		if ($this->is_valid() && $coord1->is_valid() && $coord2->is_valid()) {
			$rect = GeoRectangle::from_coords($coord1, $coord2);
			$ret = $rect->contains($this);
		}
		return $ret;
	}

	/**
	 * Returns true, if this coordinate is valid
	 * 
	 * Invalid coordinates can be created by passing NULL or a string to contructor
	 * 
	 * @return bool
	 */
	public function is_valid() {
		return is_numeric($this->lat) && is_numeric($this->lon);
	}

	/**
	 * Compute bounding box
	 * 
	 * @param float $ns_radius Radius of bound box in north to south direction
	 * @param float $we_radius Radius of bound box in west to east direction
	 * 
	 * @return array Array with two elements 'min' and 'max', containing a GeoCoordinate instance 
	 * 
	 * @attention The bounding box is a rectangle, so the distance to elements located within the 
	 *            corners of that rectangle may be larger than the radius passed.
	 */
	public function bounding_box($ns_radius, $we_radius) {
		$arr = GeoCalculator::bounding_box($this->lat, $this->lon, $ns_radius, $we_radius);
		
		return array(
			'min' => new GeoCoordinate($arr['lat']['min'], $arr['lon']['min']),
			'max' => new GeoCoordinate($arr['lat']['max'], $arr['lon']['max'])
		);
	}

	/**
	 * Compute bounding box
	 *
	 * @param float $ns_radius Radius of bound box in north to south direction
	 * @param float $we_radius Radius of bound box in west to east direction
	 *
	 * @return GeoRectangle
	 *
	 * @attention The bounding box is a rectangle, so the distance to elements located within the
	 *            corners of that rectangle may be larger than the radius passed.
	 */
	public function bounding_rect($ns_radius, $we_radius) {
		$arr = GeoCalculator::bounding_box($this->lat, $this->lon, $ns_radius, $we_radius);
		return new GeoRectangle($arr['lat']['min'], $arr['lon']['min'], $arr['lat']['max'], $arr['lon']['max']);
	}

	/**
	 * Calculate a bounding box that contains all of coordinates passed
	 *
	 * @static
	 * @param $arr_coordinates
	 * @return array Array with two elements 'min' and 'max', containing a GeoCoordinate instance
	 */
	public static function boundig_box_of($arr_coordinates) {
		$arr_data = array();
		/* @var $coord GeoCoordinate */
		foreach($arr_coordinates as $coord) {
			if ($coord->is_valid()) {
				$arr_data[] = array('lat' => $coord->lat, 'lon' => $coord->lon);
			}
		}
		$arr = GeoCalculator::bounding_box_of($arr_data);
		return $arr ? array(
			'min' => new GeoCoordinate($arr['lat']['min'], $arr['lon']['min']),
			'max' => new GeoCoordinate($arr['lat']['max'], $arr['lon']['max'])
		) : false;
	}
}