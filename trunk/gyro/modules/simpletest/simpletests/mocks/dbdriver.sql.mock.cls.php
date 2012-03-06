<?php
class DBDriverMySqlMock extends DBDriverMysql {
	public $queries = array();

	public function __construct() {
		$this->initialize('db', 'user', 'password', 'host');
	}
	
	/**
	 * Connect if not already connceted
	 * 
	 * @return void
	 */
	protected function connect() {
		// Do nothing, this is a mock
	}
	
	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escape($value) {
		return mysql_real_escape_string(Cast::string($value));
	}

	/**
	 * Execute an SQL command (Insert, Update...)
	 *
	 * @param string $sql
	 * @return Status
	 */
	public function execute($sql) {
		$this->queries[] = $sql;
		return new Status();
	}

	/**
	 * Execute a Select statement
	 *
	 * @param string $sql
	 * @return IDBResultSet
	 */
	public function query($sql) {
		$this->queries[] = $sql;
		return false;
	}
}