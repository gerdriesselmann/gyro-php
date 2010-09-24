<?php
class DBQueryDeleteTest extends GyroUnitTestCase {
	public function test_get_sql() {
		$table = new MockIDBTable();
		
		$query = new DBQueryDelete($table);
		$this->assertEqual(
			"TRUNCATE `db`.`table`",
			$query->get_sql()
		);

		$query = new DBQueryDelete($table);
		$query->set_limit(5);
		$this->assertEqual(
			"DELETE FROM `db`.`table` LIMIT 0,5",
			$query->get_sql()
		);
		
		$query = new DBQueryDelete($table);
		$query->add_order('col1');
		$this->assertEqual(
			"DELETE FROM `db`.`table` ORDER BY col1 ASC",
			$query->get_sql()
		);
		
		$query = new DBQueryDelete($table);
		$query->add_where('column', '=', 1);
		$this->assertEqual(
			"DELETE FROM `db`.`table` WHERE ((`alias`.`column` = '1'))",
			$query->get_sql()
		);
		
		$query->set_limit(5);
		$this->assertEqual(
			"DELETE FROM `db`.`table` WHERE ((`alias`.`column` = '1')) LIMIT 0,5",
			$query->get_sql()
		);

		$query->add_order('col1');
		$this->assertEqual(
			"DELETE FROM `db`.`table` WHERE ((`alias`.`column` = '1')) ORDER BY col1 ASC LIMIT 0,5",
			$query->get_sql()
		);
		
		$query->add_order('col2', DBQueryDelete::DESC);
		$this->assertEqual(
			"DELETE FROM `db`.`table` WHERE ((`alias`.`column` = '1')) ORDER BY col1 ASC, col2 DESC LIMIT 0,5",
			$query->get_sql()
		);
	}
}
