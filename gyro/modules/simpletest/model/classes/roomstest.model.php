<?php
/**
 * A test class, representing a room in university
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class DAORoomsTest extends DataObjectBase {
	public $id;
	public $number;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'roomstest',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('number', 10, null, DBField::NOT_NULL), 
			),
			'id',
			null,
			null,
			new DBDriverMySqlMock()			
	    );
	}	
}