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
	 * @param int $precision Number of digits, passed to GyroString::number() to format values
	 * @param bool $system True to use C locale to format values. False to use current locale. Passed to GyroString::number()
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
		$center_lon = ($this->north_west->lon + $this->south_east->lon) / 2;
		if ($this->spans_180_deg_meridian()) {
			// This rect spans over the -180/180 boundary.
			$center_lon = $center_lon + 180.0;
		}
		return new GeoCoordinate(
			($this->north_west->lat + $this->south_east->lat) / 2,
			$center_lon
		);
	}

	/**
	 * Returns true if the rect spans the 180° meridian (date line)
	 */
	private function spans_180_deg_meridian() {
		return ($this->north_west->lon > $this->south_east->lon);
	}

	/**
	 * Returns whether the given coord in within the rectangle or not
	 *
	 * @param GeoCoordinate $coord
	 * @return bool
	 */
	public function contains(GeoCoordinate $coord) {
		$lat_matches = ($coord->lat <= $this->north_west->lat) && ($coord->lat >= $this->south_east->lat);
		$test_lon = $coord->lon;
		$lon_matches = false;
		if ($this->spans_180_deg_meridian()) {
			// This rect spans over the -180/180 boundary. We look if point on opposite is inside opposite rectangle
			$test_lon = GeoCalculator::normalize_lon($test_lon + 180.0);
			$lon_matches = ($test_lon <= $this->north_west->lon) && ($test_lon >= $this->south_east->lon);
		}
		else {
			$lon_matches = ($test_lon >= $this->north_west->lon) && ($test_lon <= $this->south_east->lon);
		}

		return $lat_matches && $lon_matches;
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

	/**
	 * Configure a DB query, assuming lat and lon are stored in two columns
	 *
	 * @param IDBWhereHolder $holder
	 * @param string $lat_field_name
	 * @param string $lon_field_name
	 */
	public function configure_query(IDBWhereHolder $holder, $lat_field_name, $lon_field_name) {
		$table = $holder->get_wheres()->get_table();
		$where_lat = new DBWhereGroup($table);
		$where_lat->add_where($lat_field_name, '>=', $this->north_west->lat_to_string(4, true));
		$where_lat->add_where($lat_field_name, '<=', $this->south_east->lat_to_string(4, true));

		$where_lon = new DBWhereGroup($table);
		if ($this->spans_180_deg_meridian()) {
			// e.g. -178 => +178
			$where_lon->add_where($lon_field_name, '<=', $this->north_west->lon_to_string(4, true));
			$where_lon->add_where($lon_field_name, '>=', $this->south_east->lon_to_string(4, true), IDBWhere::LOGIC_OR);
		} else {
			$where_lon->add_where($lon_field_name, '>=', $this->north_west->lon_to_string(4, true));
			$where_lon->add_where($lon_field_name, '<=', $this->south_east->lon_to_string(4, true));
		}

		$holder->add_where_object($where_lat);
		$holder->add_where_object($where_lon);
	}
}