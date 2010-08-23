<?php
Load::components('geocalculator');
 
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
		$this->lat = $lat;
		$this->lon = $lon;
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
	 * Calculates distance between this and other points
	 * 
	 * @param GeoCoordinate $other Coordinate of point
	 * @return float Distance in km or FALSE if one of the coordinates is invalid
	 */
	public function distance_to($other) {
		$ret = false;
		if ($this->is_valid() && $other->is_valid()) {
			$ret = GeoCalculator::distance($this->lat, $this->lon, $other->lat, $other->lon);
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
}