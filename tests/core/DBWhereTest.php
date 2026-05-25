<?php
use PHPUnit\Framework\TestCase;

class DBWhereTest extends TestCase {
	public function test_get_operator() {
		$where = new DBWhere(new MockIDBTable(), 'column');
		$this->assertEquals(IDBWhere::LOGIC_AND, $where->get_logical_operator());
		$where = new DBWhere(new MockIDBTable(), 'column', null, null, IDBWhere::LOGIC_OR);
		$this->assertEquals(IDBWhere::LOGIC_OR, $where->get_logical_operator());
	}

	public function test_get_sql() {
		$table = new MockIDBTable();

		$where = new DBWhere($table, 'column');
		$this->assertEquals('column', $where->get_sql());

		$where = new DBWhere($table, 'column', '=', 1234);
		$this->assertEquals("(`alias`.`column` = '1234')", $where->get_sql());

		$where = new DBWhere($table, 'column', IDBWhere::OP_IN, array(1, 2, 3));
		$this->assertEquals("(`alias`.`column` IN ('1', '2', '3'))", $where->get_sql());

		$where = new DBWhere($table, 'column', IDBWhere::OP_NOT_IN, array(1, 2, 3));
		$this->assertEquals("(`alias`.`column` NOT IN ('1', '2', '3'))", $where->get_sql());

		$where = new DBWhere($table, 'column', IDBWhere::OP_NOT_NULL);
		$this->assertEquals("(`alias`.`column` IS NOT NULL )", $where->get_sql());

		$where = new DBWhere($table, 'column', IDBWhere::OP_IS_NULL);
		$this->assertEquals("(`alias`.`column` IS NULL )", $where->get_sql());
	}
}
