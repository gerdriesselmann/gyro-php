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
			"SELECT DISTINCT `roomstest`.`id` AS `id`, `roomstest`.`number` AS `number` FROM `db`.`roomstest` AS `roomstest` INNER JOIN `db`.`coursestest` AS `coursestest` ON (((`coursestest`.`id_room` = `roomstest`.`id`))) INNER JOIN `db`.`studentstest2coursestest` AS `studentstest2coursestest` ON (((`studentstest2coursestest`.`id_course` = `coursestest`.`id`))) INNER JOIN `db`.`studentstest` AS `studentstest` ON (((`studentstest`.`id` = `studentstest2coursestest`.`id_student`))) WHERE ((((((`studentstest`.`id` = 5))))))",
			$query->get_sql()
		);
	}
	
	public function test_insert() {
		/* @var $student DAOStudentsTest */
		$student = DB::create('studentstest');
		$student->name = 'Heinz';
		
		$query = $student->create_insert_query();
		$this->assertEqual(
			"INSERT INTO `db`.`studentstest` (`name`) VALUES ('Heinz')",
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
			"UPDATE `db`.`studentstest` AS `studentstest` SET `studentstest`.`name` = 'Heinz', `studentstest`.`modificationdate` = DEFAULT WHERE (((`studentstest`.`id` = 18)))",
			$query->get_sql()
		);
	}

	public function test_update_null() {
		/* @var $dao DAOCoursesTest */
		$dao = DB::create('coursestest');
		$dao->id = 18;
		$dao->title = 'Programming for Beginners';
		$dao->id_teacher = 1;
		$dao->id_room = 5;
		$dao->description = new DBNull();
		
		$query = $dao->create_update_query();
		$this->assertEqual(
			"UPDATE `db`.`coursestest` AS `coursestest` SET `coursestest`.`id_room` = 5, `coursestest`.`id_teacher` = 1, `coursestest`.`title` = 'Programming for Beginners', `coursestest`.`description` = NULL WHERE (((`coursestest`.`id` = 18)))",
			$query->get_sql()
		);

		$dao = DB::create('coursestest');
		$params = array(
			'id' => 18,
			'title' => 'Programming for Beginners',
			'id_teacher' => new DBNull(),
			'id_room' => 5,
			'description' => new DBNull()
		);
		$dao->read_from_array($params);
		$query = $dao->create_update_query();
		$this->assertEqual(
			"UPDATE `db`.`coursestest` AS `coursestest` SET `coursestest`.`id_room` = 5, `coursestest`.`id_teacher` = NULL, `coursestest`.`title` = 'Programming for Beginners', `coursestest`.`description` = NULL WHERE (((`coursestest`.`id` = 18)))",
			$query->get_sql()
		);
	}
	
	public function test_select_update_datetime() {
		/* @var $student DAOStudentsTest */
		$student = DB::create('studentstest');
		$student->add_where('modificationdate', '<', '2008-08-08 08:08:08');
		
		$query = $student->create_select_query();
		$this->assertEqual(
			"SELECT `studentstest`.`id` AS `id`, `studentstest`.`name` AS `name`, UNIX_TIMESTAMP(`studentstest`.`modificationdate`) AS `modificationdate` FROM `db`.`studentstest` AS `studentstest` WHERE ((((`studentstest`.`modificationdate` < '2008-08-08 08:08:08'))))",
			$query->get_sql()
		);
		
		$student->name = 'Heinz';
		$query = $student->create_update_query(DataObjectBase::WHERE_ONLY);
		$this->assertEqual(
			"UPDATE `db`.`studentstest` AS `studentstest` SET `studentstest`.`name` = 'Heinz' WHERE ((((`studentstest`.`modificationdate` < '2008-08-08 08:08:08'))))",
			$query->get_sql()
		);
	}

    public function test_serialize() {
        $time = time();

        /* @var DAOStudentsTest $student */
        $student = DB::create('studentstest');
        $student->id = 10;
        $student->name = 'Heinz';
        $student->modificationdate = $time;
        $student->add_where('modificationdate', '<', '2008-08-08 08:08:08');

        $serialized = serialize($student);
        /** @var DAOStudentsTest $unserialized */
        $unserialized = unserialize($serialized);
        $this->assertTrue($unserialized instanceof DAOStudentsTest);
        $this->assertEqual($student->id, $unserialized->id);
        $this->assertEqual($student->name, $unserialized->name);
        $this->assertEqual($student->get_table_name(), $unserialized->get_table_name());

        // Legacy serialization format from PHP 8.2 (__sleep)
        $legacy_data = "O:15:\"DAOStudentsTest\":3:{s:19:\"\0DAOStudentsTest\0id\";i:10;s:21:\"\0DAOStudentsTest\0name\";s:5:\"Heinz\";s:33:\"\0DAOStudentsTest\0modificationdate\";i:" . $time . ";}";
        $unserialized = unserialize($legacy_data);
        assert($unserialized->id === 10);
        assert($unserialized->name === "Heinz");
        assert($unserialized->modificationdate === $time);
    }
}