<?php
/**
 * A DB Where representation
 *
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBWhere implements IDBWhere {
	protected $table;
	protected $column;
	protected $operator;
	protected $value;
	protected $logical_operator;
	
	/**
	 * Constructor
	 *
	 * @param IDBTable $table Table that contains column
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @mode string Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 */
	public function __construct(IDBTable $table, $column, $operator = null, $value = null, $mode = IDBWhere::LOGIC_AND) {
		$this->table = $table;
		$this->column = $column;
		$this->operator = String::to_upper(trim($operator));
		$this->value = $value;
		$this->logical_operator = $mode;
	}

	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		$sqlbuilder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::WHERE, $this);
		return $sqlbuilder->get_sql();
	}
	
	/**
	 * Returns table assigned 
	 * 
	 * @return IDBTable
	 */
	public function get_table() {
		return $this->table;
	}
	
	/**
	 * Returns column. May also be null. 
	 * 
	 * @return string 
	 */
	public function get_column() {
		return $this->column;
	}
	
	/**
	 * Returns the operator (=, >=, LIKe etc.) May also be NULL
	 * 
	 * @return string 
	 */
	public function get_operator() {
		return $this->operator;
	}
	
	/**
	 * Returns the value. May also be NULL
	 * 
	 * @return mixed 
	 */
	public function get_value() {
		return $this->value;
	}
			
	/**
	 * Return logical operator (AND or OR)
	 *
	 * @return string
	 */
	public function get_logical_operator() {
		return $this->logical_operator;
	}

	/**
	 * Prefix column with table name
	 *
	 * @param string $column
	 * @param IDBTable|string $table
	 * @return string
	 */
	protected function prefix_table_name($column, $table) {
		$ret = $column;
		if (!String::contains($column, '.')) {
			if ($table instanceof IDBTable) {
				$ret = DB::escape_database_entity($column, $table->get_table_driver()); 
				if ($table->get_table_field($column)) {
					$ret = $table->get_table_alias_escaped() . '.' . $ret;
				}
			}
			else {
				$ret = DB::escape_database_entity($column);
			}
		}
		return $ret;
	}
}
