<?php
/**
 * Result set for Sphinx
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBResultSetSphinx implements IDBResultSet {
	/**
	 * query result
	 *
	 * @var array
	 */
	protected $result = null;
	protected $status;

	public function __construct($result, $status) {
		$this->result = $result;
		$this->status = $status;
	}
	
	/**
	 * Closes internal cursor
	 * 
	 * @return void
	 */
	public function close() {
		$this->result = null;
	}
	
	/**
	 * Returns number of columns in result set
	 *
	 * @return int
	 */
	public function get_column_count() {
		return 0;
	}
	
	/**
	 * Returns number of rows in result set
	 * 
	 * @return int
	 */
	public function get_row_count() {
		$ret = 0;
		if ($this->result) {
			$ret = $this->result['total'];
		}
		return $ret;
	}
	
	/**
	 * Returns row as associative array
	 *
	 * @return array | bool False if no more data is available
	 */
	public function fetch() {
		$ret = false;
		if ($this->result) {
			$record = each($this->result['matches']);
			if ($record) {
				$ret = $this->read_record($record['value']);
			}
		}
		return $ret;
	}
	
	protected function read_record($arr_record) {
		$ret = array();
		foreach($arr_record as $key => $value) {
			if (is_array($value)) {
				$ret = array_merge($ret, $this->read_record($value));
			}
			else {
				$ret[$key] = $value;
			}	
		}
		return $ret;
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
