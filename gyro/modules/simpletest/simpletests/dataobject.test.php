<?php
/**
 * Test "if it all works" on pseudo realistic test model
 */
class DataObjectTest extends GyroUnitTestCase {
	public function test_4_join() {
		// Find rooms for a given studen
		/* @var $student DataObjectBase */
		$student = DB::create('studentstest');
		$link = DB::create('studentstest2coursestest');
		$courses = DB::create('coursestest');
		$rooms = DB::create('roomstest');

		$student->id = 5;
		
		$rooms->join($courses);
		$courses->join($link);
		$link->join($student);
		
		$query = $rooms->create_select_query();
		
		$this->assertEqual(
			"SELECT DISTINCT `roomstest`.`id` AS `id`, `roomstest`.`number` AS `number` FROM `roomstest` AS `roomstest` INNER JOIN `coursestest` AS `coursestest` ON (((`coursestest`.`id_room` = `roomstest`.`id`))) INNER JOIN `studentstest2coursestest` AS `studentstest2coursestest` ON (((`studentstest2coursestest`.`id_course` = `coursestest`.`id`))) INNER JOIN `studentstest` AS `studentstest` ON (((`studentstest`.`id` = `studentstest2coursestest`.`id_student`))) WHERE ((((((`studentstest`.`id` = 5))))))",
			$query->get_sql()
		);
	}
	
	public function test_insert() {
		/* @var $student DAOStudentsTest */
		$student = DB::create('studentstest');
		$student->name = 'Heinz';
		
		$query = $student->create_insert_query();
		$this->assertEqual(
			"INSERT INTO `studentstest` (`name`) VALUES ('Heinz')",
			$query->get_sql()
		);
	}
	
	public function test_update() {
		/* @var $student DAOStudentsTest */
		$student = DB::create('studentstest');
		$student->id = 18;
		$student->name = 'Heinz';
		$student->modificationdate = time();
		
		$query = $student->create_update_query();
		$this->assertEqual(
			"UPDATE `studentstest` AS `studentstest` SET `studentstest`.`name` = 'Heinz', `studentstest`.`modificationdate` = DEFAULT WHERE (((`studentstest`.`id` = 18)))",
			$query->get_sql()
		);
	}
	
	public function test_select_update_datetime() {
		/* @var $student DAOStudentsTest */
		$student = DB::create('studentstest');
		$student->add_where('modificationdate', '<', '2008-08-08 08:08:08');
		
		$query = $student->create_select_query();
		$this->assertEqual(
			"SELECT DISTINCT `studentstest`.`id` AS `id`, `studentstest`.`name` AS `name`, UNIX_TIMESTAMP(`studentstest`.`modificationdate`) AS `modificationdate` FROM `studentstest` AS `studentstest` WHERE ((((`studentstest`.`modificationdate` < '2008-08-08 08:08:08'))))",
			$query->get_sql()
		);
		
		$student->name = 'Heinz';
		$query = $student->create_update_query(DataObjectBase::WHERE_ONLY);
		$this->assertEqual(
			"UPDATE `studentstest` AS `studentstest` SET `studentstest`.`name` = 'Heinz' WHERE ((((`studentstest`.`modificationdate` < '2008-08-08 08:08:08'))))",
			$query->get_sql()
		);
		
	}
}