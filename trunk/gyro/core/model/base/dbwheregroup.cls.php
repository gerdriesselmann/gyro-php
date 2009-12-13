<?php
/**
 * A group of where statements that acts as own where
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBWhereGroup implements IDBWhere, IDBWhereHolder {
	protected $where_clauses = array();
	protected $logical_operator;
	/**
	 * Table object
	 *
	 * @var IDBTable
	 */
	protected $table;
	
	/**
	 * Constructor
	 * 
	 * @param IDBTable $table Table that contains column
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @param string $logical_operator Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 */
	public function __construct(IDBTable $table, $logical_operator = IDBWhere::LOGIC_AND) {
		$this->table = $table;
		$this->logical_operator = $logical_operator;
	}
	
	/**
	 * Adds where to this
	 * 
	 * Example:
	 * 
	 * $object = new mytable();
	 * $object->add_where('id', '>', 3);
	 * $object->add_where('name', 'in', array('Hans', 'Joe'));
	 * $wheres = new DBWhereGroup($object);	 * 
	 * $wheres->add_where('email', IDBWhere::OP_LIKE, '%provider1%');
	 * $wheres->add_where('email', IDBWhere::OP_LIKE, '%provider2%', IDBWhere::LOGIC_OR);
	 * $object->add_where($wheres->get_sql());
	 * 
	 * Results in the following SQL:
	 * SELECT * FROM mytable WHERE id > 3 AND name IN ('Hans', 'Joe') AND (email LIKE '%provider1%' OR email LIKE '%provider2%');
	 * 
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @param string $mode Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 * @return DBWhere
	 * 
	 */
	public function add_where($column, $operator = null, $value = null, $mode = IDBWhere::LOGIC_AND) {
		$ret = new DBWhere($this->table, $column, $operator, $value, $mode);
		$this->where_clauses[] = $ret;
		return $ret;
	}

	/**
	 * Adds IDBWhere instance to this
	 */
	public function add_where_object(IDBWhere $where) {
		$this->where_clauses[] = $where;	
	}

	/**
	 * Returns root collection of wheres
	 *
	 * @return DBWhereGroup
	 */
	public function get_wheres() {
		return $this;
	}	
	
	/**
	 * Returns the number of clauses added
	 *
	 * @return int
	 */
	public function count() {
		return count($this->where_clauses);
	}
	
	/**
	 * Returns where objcts, this groups consists of, as array
	 * 
	 * @return array
	 */
	public function get_children() {
		return $this->where_clauses;
	}
	
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql() {
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::WHEREGROUP, $this);
		return $builder->get_sql();
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
		
	}
	
	/**
	 * Returns the operator (=, >=, LIKe etc.) May also be NULL
	 * 
	 * @return string 
	 */
	public function get_operator() {
		return null;
	}
	
	/**
	 * Returns the value. May also be NULL
	 * 
	 * @return mixed 
	 */
	public function get_value() {
		return null;
	}
	
	/**
	 * Return logical operator (AND or OR)
	 * 
	 * @return string
	 */
	public function get_logical_operator() {
		return $this->logical_operator;
	}
	
}
