<?php
use PHPUnit\Framework\TestCase;

class DBQueryInsertTest extends TestCase {
	public function test_get_sql() {
		$table = new MockIDBTable();

		$query = new DBQueryInsert($table);
		$this->assertEquals(
			"INSERT INTO `db`.`table` () VALUES ()",
			$query->get_sql()
		);

		$query->add_where('column', '=', 1);
		$this->assertEquals(
			"INSERT INTO `db`.`table` () VALUES ()",
			$query->get_sql()
		); // WHERE is ignored

		$query->set_policy(DBQueryInsert::DELAYED);
		$this->assertEquals(
			"INSERT DELAYED INTO `db`.`table` () VALUES ()",
			$query->get_sql()
		);

		$query->set_fields(array('col1' => 1, 'col2' => 2));
		$this->assertEquals(
			"INSERT DELAYED INTO `db`.`table` (`col1`, `col2`) VALUES ('1', '2')",
			$query->get_sql()
		);

		$table_select = new MockIDBTable('stable', 'salias');
		$field_select = new DBField('scolumn');
		$table_select->add_field($field_select);

		$query_select = new DBQuerySelect($table_select);
		$query->set_select($query_select);
		$this->assertEquals(
			"INSERT DELAYED INTO `db`.`table` (`col1`, `col2`) SELECT `salias`.`column` AS `column`, `salias`.`scolumn` AS `scolumn` FROM `db`.`stable` AS `salias`",
			$query->get_sql()
		);

		$query->set_policy(DBQueryInsert::DELAYED | DBQueryInsert::IGNORE);
		$this->assertEquals(
			"INSERT DELAYED IGNORE INTO `db`.`table` (`col1`, `col2`) SELECT `salias`.`column` AS `column`, `salias`.`scolumn` AS `scolumn` FROM `db`.`stable` AS `salias`",
			$query->get_sql()
		);
	}
}
