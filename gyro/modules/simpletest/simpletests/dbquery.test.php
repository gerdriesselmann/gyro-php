<?php
class DBQueryMock extends DBQuery {
	public function get_sql() {
		return '';
	}
}

class DBQueryTest extends GyroUnitTestCase {
	public function test_set_fields() {
		$table = new MockIDBTable();
		$query = new DBQueryMock($table);
		$this->assertEqual(array(), $query->get_fields());
		$query->set_fields(array('a' => 'b'));
		$this->assertEqual(array('a' => 'b'), $query->get_fields());
		$query->set_fields(DBQuery::CLEAR);
		$this->assertEqual(array(), $query->get_fields());
	}
	
	public function test_policy() {
		$table = new MockIDBTable();

		$query = new DBQueryMock($table);
		$this->assertEqual(DBQuery::NORMAL, $query->get_policy());
		$query->set_policy(4);
		$this->assertEqual(4, $query->get_policy());
		
		$query = new DBQueryMock($table, 4);
		$this->assertEqual(4, $query->get_policy());
	}
	
}