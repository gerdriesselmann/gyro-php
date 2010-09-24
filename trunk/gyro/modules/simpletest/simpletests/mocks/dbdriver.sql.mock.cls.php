<?php
class DBDriverMySqlMock extends DBDriverMysql {
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
}