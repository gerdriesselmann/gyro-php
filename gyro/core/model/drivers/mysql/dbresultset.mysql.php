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
	 * @var mysqli_result
	 */
	protected $result_set = null;
	/**
	 * Status for query
	 *
	 * @var Status
	 */
	protected $status = null;

	public function __construct($result_set, $status) {
		$this->result_set = $result_set;
		$this->status = $status;
	}
	
	public function __destruct() {
		$this->close();
	}

	/**
	 * Closes internal cursor
	 * 
	 * @return void
	 */
	public function close() {
		if ($this->result_set) {
			$this->result_set->close();
			$this->result_set = null;
		}
	}
	
	/**
	 * Returns number of columns in result set
	 *
	 * @return int
	 */
	public function get_column_count() {
		if ($this->result_set) {
			return $this->result_set->field_count;
		} else {
			return 0;
		}
	}
	
	/**
	 * Returns number of rows in result set
	 * 
	 * @return int
	 */
	public function get_row_count() {
		if ($this->result_set) {
			return $this->result_set->num_rows;
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Returns row as associative array
	 *
	 * @return array | bool False if no more data is available
	 */
	public function fetch() {
		if ($this->result_set) {
			return $this->result_set->fetch_assoc();
		}
		else {
			return array();
		}
	}
	
	/**
	 * Returns status 
	 *
	 * @param Status
	 */
	public function get_status() {
		return $this->status;
	}
}
