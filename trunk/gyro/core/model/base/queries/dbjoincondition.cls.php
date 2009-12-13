<?php
/**
 * A join condition (table1.c1 = table2.c2)
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBJoinCondition extends DBWhere {
	/**
	 * Constructor.
	 *
	 * @param IDBTable One table in join
	 * @param string $column1 Field on first table
	 * @param IDBTable $table2 Second table in join
	 * @param string $column2 Field on second table
	 * @param string $mode Eiter AND or OR
	 */
	public function __construct(IDBTable $table1, $column1, IDBTable $table2, $column2, $mode = IDBWhere::LOGIC_AND) {
		$sql = 
			'(' . 
			$this->prefix_table_name($column1, $table1) . 
			' = ' . 
			$this->prefix_table_name($column2, $table2) .
			')';
				
		parent::__construct($table1, $sql, null, null, $mode);
	}
}