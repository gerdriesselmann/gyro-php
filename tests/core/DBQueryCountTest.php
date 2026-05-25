<?php
use PHPUnit\Framework\TestCase;

class DBQueryCountTest extends TestCase {
	public function test_get_sql() {
		$table = new MockIDBTable();

		$query = new DBQueryCount($table);
		$this->assertEquals(
			"SELECT COUNT(*) AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);

		$query->add_where('column', '=', 1);
		$this->assertEquals(
			"SELECT COUNT(*) AS c FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->set_fields(array('col1', 'col2'));
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`, `alias`.`col2`) AS c FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->set_limit(5);
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`, `alias`.`col2`) AS c FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->add_order('`col1`');
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`, `alias`.`col2`) AS c FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->add_order('`col2`', DBQueryCount::DESC);
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`, `alias`.`col2`) AS c FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$joinTable = new MockIDBTable('jointable', 'joinalias');
		$joinquery = $query->add_join($joinTable);
		$joinquery->add_join_condition('id_join', 'id_org');
		$joinquery->add_where('joincol1', '=', 'val');
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`, `alias`.`col2`) AS c FROM `db`.`table` AS `alias` INNER JOIN `db`.`jointable` AS `joinalias` ON ((`id_join` = `id_org`)) WHERE (((`alias`.`column` = '1')) AND (((`joincol1` = 'val'))))",
			$query->get_sql()
		);
	}

	public function test_values_as_fields() {
		$table = new MockIDBTable();

		$query = new DBQueryCount($table);
		$query->set_fields("'some value'");
		$this->assertEquals(
			"SELECT COUNT('some value') AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);

		$query->set_fields(array("'some value'" => 'column1'));
		$this->assertEquals(
			"SELECT COUNT('some value') AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}

	public function test_group_by() {
		$table = new MockIDBTable();
		$query = new DBQueryCount($table);
		$query->add_group_by('col1');

		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`) AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);

		$query->set_fields(array('sum(col2)' => 'sc'));
		$this->assertEquals(
			"SELECT COUNT(`alias`.`col1`) AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}
}
