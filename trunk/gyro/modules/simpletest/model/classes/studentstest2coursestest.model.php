<?php
/**
 * A test class, assigning students to courses
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class DAOStudentsTest2CoursesTest extends DataObjectBase {
	public $id_student;
	public $id_course;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'studentstest2coursestest',
			array(
				new DBFieldInt('id_student', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('id_course', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL), 
			),
			array('id_student', 'id_course'),
			array(
				new DBRelation(
					'studentstest',
					new DBFieldRelation('id_student', 'id')
				),
				new DBRelation(
					'coursestest',
					new DBFieldRelation('id_course', 'id')
				)
			)			
	    );
	}	
}