<?php
/**
 * A test class, representing students at university
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class DAOStudentsTest extends DataObjectBase {
	public $id;
	public $name;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'studentstest',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('name', 40, null, DBField::NOT_NULL), 
				new DBFieldDateTime('modificationdate', null, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL)
			),
			'id'			
	    );
	}	
}