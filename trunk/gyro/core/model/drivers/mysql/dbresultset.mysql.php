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
	 * @var int
	 */
	protected $result_handle = null;
	/**
	 * Status for query
	 *
	 * @var Status
	 */
	protected $status = null;

	public function __construct($result_handle, $status) {
		$this->result_handle = $result_handle;
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
		if ($this->result_handle) {
			mysql_free_result($this->result_handle);
		}
		$this->result_handle = null;
	}
	
	/**
	 * Returns number of columns in result set
	 *
	 * @return int
	 */
	public function get_column_count() {
		if ($this->result_handle) {
			return mysql_num_fields($this->result_handle);
		}
		return false;
	}
	
	/**
	 * Returns number of rows in result set
	 * 
	 * @return int
	 */
	public function get_row_count() {
		if ($this->result_handle) {
			return mysql_num_rows($this->result_handle);
		}
		return 0;
	}
	
	/**
	 * Returns row as associative array
	 *
	 * @return array | bool False if no more data is available
	 */
	public function fetch() {
		if ($this->result_handle) {
			return mysql_fetch_assoc($this->result_handle);
		}
		return false;
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
