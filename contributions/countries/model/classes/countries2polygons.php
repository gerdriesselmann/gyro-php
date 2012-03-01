<?php
/**
 * DAO for table countries2polygons
 */
class DAOCountries2polygons extends DataObjectBase {
	public $id_country;
	public $polygon;
	public $lat1;
	public $lon1;
	public $lat2;
	public $lon2;

	/**
	 * Create Table description
	 *
	 * @return DBTable
	 */
	function create_table_object() {
		return new DBTable(
			'countries2polygons',
			array(
				new DBFieldText('id_country', 2, null, DBField::NOT_NULL),
				new DBFieldText('polygon', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NOT_NULL),
				new DBFieldFloat('lat1', null, DBField::NOT_NULL),
				new DBFieldFloat('lon1', null, DBField::NOT_NULL),
				new DBFieldFloat('lat2', null, DBField::NOT_NULL),
				new DBFieldFloat('lon2', null, DBField::NOT_NULL),
			),
			'id_country',
			new DBRelation('countries', new DBFieldRelation('id_country', 'id'))
		);
	}

	public function get_bounding_rect() {
		Load::components('georectangle');
		return new GeoRectangle($this->lat1, $this->lon1, $this->lat2, $this->lon2);
	}
}