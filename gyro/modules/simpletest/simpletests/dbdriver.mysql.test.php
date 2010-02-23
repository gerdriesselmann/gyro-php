<?php
/**
 * Test MySQL DB Driver
 */
class DBDriverMysqlTest extends GyroUnitTestCase {
	public function test_get_set() {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host');
		
		$this->assertEqual('db', $driver->get_db_name());
		$this->assertEqual('host', $driver->get_host());
		$this->assertEqual('mysql', $driver->get_driver_name());
	}
	
	public function test_escape_database_entity() {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host');
		
		$this->assertEqual('`t`', $driver->escape_database_entity('t', DBDriverMysql::TABLE));
		$this->assertEqual('`t`', $driver->escape_database_entity('t', DBDriverMysql::ALIAS));
		$this->assertEqual('`t`', $driver->escape_database_entity('t', DBDriverMysql::FIELD));

		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host', array('type' => DBDriverMysql::SECONDARY));
		
		$this->assertEqual('`db`.`t`', $driver->escape_database_entity('t', DBDriverMysql::TABLE));
		$this->assertEqual('`t`', $driver->escape_database_entity('t', DBDriverMysql::ALIAS));
		$this->assertEqual('`t`', $driver->escape_database_entity('t', DBDriverMysql::FIELD));
	}
}