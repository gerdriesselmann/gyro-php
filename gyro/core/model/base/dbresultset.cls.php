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
	protected ?PDOStatement $pdo_statement = null;

	public function __construct($pdo) {
		$this->pdo_statement = $pdo;
	}

	public function close(): void {
		$this->pdo_statement->closeCursor();
	}

	public function get_column_count(): int {
		return $this->pdo_statement->columnCount();
	}

	public function get_row_count(): int {
		return $this->pdo_statement->rowCount();
	}

	public function fetch(): array|false {
		return $this->pdo_statement->fetch(PDO::FETCH_ASSOC);
	}

	public function get_status(): Status {
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
