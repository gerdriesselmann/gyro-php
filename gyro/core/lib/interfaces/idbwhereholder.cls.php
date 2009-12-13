<?php
/**
 * Interface for things that have where clauses
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBWhereHolder {
	/**
	 * Adds where to this
	 * 
	 * Example:
	 * 
	 * $object = new mytable();
	 * $object->add_where('id', '>', 3);
	 * $object->add_where('name', 'in', array('Hans', 'Joe'));
	 * $object->add_where("(email like '%provider1%' or email like '%provider2%')");
	 * 
	 * Will query using the following SQL:
	 * 
	 * SELECT * FROM mytable WHERE (id > 3 AND name IN ('Hans', 'Joe') AND (email like '%provider1%' or email like '%provider2%'));
	 * 
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @mode string Either IDBWhere::OP_AND or DBWhere::OP_OR
	 */
	public function add_where($column, $operator = null, $value = null, $mode = 'AND');
	
	/**
	 * Adds IDBWhere instance to this
	 */
	public function add_where_object(IDBWhere $where);

	/**
	 * Returns root collection of wheres
	 *
	 * @return DBWhereGroup
	 */
	public function get_wheres();	
}