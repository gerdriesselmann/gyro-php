<?php

class DBWhereTest extends GyroUnitTestCase {
	public function test_get_operator() {
		$where = new DBWhere(new MockIDBTable(), 'column');
		$this->assertEqual(IDBWhere::LOGIC_AND, $where->get_logical_operator());
		$where = new DBWhere(new MockIDBTable(), 'column', null, null, IDBWhere::LOGIC_OR);
		$this->assertEqual(IDBWhere::LOGIC_OR, $where->get_logical_operator());
	}
	
	public function test_get_sql() {
		$table = new MockIDBTable();
		
		$where = new DBWhere($table, 'column');
		$this->assertEqual('column', $where->get_sql());
		
		$where = new DBWhere($table, 'column', '=', 1234);
		$this->assertEqual("(`alias`.`column` = '1234')", $where->get_sql());
	
		$where = new DBWhere($table, 'column', IDBWhere::OP_IN, array(1,2,3));
		$this->assertEqual("(`alias`.`column` IN ('1', '2', '3'))", $where->get_sql());
	
		$where = new DBWhere($table, 'column', IDBWhere::OP_NOT_IN, array(1,2,3));
		$this->assertEqual("(`alias`.`column` NOT IN ('1', '2', '3'))", $where->get_sql());

		$where = new DBWhere($table, 'column', IDBWhere::OP_NOT_NULL);
		$this->assertEqual("(`alias`.`column` IS NOT NULL )", $where->get_sql());
	
		$where = new DBWhere($table, 'column', IDBWhere::OP_IS_NULL);
		$this->assertEqual("(`alias`.`column` IS NULL )", $where->get_sql());
	}
}