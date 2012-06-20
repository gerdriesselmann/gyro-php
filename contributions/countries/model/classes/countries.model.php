<?php
/**
 * Model class for countries
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
class DAOCountries extends DataObjectBase implements ISelfDescribing, IHierarchic {
    public $id;
    public $id_continent;
	public $code3;
	public $codenum;
    public $name;
    public $capital;
    public $area;
    public $population;
    public $currency;
    public $lat1;
    public $lon1;
	public $lat2;
	public $lon2;
	public $lat_capital;
	public $lon_capital;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
            'countries',
            array(
                new DBFieldText('id', 2, null, DBField::NOT_NULL),
                new DBFieldText('id_continent', 2, null, DBField::NOT_NULL),
                new DBFieldText('name', 50, null, DBField::NOT_NULL),
                new DBFieldText('code3', 3, null, DBField::NONE),
				new DBFieldInt('codenum', null, DBField::NONE),
				new DBFieldText('capital', 50, null, DBField::NONE),
				new DBFieldFloat('area', null, DBFieldFloat::UNSIGNED),
				new DBFieldInt('population', null, DBFieldInt::UNSIGNED),
				new DBFieldText('currency', 3, null, DBField::NONE),
				new DBFieldFloat('lat1', null, DBField::NONE),
				new DBFieldFloat('lon1', null, DBField::NONE),
				new DBFieldFloat('lat2', null, DBField::NONE),
				new DBFieldFloat('lon2', null, DBField::NONE),
				new DBFieldFloat('lat_capital', null, DBField::NONE),
				new DBFieldFloat('lon_capital', null, DBField::NONE),
            ),
            'id',
            new DBRelation(
            	'continents',
            	new DBFieldRelation('id_continent', 'id')
            )
        );
    }
    
    /**
     * Returns continent of this country
     * 
     * @return DAOContinents
     */
    public function get_continent() {
    	return Countries::get_continent($this->id_continent);
    }

	/**
	 * Returns whether country is in group or not
	 *
	 * @param int $group_id ID of group
	 * @return bool
	 */
	public function is_in_group($group_id) {
		$link = new DAOCountries2countriesgroups();
		$link->id_group = $group_id;
		$link->id_country = $this->id;
		return $link->count() > 0;
	}

	/**
	 * Get bounding rectangle
	 *
	 * @return GeoRectangle
	 */
	public function get_bounding_rect() {
		Load::components('geocoordinate');
		return GeoCoordinate::bounding_rect_of(array(
			new GeoCoordinate($this->lat1, $this->lon1),
			new GeoCoordinate($this->lat2, $this->lon2)
		));
	}

	/**
	 * Get title for this class
	 *
	 * @return string
	 */
	public function get_capital() {
		return tr($this->capital, 'countries');
	}

	/**
	 * Return Coordinate of capital
	 *
	 * @return GeoCoordinate
	 */
	public function get_capital_coordinate() {
		Load::components('geocoordinate');
		return new GeoCoordinate($this->lat_capital, $this->lon_capital);
	}

	/**
	 * Returns array of neighboring countries
	 *
	 * @return array Array of DAOCountries
	 */
	public function get_neighbors() {
		Load::models('countries2neighbors');
		$link = new DAOCountries2neighbors();
		$link->id_country_1 = $this->id;
		$countries = new DAOCountries();
		$countries->join($link, new DBJoinCondition($link, 'id_country_2', $countries, 'id'));
		return $countries->find_array();
	}

	// ************************************
	// ISelfDescribing
	// ************************************
	
	/**
	 * Get title for this class
	 * 
	 * @return string
	 */
	public function get_title() {
		return tr($this->name, 'countries');
	}

	/**
	 * Get description for this instance
	 *  
	 * @return string 
	 */
	public function get_description() {
		return '';
	}
	
	// *************************************
	// IHierarchic
	// *************************************
	 	
	/**
	 * Get parent for this item 
	 * 
	 * @return IHierarchic Parent item or null
	 */
	public function get_parent() {
		return $this->get_continent();
	}
	
	/**
	 * Get childs for this item 
	 * 
	 * @return array Array of IHierarchic items
	 */
	public function get_childs() {
		return array();
	}
}
