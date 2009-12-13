<?php
/**
 * Defines Interface to abstract search
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ISearchAdapter {
	const ASC = 'ASC';
	const DESC = 'DESC';
	
	const CLEAR = 'CLEAR_SORT';
	
	/**
	 * Count resulting items
	 */
	public function count();
	
	/**
	 * Limit result
	 */
	public function limit($start = 0, $number_of_items = 0);
	
	/**
	 * Execute search
	 * 
	 * @return array Array of result items
	 */
	public function execute();

	/**
	 * Return array of sortable columns. Array has column name as key and a sort type (enum) as value  
	 */
	public function get_sortable_columns();
	
	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column();
	
	/**
	 * Sort result by colum
	 * 
	 * Pass self::CLEAR to clear current sort
	 * 
	 * @param String column name
	 * @param Enum 'asc' or 'desc'
	 */ 
	public function sort($column, $order = self::ASC);
	
	/**
	 * Return array of filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters();
	
	/**
	 * Apply a modifier
	 * 
	 * @param IDBQueryModifier The modifier to be applied
	 */
	public function apply_modifier($modifier);
}
