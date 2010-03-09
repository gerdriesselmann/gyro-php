<?php
/**
 * Execute a given filter on a linked table, rather than the primary table
 * 
 * Construct with another filter to execute
 * 
 * @code
 * public function get_filters() {
 *   return array(
 *     new DBFilterGroup(
 *       'filteronlink',
 *       tr('Filtering on linked table'),
 *       array(
 *         'example' => new DBFilterOnLinkedTable(
 *           tr('Example'),
 *           new DBFilterColumn('field_a', 'value_a', 'Ignored'),
 *           'table_linked'
 *         ), 
 *         // Filters can be mixed!
 *         'classic' => new DBFilterColumn('field_c', 'old_stuff', tr('Classic'))
 *       )
 *     );
 *   )
 * }
 * @endcode
 * 
 * @ingroup QueryModifiers
 * @author Gerd Riesselmann
 */
class DBFilterOnLinkedTable extends DBFilter {
	/**
	 * The filter to execute on the linked table
	 * 
	 * @var DBFilter
	 */
	protected $filter;
	
	/**
	 * Linked Table
	 * 
	 * @var IDBTable|string
	 */
	protected $linked_table;
	
	/**
	 * Join conditions
	 * 
	 * @var array Array of DBJoinCondition or FALSE
	 */
	protected $join_conditions;
	
	/**
	 * Join Type
	 * 
	 * @var string One of DBQueryJoined constants
	 */
	protected $join_type;
	
	public function __construct($title, $filter, $table, $join_condition = false, $join_type = DBQueryJoined::INNER) {
		$this->filter = $filter;
		$this->linked_table = $table;
		$this->join_condition = $join_condition;
		$this->join_type = $join_type;
		parent::__construct($title);
	}
	

	/**
	 * Apply 
	 * 
	 * @param DataObjectBase $query
	 */
	public function apply($query) {
		$table = ($this->linked_table instanceof IDBTable) ? $this->linked_table : DB::create($this->linked_table);
		$this->filter->apply($table);
		$query->join($table, $this->join_conditions, $this->join_type);
	}	
	
	/**
	 * Returns inside filter
	 * 
	 * @return DBFilter
	 */
	public function get_inside_filter() {
		return $this->filter;
	}
}