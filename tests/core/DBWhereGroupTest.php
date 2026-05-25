<?php
use PHPUnit\Framework\TestCase;

class DBWhereGroupTest extends TestCase {
	public function test_get_operator() {
		$table = new MockIDBTable();
		$where = new DBWhereGroup($table);
		$this->assertEquals(IDBWhere::LOGIC_AND, $where->get_logical_operator());
		$where = new DBWhereGroup($table, IDBWhere::LOGIC_OR);
		$this->assertEquals(IDBWhere::LOGIC_OR, $where->get_logical_operator());
	}

	public function test_get_sql() {
		$table = new MockIDBTable();

		$wheregroup = new DBWhereGroup($table);
		$this->assertEquals('', $wheregroup->get_sql());

		$wheregroup->add_where('column1', '=', 1234);
		$this->assertEquals("((`column1` = '1234'))", $wheregroup->get_sql());

		$where2 = new DBWhere($table, 'column2', '!=', 4321);
		$wheregroup->add_where_object($where2);
		$this->assertEquals("((`column1` = '1234') AND (`column2` != '4321'))", $wheregroup->get_sql());
	}

	public function test_add_empty() {
		$table = new MockIDBTable();

		$wheregroup = new DBWhereGroup($table);
		$this->assertEquals('', $wheregroup->get_sql());

		$wheregroup2 = new DBWhereGroup($table);
		$wheregroup->add_where_object($wheregroup2);
		$wheregroup->add_where('column1', '=', 1234);
		$this->assertEquals("((`column1` = '1234'))", $wheregroup->get_sql());
	}
}
