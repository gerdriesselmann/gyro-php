<?php
class DBQueryJoinedTest extends GyroUnitTestCase {
	public function test_join_type() {
		$table_parent = new MockIDBTable();
		$table_joined = new MockIDBTable();
				
		$query_parent = new DBQuerySelect($table_parent);
		
		$query_joined = new DBQueryJoined($table_joined, $query_parent);		
		$this->assertEqual(DBQueryJoined::INNER, $query_joined->get_join_type());
		$query_joined->set_join_type(DBQueryJoined::LEFT);
		$this->assertEqual(DBQueryJoined::LEFT, $query_joined->get_join_type());
		
		$query_joined = new DBQueryJoined($table_joined, $query_parent, DBQueryJoined::RIGHT);		
		$this->assertEqual(DBQueryJoined::RIGHT, $query_joined->get_join_type());
	}
}
