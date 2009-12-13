<?php
class DBWhereGroupTest extends GyroUnitTestCase {
	public function test_get_operator() {
		$table = new MockIDBTable();
		$where = new DBWhereGroup($table);
		$this->assertEqual(IDBWhere::LOGIC_AND, $where->get_logical_operator());
		$where = new DBWhereGroup($table, IDBWhere::LOGIC_OR);
		$this->assertEqual(IDBWhere::LOGIC_OR, $where->get_logical_operator());
	}
	
	public function test_get_sql() {
		$table = new MockIDBTable();
		
		$wheregroup = new DBWhereGroup($table);
		$this->assertEqual('', $wheregroup->get_sql());
		
		$wheregroup->add_where('column1', '=', 1234);
		$this->assertEqual("((`column1` = '1234'))", $wheregroup->get_sql());
		
		$where2 = new DBWhere($table, 'column2', '!=', 4321);
		$wheregroup->add_where_object($where2);		
		$this->assertEqual("((`column1` = '1234') AND (`column2` != '4321'))", $wheregroup->get_sql());
	}
	
	public function test_add_empty() {
		$table = new MockIDBTable();
		
		$wheregroup = new DBWhereGroup($table);
		$this->assertEqual('', $wheregroup->get_sql());
		
		$wheregroup2 = new DBWhereGroup($table);
		$wheregroup->add_where_object($wheregroup2);
		$wheregroup->add_where('column1', '=', 1234);
		$this->assertEqual("((`column1` = '1234'))", $wheregroup->get_sql());
	}
}