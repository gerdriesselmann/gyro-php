<?php
/**
 * Masks a sub query as a table, that can be used in Joins
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBTableSubquery extends DBTable {
	/**
	 * The query to mask as table
	 */
	protected $query; 
	
	/**
	 * Constructor
	 *
	 * @param IDBTable $table The table to overload alias for
	 * @param string $alias
	 */
	public function __construct($query, $alias, $table = null) {
		$this->query = $query;
		
		$fields = array();
		if ($table instanceof IDBTable) {
			$fields = $table->get_table_fields();
		}
		parent::__construct($alias, $fields);
	}
	
	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped() {
		return '(' . $this->query . ')';
	}
}
