<?php
use PHPUnit\Framework\TestCase;

class DBQueryUpdateTest extends TestCase {
	public function test_get_sql() {
		$table = new MockIDBTable();

		$query = new DBQueryUpdate($table);
		$query->set_fields(array('col1' => 1, 'col2' => 'abc'));
		$this->assertEquals(
			"UPDATE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc'",
			$query->get_sql()
		);

		$query->add_where('column', '=', 1);
		$this->assertEquals(
			"UPDATE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1'))",
			$query->get_sql()
		);

		$query->set_policy(DBQueryUpdate::IGNORE);
		$this->assertEquals(
			"UPDATE IGNORE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1'))",
			$query->get_sql()
		);

		$query->set_limit(5);
		$this->assertEquals(
			"UPDATE IGNORE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1')) LIMIT 5",
			$query->get_sql()
		);

		$query->set_limit(5, 5);
		$this->assertEquals(
			"UPDATE IGNORE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1')) LIMIT 5",
			$query->get_sql() // No offset on UPDATE queries!
		);

		$query->add_order('`col1`');
		$this->assertEquals(
			"UPDATE IGNORE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1')) ORDER BY `col1` ASC LIMIT 5",
			$query->get_sql()
		);

		$query->add_order('`col2`', DBQueryUpdate::DESC);
		$this->assertEquals(
			"UPDATE IGNORE `db`.`table` AS `alias` SET `alias`.`col1` = '1', `alias`.`col2` = 'abc' WHERE ((`alias`.`column` = '1')) ORDER BY `col1` ASC, `col2` DESC LIMIT 5",
			$query->get_sql()
		);
	}

	public function test_db_null() {
		$table = new MockIDBTable();

		$query = new DBQueryUpdate($table);
		$query->set_fields(array('col1' => new DBNull(), 'col2' => 'abc'));
		$this->assertEquals(
			"UPDATE `db`.`table` AS `alias` SET `alias`.`col1` = NULL, `alias`.`col2` = 'abc'",
			$query->get_sql()
		);
	}
}
