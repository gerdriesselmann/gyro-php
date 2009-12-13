<?php
/**
 * A test class, representing teachers at university
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class DAOTeachersTest extends DataObjectBase {
	public $id;
	public $name;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'teacherstest',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('name', 40, null, DBField::NOT_NULL), 
			),
			'id'			
	    );
	}	
}