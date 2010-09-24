<?php
require_once dirname(__FILE__) . '/ipolicyholder.cls.php';
/**
 * Interface for DB Field
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBField extends IPolicyHolder {
	/**
	 * Returns name
	 *
	 * @return string
	 */
	public function get_field_name();
	
	/**
	 * Returns true, if null values are allowed
	 *
	 * @return bool
	 */
	public function get_null_allowed();
	
	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default();
	
	/**
	 * Returns true, if field has default value
	 */
	public function has_default_value();
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value);
	
	/**
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value);
	
	/**
	 * Format for use in WHERE clause
	 *  
	 * @param mixed $value
	 * @return string
	 */
	public function format_where($value);
	
	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select();
	
	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value);
	
	/**
	 * Reads value from array (e.g $_POST) and converts it into something meaningfull
	 */
	public function read_from_array($arr);
	
	/**
	 * Set connection of field
	 * 
	 * @param string|IDBDriver $connection
	 * @return void
	 */
	public function set_connection($connection);

	/**
	 * Set table field belongs to
	 * 
	 * @attention The table may not be set, fields must be aware of this!
	 * 
	 * @param IDBTable $table
	 * @return void
	 */
	public function set_table($table);
}