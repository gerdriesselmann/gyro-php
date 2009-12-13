<?php
/**
 * A constraint on a DB table
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBConstraint {
	/**
	 * Returns field taking parts in this relation
	 * 
	 * @return array Array with column name as value 
	 */
	public function get_fields();
	
	/**
	 * Check if constraints are fullfiled.  
	 *
	 * @param array Cosntraint Column data Associative array of form fieldname => fieldvalue
	 * @param array Key Column Data Associative array of form fieldname => fieldvalue
	 * @return Status
	 */
	public function validate($arr_fields, $arr_keys);
}
?>