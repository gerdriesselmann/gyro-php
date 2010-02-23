<?php
/**
 * Test query building on secondary connection
 */
class DBQuerySecondaryTest extends GyroUnitTestCase {
	/**
	 * Table to test
	 * @var DBTable
	 */
	private $table;
	
	public function setUp() {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host', array('type' => DBDriverMysql::SECONDARY));
		$this->table = new MockIDBTable('table', 'alias', $driver);	
	}	
	
	public function test_select() {
		$query = new DBQuerySelect($this->table);
		$this->assertEqual(
			"SELECT `alias`.`column` AS `column` FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}

	public function test_count() {
		$query = new DBQueryCount($this->table);
		$this->assertEqual(
			"SELECT COUNT(*) AS c FROM `db`.`table` AS `alias`",
			$query->get_sql()
		);
	}
	
}