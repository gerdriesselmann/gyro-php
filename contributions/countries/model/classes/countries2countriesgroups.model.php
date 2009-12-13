<?php
/**
 * Model class for cuntries
 */
class DAOCountries2countriesgroups extends DataObjectBase {
    public $id_country;
    public $id_group;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
			'countries2countriesgroups',
            array(
            	new DBFieldText('id_country', 2, null, DBField::NOT_NULL),
				new DBFieldInt('id_group', null, DBFieldInt::UNSIGNED | DBField::NOT_NULL),
			),
			array('id_country', 'id_group'),
			array(
	            new DBRelation(
	            	'countries',
	            	new DBFieldRelation('id_country', 'id')
	            ),
				new DBRelation(
            		'countriesgroups',
            		new DBFieldRelation('id_group', 'id')
            	)
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
