<?php
/**
 * Model class for assigning countries to countries in a neighbor relationship
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
class DAOCountries2neighbors extends DataObjectBase {
    public $id_country_1;
    public $id_country_2;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
			'countries2neighbors',
			array(
				new DBFieldText('id_country_1', 2, null, DBField::NOT_NULL),
				new DBFieldText('id_country_2', 2, null, DBField::NOT_NULL),
			),
			array('id_country_1', 'id_country_2'),
			array(
				new DBRelation(
					'countries',
					new DBFieldRelation('id_country_1', 'id')
				),
				new DBRelation(
					'countries',
					new DBFieldRelation('id_country_2', 'id')
				),
			)
		);
    }
}
