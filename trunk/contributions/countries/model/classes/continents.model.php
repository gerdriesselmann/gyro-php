<?php
/**
 * Model class for continetns
 */
class DAOContinents extends DataObjectBase implements ISelfDescribing, IHierarchic {
    public $id;
    public $name;

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
