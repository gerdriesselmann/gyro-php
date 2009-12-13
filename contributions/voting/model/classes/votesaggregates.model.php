<?php
/**
 * Model class for Votes
 * 
 * Votes can hold values from 0 to 100 (so they contain percents, actually)
 */
class DAOVotesaggregates extends DataObjectBase {
	public $id;
	public $instance;
	public $average;
	public $numtotal;
	public $modificationdate;
	
	// Table structure
	protected function create_table_object() {
	    return new DBTable(
	    	'votesaggregates',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInstanceReference('instance'), 
				new DBFieldFloat('average'),
				new DBFieldInt('numtotal'),
				new DBFieldDateTime('modificationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP),
			),
			'id'			
	    );
	}
	
	public function get_average($precision = 0) {
		$f = pow(10, $precision);
		$ret = intval(ceil($this->average * $f)) / $f;
		return $ret;
	}
}
