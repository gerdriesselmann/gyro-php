<?php
use PHPUnit\Framework\TestCase;

class DBQueryMockPHPUnit extends DBQuery {
	public function get_sql() {
		return '';
	}
}

class DBQueryTest extends TestCase {
	public function test_set_fields() {
		$table = new MockIDBTable();
		$query = new DBQueryMockPHPUnit($table);
		$this->assertEquals(array(), $query->get_fields());
		$query->set_fields(array('a' => 'b'));
		$this->assertEquals(array('a' => 'b'), $query->get_fields());
		$query->set_fields(DBQuery::CLEAR);
		$this->assertEquals(array(), $query->get_fields());
	}

	public function test_policy() {
		$table = new MockIDBTable();

		$query = new DBQueryMockPHPUnit($table);
		$this->assertEquals(DBQuery::NORMAL, $query->get_policy());
		$query->set_policy(4);
		$this->assertEquals(4, $query->get_policy());

		$query = new DBQueryMockPHPUnit($table, 4);
		$this->assertEquals(4, $query->get_policy());
	}
}
