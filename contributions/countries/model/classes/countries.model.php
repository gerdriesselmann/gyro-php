<?php
/**
 * Model class for cuntries
 */
class DAOCountries extends DataObjectBase implements ISelfDescribing {
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
}
