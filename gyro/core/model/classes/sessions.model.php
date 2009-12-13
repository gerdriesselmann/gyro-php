<?php
/**
 * Table Definition for session
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DAOSessions extends DataObjectBase {
	public $id;              
	public $data;            
	public $modificationdate;

	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
	    return new DBTable(
	    	'sessions',
			array(
				new DBFieldText('id', 60, null, DBFieldText::NOT_NULL),
				new DBFieldText('data', DBFieldText::BLOB_LENGTH_LARGE),
				new DBFieldDateTime('modificationdate', null, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL),				
			),
			'id'			
	    );
	}
}
