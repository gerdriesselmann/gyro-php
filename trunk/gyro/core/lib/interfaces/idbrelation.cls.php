<?php
/**
 * Interface for a DB relation 
 *  
 * We use terminology source and target here. Of course what is source and what is target depends on 
 * the perspective, so we could also call this A and B.
 * 
 * A relation is defined for one field on source table that relates to one field on target table  
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBRelation {
	/**
	 * Return target table name
	 * 
	 * @return string
	 */	
	public function get_target_table_name();
	
	/**
	 * Returns field taking parts in this relation
	 * 
	 * @return array Associative array with column name as key and IDFieldRelation instance as value 
	 */
	public function get_fields();

	/**
	 * Returns array of fields, but fields get reversed before 
	 *
	 * @return array Associative array with column name as key and IDFieldRelation instance as value 
	 */
	public function get_reversed_fields();
	
	/**
	 * Returns true, if null values are allowed
	 *
	 * @return bool
	 */
	public function get_null_allowed();
	
	/**
	 * Check if relation conditions are fullfiled. This checks from a source perspective, that is:
	 * 
	 * - See if source field conditions are met (e.g. NOT NULL)
	 * - Check if there is a least one record on target table 
	 *
	 * @param array Associative array of form fieldname => fieldvalue
	 * @return Status
	 */
	public function validate($arr_fields);
}
