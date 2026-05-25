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
	 * @var array|null
	 */
	protected ?array $result = null;
	protected Status $status;

	public function __construct($result, $status) {
		$this->result = $result;
		$this->status = $status;
	}

	public function close(): void {
		$this->result = null;
	}

	public function get_column_count(): int {
		return 0;
	}

	public function get_row_count(): int {
		$ret = 0;
		if ($this->result) {
			$ret = $this->result['total'];
		}
		return $ret;
	}

	public function fetch(): array|false {
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

	public function get_status(): Status {
		return $this->status;
	}
}
