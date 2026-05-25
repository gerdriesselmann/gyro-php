<?php
/**
 * Result set for MySQL
 *
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBResultSetMysql implements IDBResultSet {
	/**
	 * Handle to query result
	 *
	 * @var mysqli_result|null
	 */
	protected ?mysqli_result $result_set = null;
	/**
	 * Status for query
	 *
	 * @var Status
	 */
	protected ?Status $status = null;

	public function __construct($result_set, $status) {
		$this->result_set = $result_set;
		$this->status = $status;
	}

	public function __destruct() {
		$this->close();
	}

	public function close(): void {
		if ($this->result_set) {
			$this->result_set->close();
			$this->result_set = null;
		}
	}

	public function get_column_count(): int {
		if ($this->result_set) {
			return $this->result_set->field_count;
		} else {
			return 0;
		}
	}

	public function get_row_count(): int {
		if ($this->result_set) {
			return $this->result_set->num_rows;
		}
		else {
			return 0;
		}
	}

	public function fetch(): array|false {
		if ($this->result_set) {
			$row = $this->result_set->fetch_assoc();
			return $row === null ? false : $row;
		}
		else {
			return false;
		}
	}

	public function get_status(): Status {
		return $this->status;
	}
}
