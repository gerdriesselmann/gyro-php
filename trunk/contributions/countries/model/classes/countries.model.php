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
    public $name;

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
