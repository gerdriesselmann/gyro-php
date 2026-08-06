<?php
use PHPUnit\Framework\TestCase;

class DBQuerySecondaryTest extends TestCase {
	private $table;

	protected function setUp(): void {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host', array('type' => DBDriverMysql::SECONDARY));
		$this->table = new MockIDBTable('table', 'alias', $driver);
	}

	public function test_select() {
		$query = new DBQuerySelect($this->table);
		$this->assertEquals(
			"SELECT `alias`.`column` AS `column` FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}

	public function test_count() {
		$query = new DBQueryCount($this->table);
		$this->assertEquals(
			"SELECT COUNT(*) AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}
}
