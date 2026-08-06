<?php
use PHPUnit\Framework\TestCase;

class DBQueryJoinedTest extends TestCase {
	public function test_join_type() {
		$table_parent = new MockIDBTable();
		$table_joined = new MockIDBTable();

		$query_parent = new DBQuerySelect($table_parent);

		$query_joined = new DBQueryJoined($table_joined, $query_parent);
		$this->assertEquals(DBQueryJoined::INNER, $query_joined->get_join_type());
		$query_joined->set_join_type(DBQueryJoined::LEFT);
		$this->assertEquals(DBQueryJoined::LEFT, $query_joined->get_join_type());

		$query_joined = new DBQueryJoined($table_joined, $query_parent, DBQueryJoined::RIGHT);
		$this->assertEquals(DBQueryJoined::RIGHT, $query_joined->get_join_type());
	}
}
