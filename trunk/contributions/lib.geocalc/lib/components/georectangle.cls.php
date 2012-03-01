<?php
Load::components('geocalculator', 'geocoordinate');
/**
 * Represents a rectangle on the earth surface, a bounding box
 */
class GeoRectangle {
	/**
	 * Left top, north west coordinate
	 * @var GeoCoordinate
	 */
	public $north_west;
	/**
	 * Right bottom, South East coordinate
	 * @var GeoCoordinate
	 */
	public $south_east;

	/**
	 * Contructor
	 *
	 * @param float $lat_nw
	 * @param float $lon_nw
	 * @param float $lat_se
	 * @param float $lon_se
	 */
	public function __construct($lat_nw, $lon_nw, $lat_se, $lon_se) {
		$this->north_west = new GeoCoordinate($lat_nw, $lon_nw);
		$this->south_east = new GeoCoordinate($lat_se, $lon_se);
	}


	/**
	 * Create Rectangle from coordinates
	 *
	 * @static
	 * @param GeoCoordinate $north_west
	 * @param GeoCoordinate $south_east
	 * @return GeoRectangle
	 */
	public static function from_coords(GeoCoordinate $north_west, GeoCoordinate $south_east) {
		return new GeoRectangle($north_west->lat, $north_west->lon, $south_east->lat, $south_east->lon);
	}

	/**
	 * Renders this Coordinate in the form Latitude(divider1)Longitude(divider2)Latitude(divider1)Longitude
	 *
	 * @param string $divider_lat_lon Divider of lat and loin
	 * @param string $divider_coord Divider of both coordinates
	 * @param int $precision Number of digits, passed to String::number() to format values
	 * @param bool $system True to use C locale to format values. False to use current locale. Passed to String::number()
	 *
	 * @return string
	 */
	public function to_string($divider_lat_lon = ', ', $divider_coord = '/', $precision = 4, $system = false) {
		return
			$this->north_west->to_string($divider_lat_lon, $precision, $system) .
			$divider_coord .
			$this->south_east->to_string($divider_lat_lon, $precision, $system);
	}

	/**
	 * Returns center of rectangle
	 *
	 * @return GeoCoordinate
	 */
	public function center() {
		return new GeoCoordinate(
			($this->north_west->lat + $this->south_east->lat) / 2,
			($this->north_west->lon + $this->south_east->lon) / 2
		);
	}

	/**
	 * Returns whether the given coord in within the rectangle or not
	 *
	 * @param GeoCoordinate $coord
	 * @return bool
	 */
	public function contains(GeoCoordinate $coord) {
		return
			($coord->lat <= $this->south_east->lat) &&
			($coord->lat >= $this->north_west->lat) &&
			($coord->lon <= $this->south_east->lon) &&
			($coord->lon >= $this->south_east->lon);
	}

	/**
	 * Returns true, if this coordinate is valid
	 *
	 * Invalid coordinates can be created by passing NULL or a string to contructor
	 *
	 * @return bool
	 */
	public function is_valid() {
		return $this->north_west->is_valid() && $this->south_east->is_valid();
	}

}