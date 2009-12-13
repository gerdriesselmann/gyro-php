<?php
/**
 * A mock for a DBTable
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class MockIDBTable extends DBTable {
	protected $alias;
	
	public function __construct($name = 'table', $alias = 'alias') {
		parent::__construct($name);
		$this->alias = $alias;
		$field = new DBField('column');
		$this->add_field($field);
	}

	/**
	 * Returns alias of table, if any
	 * 
	 * @return string
	 */
	public function get_table_alias() {
		return $this->alias;
	}
 }
