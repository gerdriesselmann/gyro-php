<?php
use PHPUnit\Framework\TestCase;

class DBDriverMysqlTest extends TestCase {
	public function test_get_set() {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host');

		$this->assertEquals('db', $driver->get_db_name());
		$this->assertEquals('host', $driver->get_host());
		$this->assertEquals('mysql', $driver->get_driver_name());
	}

	public function test_escape_database_entity() {
		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host');

		$this->assertEquals('`db`.`t`', $driver->escape_database_entity('t', DBDriverMysql::TABLE));
		$this->assertEquals('`t`', $driver->escape_database_entity('t', DBDriverMysql::ALIAS));
		$this->assertEquals('`t`', $driver->escape_database_entity('t', DBDriverMysql::FIELD));

		$driver = new DBDriverMysql();
		$driver->initialize('db', 'user', 'password', 'host', array('type' => DBDriverMysql::SECONDARY));

		$this->assertEquals('`db`.`t`', $driver->escape_database_entity('t', DBDriverMysql::TABLE));
		$this->assertEquals('`t`', $driver->escape_database_entity('t', DBDriverMysql::ALIAS));
		$this->assertEquals('`t`', $driver->escape_database_entity('t', DBDriverMysql::FIELD));
	}
}
