<?php
class DBJoinConditionTest extends GyroUnitTestCase {
	public function test_get_sql() {
		$table1 = new MockIDBTable();
		
		$table2 = new MockIDBTable('table2', 'alias2');
		$field2 = new DBField('column2');
		$table2->add_field($field2);
		
		$where = new DBJoinCondition($table1, 'column', $table2, 'column2');
		$this->assertEqual('(`alias`.`column` = `alias2`.`column2`)', $where->get_sql());
	}
}
