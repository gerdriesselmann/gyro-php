<?php
/**
 * Interface for stepping through DB result set
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBResultSet {
	/**
	 * Closes internal cursor
	 * 
	 * @return void
	 */
	public function close();
	
	/**
	 * Returns number of columns in result set
	 *
	 * @return int
	 */
	public function get_column_count();
	
	/**
	 * Returns number of rows in result set
	 * 
	 * @return int
	 */
	public function get_row_count();
	
	/**
	 * Returns row as associative array
	 *
	 * @return array
	 */
	public function fetch();
	
	/**
	 * Returns status 
	 *
	 * @return Status
	 */
	public function get_status(); 
}
