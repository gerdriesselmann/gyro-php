<?php
/**
 * Model class for translation of country names
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
class DAOCountriestranslations extends DataObjectBase {
    public $id;
    public $lang;
    public $name;

    /**
     * Create table definition
     *
     * @return DBTable
     */
    protected function create_table_object() {
        return new DBTable(
            'countriestranslations',
            array(
                new DBFieldText('id_country', 2, null, DBField::NOT_NULL),
                new DBFieldText('lang', 5, null, DBField::NOT_NULL),
                new DBFieldText('name', 50, null, DBField::NOT_NULL),
            ),
            array('id_country', 'lang'),
            new DBRelation(
            	'countries',
            	new DBFieldRelation('id_country', 'id')
            )
        );
    }
}
