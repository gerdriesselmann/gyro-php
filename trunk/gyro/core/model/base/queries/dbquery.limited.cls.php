<?php
/**
 * A limited query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
abstract class DBQueryLimited extends DBQuery {
	/**
	 * Array with two members: 1. start_row, 2. number of rows
	 *
	 * @var array
	 */
	protected $limit = array(0, 0);
	
	/**
	 * Set limit
	 *
	 * @param int $start_or_count start record (0-based) or count, if $count is 0
	 * @param int $count Optional. Number of rows to retrieve
	 */
	public function set_limit($start_or_count, $count = null) {
		if ($count) {
			$this->limit = array($start_or_count, $count);
		}
		else {
			$this->limit = array(0, $start_or_count);
		}
	}
	
	/**
	 * Returns limit
	 * 
	 * @return array Array with two members: 1. start_row, 2. number of rows
	 */
	public function get_limit() {
		return $this->limit;
	}
}
