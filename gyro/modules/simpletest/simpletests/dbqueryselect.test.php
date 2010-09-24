<?php
class DBQuerySelectTest extends GyroUnitTestCase {
	
	public function test_limit() {
		$table = new MockIDBTable();

		$query = new DBQuerySelect($table);
		$this->assertEqual(array(0,0), $query->get_limit());
		
		$query->set_limit(5);
		$this->assertEqual(array(0,5), $query->get_limit());

		$query->set_limit(1,2);
		$this->assertEqual(array(1,2), $query->get_limit());
		
		$query->set_limit(0);
		$this->assertEqual(array(0,0), $query->get_limit());		
	}
	
	public function test_order() {
		$table = new MockIDBTable();

		$query = new DBQuerySelect($table);
		$this->assertEqual(array(), $query->get_orders());
		
		$query->add_order('col1');
		$this->assertEqual(
			array(
				array('field' => 'col1', 'table' => $table, 'direction' => DBQuerySelect::ASC) 
			), 
			$query->get_orders()
		);

		$query->add_order('col2', DBQuerySelect::DESC);
		$this->assertEqual(
			array(
				array('field' => 'col1', 'table' => $table, 'direction' => DBQuerySelect::ASC), 
				array('field' => 'col2', 'table' => $table, 'direction' => DBQuerySelect::DESC) 
			), 
			$query->get_orders()
		);
		
		$query->add_order(DBQuerySelect::CLEAR);
		$this->assertEqual(array(), $query->get_orders());
	}
	
	public function test_get_sql() {
		$table = new MockIDBTable();

		$query = new DBQuerySelect($table);
		$this->assertEqual(
			"SELECT `alias`.`column` AS `column` FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
		
		$query->add_where('column', '=', 1);
		$this->assertEqual(
			"SELECT `alias`.`column` AS `column` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);
		
		$query->set_policy(DBQuerySelect::DISTINCT);
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`column` AS `column` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1')))",
			$query->get_sql()
		);

		$query->set_policy(DBQuerySelect::FOR_UPDATE);
		$this->assertEqual(
			"SELECT `alias`.`column` AS `column` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) FOR UPDATE",
			$query->get_sql()
		);

		$query->set_policy(DBQuerySelect::FOR_UPDATE | DBQuerySelect::DISTINCT);
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`column` AS `column` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) FOR UPDATE",
			$query->get_sql()
		); // I know this does not make sense - its a test however
		
		$query->set_fields(array('col1', 'col2'));
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`col1` AS `col1`, `alias`.`col2` AS `col2` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) FOR UPDATE",
			$query->get_sql()
		);

		$query->set_limit(5);
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`col1` AS `col1`, `alias`.`col2` AS `col2` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) LIMIT 0,5 FOR UPDATE",
			$query->get_sql()
		);

		$query->add_order('`col1`');
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`col1` AS `col1`, `alias`.`col2` AS `col2` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) ORDER BY `col1` ASC LIMIT 0,5 FOR UPDATE",
			$query->get_sql()
		);
		
		$query->add_order('`col2`', DBQuerySelect::DESC);
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`col1` AS `col1`, `alias`.`col2` AS `col2` FROM `db`.`table` AS `alias` WHERE (((`alias`.`column` = '1'))) ORDER BY `col1` ASC, `col2` DESC LIMIT 0,5 FOR UPDATE",
			$query->get_sql()
		);
		
		$joinTable = new MockIDBTable('jointable', 'joinalias');
		$joinquery = $query->add_join($joinTable);
		$joinquery->add_join_condition('id_join', 'id_org');
		$joinquery->add_where('joincol1', '=', 'val');
		$this->assertEqual(
			"SELECT DISTINCT `alias`.`col1` AS `col1`, `alias`.`col2` AS `col2` FROM `db`.`table` AS `alias` INNER JOIN `db`.`jointable` AS `joinalias` ON ((`id_join` = `id_org`)) WHERE (((`alias`.`column` = '1')) AND (((`joincol1` = 'val')))) ORDER BY `col1` ASC, `col2` DESC LIMIT 0,5 FOR UPDATE",
			$query->get_sql()
		);
	}
	
	public function test_values_as_fields() {
		$table = new MockIDBTable();
		
		// Test selecting values
		$query = new DBQuerySelect($table);
		$query->set_fields("'some value'");
		$this->assertEqual(
			"SELECT 'some value' AS `'some value'` FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);

		$query->set_fields(array("'some value'" => 'column1'));
		$this->assertEqual(
			"SELECT 'some value' AS `column1` FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}
}
