<?php
/**
 * A test class, representing a course at university
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class DAOCoursesTest extends DataObjectBase {
	public $id;
	public $id_room;
	public $id_teacher;
	public $title;
	public $description;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'coursestest',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('id_room', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('id_teacher', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('title', 100, null, DBField::NOT_NULL),
				new DBFieldText('description', DBFieldText::BLOB_LENGTH_SMALL, DBField::NONE)
			),
			'id',
			array(
				new DBRelation(
					'roomstest',
					new DBFieldRelation('id_room', 'id')
				),
				new DBRelation(
					'teacherstest',
					new DBFieldRelation('id_teacher', 'id')
				)
			),
			null,
			new DBDriverMySqlMock()								
	    );
	}	
}
