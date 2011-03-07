<?php
/**
 * Interface for DB table 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBTable {
	/**
	 * Returns name of table
	 * 
	 * @return string
	 */
	public function get_table_name();

	/**
	 * Returns alias of table, if any
	 * 
	 * @return string
	 */
	public function get_table_alias();

	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped();

	/**
	 * Returns alias of table, if any - but escaped
	 * 
	 * @return string
	 */
	public function get_table_alias_escaped();
	
	/**
	 * Returns array of columns
	 * 
	 * @return array Associative array with column name as key and IDBField instance as value 
	 */
 	public function get_table_fields();
 	
 	/**
 	 * Returns field fpr given column
 	 *
 	 * @param string $column Column name
 	 * @return IDBField Either field or false if no such field exists
 	 */
 	public function get_table_field($column);
 	
 	/**
 	 * Returns array of keys
 	 * 
 	 * @return array Associative array with column name as key and IDBField instance as value
 	 */
 	public function get_table_keys();
 	
 	/**
 	 * Returns array of relations 
 	 * 
 	 * @return array Array with IDBRelation instance as value 
 	 */
 	public function get_table_relations();
 	
 	/**
 	 * Returns relations between two tables
 	 *
 	 * @param IDBTable $other
 	 * @return array Array of IDBRelations
 	 */
 	public function get_matching_relations(IDBTable $other);
 	
 	/**
 	 * Returns array of constraints
 	 * 
 	 * @return array Array with IDBConstraint instance as value 
 	 */
 	public function get_table_constraints();
 	
 	/**
 	 * Returns DB driver fro this table
 	 *  
 	 * @return IDBDriver
 	 */
 	public function get_table_driver(); 	
}