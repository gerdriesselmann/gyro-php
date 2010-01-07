<?php
require_once dirname(__FILE__) . '/dbresultset.sphinx.php';
/**
 * Result set for Sphinx count queries
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBResultSetCountSphinx extends DBResultSetSphinx {
	protected $done = false;
	
	/**
	 * Returns row as associative array
	 *
	 * @return array | bool False if no more data is available
	 */
	public function fetch() {
		$ret = false;
		if ($this->result && !$this->done) {
			$ret = array('c' => $this->result['total_found']);
			$this->done = true;
		}
		return $ret;
	}
}
