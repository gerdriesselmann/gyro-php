<?php
require_once dirname(__FILE__) . '/idbsqlbuilder.cls.php';

/**
 * Interface to represent a where statement
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBWhere extends IDBSqlBuilder {
	const LOGIC_AND = 'AND';
	const LOGIC_OR = 'OR';

	const OP_IN = 'IN';
	const OP_NOT_IN = 'NOT IN';
	const OP_LIKE = 'LIKE';
	const OP_NOT_LIKE = 'NOT LIKE';
	const OP_IS_NULL = 'IS NULL';
	const OP_NOT_NULL = 'IS NOT NULL';
	
	/**
	 * Check wheter given value is set on bitflag field 
	 */
	const OP_IN_SET = 'IN_SET';
	/**
	 * Check wether given value is not set on bitflag field 
	 */
	const OP_NOT_IN_SET = 'NOT_IN_SET';
	
	/**
	 * Returns table assigned 
	 * 
	 * @return IDBTable
	 */
	public function get_table();
	
	/**
	 * Returns column. May also be null. 
	 * 
	 * @return string 
	 */
	public function get_column();
	
	/**
	 * Returns the operator (=, >=, LIKe etc.) May also be NULL
	 * 
	 * @return string 
	 */
	public function get_operator();
	
	/**
	 * Returns the value. May also be NULL
	 * 
	 * @return mixed 
	 */
	public function get_value();
	
	/**
	 * Return logical operator (AND or OR)
	 * 
	 * @return string
	 */
	public function get_logical_operator();	
}
