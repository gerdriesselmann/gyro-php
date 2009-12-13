<?php
/**
 * Model class for cuntries
 */
class DAOCountriesgroups extends DataObjectBase implements ISelfDescribing {
    public $id;
    public $name;
    public $abbrevation;
    public $type;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
			'countriesgroups',
            array(
				new DBFieldInt('id', null, DBFieldInt::UNSIGNED | DBFieldInt::AUTOINCREMENT | DBField::NOT_NULL),
				new DBFieldText('name', 50, null, DBField::NOT_NULL),
				new DBFieldText('abbrevation', 10, null, DBField::NONE),
				new DBFieldEnum('type', array_keys(Countries::get_group_types(), Countries::GROUP_TYPE_NONE, DBField::NOT_NULL))
			),
			'id'
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
