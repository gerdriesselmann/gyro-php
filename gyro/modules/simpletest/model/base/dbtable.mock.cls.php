<?php
/**
 * A mock for a DBTable
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class MockIDBTable extends DBTable {
	public function __construct($name = 'table', $alias = 'alias', $connection = DB::DEFAULT_CONNECTION) {
		parent::__construct($name, array(new DBField('column')), array(), array(), array(), $connection);
		$this->alias = $alias;
	}
 }
