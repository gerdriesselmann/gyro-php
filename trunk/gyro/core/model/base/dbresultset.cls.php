<?php
/**
 * Result set
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBResultSet implements IDBResultSet {
	/**
	 * Internal PDO Statement
	 *
	 * @var PDOStatement
	 */
	protected $pdo_statement = null;

	public function __construct($pdo) {
		$this->pdo_statement = $pdo;
	}

	/**
	 * Closes internal cursor
	 * 
	 * @return void
	 */
	public function close() {
		$this->pdo_statement->closeCursor();
	}
	
	/**
	 * Returns number of columns in result set
	 *
	 * @return int
	 */
	public function get_column_count() {
		return $this->pdo_statement->columnCount();
	}
	
	/**
	 * Returns number of rows in result set
	 * 
	 * @return int
	 */
	public function get_row_count() {
		return $this->pdo_statement->rowCount();
	}
	
	/**
	 * Returns row as associative array
	 *
	 * @return array | bool False if no more data is available
	 */
	public function fetch() {
		return $this->pdo_statement->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Returns status 
	 *
	 * @param Status
	 */
	public function get_status() {
		$ret = new Status();
		$stub = substr($this->pdo_statement->errorCode(), 0, 2);
		switch ($stub) {
			case '00':
				// No error
				break;
			case '01':
			case 'IM':
				// Warnings
				break;
			default:
				$info = $this->pdo_statement->errorInfo();
				$ret->append($info[2]);
				break;
		}
		return $ret;
	}
}
