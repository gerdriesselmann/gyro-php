<?php
class DBQueryCountTest extends GyroUnitTestCase {
	public function test_get_sql() {
		$table = new MockIDBTable();

		$query = new DBQueryCount($table);
		$this->assertEqual(
			"SELECT COUNT(*) AS c FROM `table` AS `alias`",
			$query->get_sql()
		);
		
		$query->add_where('column', '=', 1);
		$this->assertEqual(
			"SELECT COUNT(*) AS c FROM `table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);
		
		$query->set_fields(array('col1', 'col2'));
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`, `alias`.`col2`) AS c FROM `table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->set_limit(5);
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`, `alias`.`col2`) AS c FROM `table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->add_order('`col1`');
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`, `alias`.`col2`) AS c FROM `table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);
		
		$query->add_order('`col2`', DBQueryCount::DESC);
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`, `alias`.`col2`) AS c FROM `table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);
		
		$joinTable = new MockIDBTable('jointable', 'joinalias');
		$joinquery = $query->add_join($joinTable);
		$joinquery->add_join_condition('id_join', 'id_org');
		$joinquery->add_where('joincol1', '=', 'val');
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`, `alias`.`col2`) AS c FROM `table` AS `alias` INNER JOIN `jointable` AS `joinalias` ON ((`id_join` = `id_org`)) WHERE (((`alias`.`column` = '1')) AND (((`joincol1` = 'val'))))",
			$query->get_sql()
		);
	}
	
	public function test_values_as_fields() {
		$table = new MockIDBTable();
		
		// Test selecting values
		$query = new DBQueryCount($table);
		$query->set_fields("'some value'");
		$this->assertEqual(
			"SELECT COUNT(DISTINCT 'some value') AS c FROM `table` AS `alias`",
			$query->get_sql()
		);

		$query->set_fields(array("'some value'" => 'column1'));
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `column1`) AS c FROM `table` AS `alias`",
			$query->get_sql()
		);
	}
	
	public function test_group_by() {
		$table = new MockIDBTable();
		$query = new DBQueryCount($table);
		$query->add_group_by('col1');
		
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`) AS c FROM `table` AS `alias`",
			$query->get_sql()
		);
		
		$query->set_fields(array('sum(col2)' => 'sc'));
		$this->assertEqual(
			"SELECT COUNT(DISTINCT `alias`.`col1`) AS c FROM `table` AS `alias`",
			$query->get_sql()
		);		
	}
}
