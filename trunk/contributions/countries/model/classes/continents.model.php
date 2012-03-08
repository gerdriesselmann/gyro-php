<?php
/**
 * Model class for continetns
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
class DAOContinents extends DataObjectBase implements ISelfDescribing, IHierarchic {
    public $id;
    public $name;
	public $lat1;
	public $lon1;
	public $lat2;
	public $lon2;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
            'continents',
            array(
                new DBFieldText('id', 2, null, DBField::NOT_NULL),
                new DBFieldText('name', 50, null, DBField::NOT_NULL),
				new DBFieldFloat('lat1', null, DBField::NONE),
				new DBFieldFloat('lon1', null, DBField::NONE),
				new DBFieldFloat('lat2', null, DBField::NONE),
				new DBFieldFloat('lon2', null, DBField::NONE),
            ),
            'id'
        );
    }
    
    /**
     * Return countries - lcoalized
     */
    public function get_countries() {
    	$adapter = Countries::create_continent_adapter($this->id);
    	Countries::localize_adapter($adapter);
    	return $adapter->execute();
    }

	/**
	 * Get bounding rectangle
	 *
	 * @return GeoRectangle
	 */
	public function get_bounding_rect() {
		Load::components('georectangle');
		return new GeoRectangle($this->lat1, $this->lon1, $this->lat2, $this->lon2);
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
		return false;
	}
	
	/**
	 * Get childs for this item 
	 * 
	 * @return array Array of IHierarchic items
	 */
	public function get_childs() {
		return $this->get_countries();
	}
}
