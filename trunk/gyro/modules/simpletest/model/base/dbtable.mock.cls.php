<?php
/**
 * A mock for a DBTable
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class MockIDBTable extends DBTable {
	public function __construct($name = 'table', $alias = 'alias') {
		parent::__construct($name, array(new DBField('column')), array(), array(), array(), new DBDriverMySqlMock());
		$this->alias = $alias;
	}
 }
