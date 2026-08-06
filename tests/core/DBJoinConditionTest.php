<?php
use PHPUnit\Framework\TestCase;

class DBJoinConditionTest extends TestCase {
	public function test_get_sql() {
		$table1 = new MockIDBTable();

		$table2 = new MockIDBTable('table2', 'alias2');
		$field2 = new DBField('column2');
		$table2->add_field($field2);

		$where = new DBJoinCondition($table1, 'column', $table2, 'column2');
		$this->assertEquals('(`alias`.`column` = `alias2`.`column2`)', $where->get_sql());
	}
}
