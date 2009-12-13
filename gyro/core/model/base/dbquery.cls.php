<?php
/**
 * Represents a DB query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
abstract class DBQuery implements IDBQuery  {
	/**
	 * Table this query is is done upon
	 *
	 * @var IDBTable
	 */
	protected $table;
	/**
	 * Where clauses
	 *
	 * @var DBWhereGroup
	 */
	protected $wheres;
	/**
	 * Array of fields.
	 *
	 * @var array
	 */
	protected $fields = array();
	/**
	 * Policy used for query building. Defined by subclasses
	 *
	 * @var int
	 */
	protected $policy;

	public function __construct(IDBTable $table, $policy = self::NORMAL) {
		$this->table = $table;
		$this->policy = $policy;
		$this->wheres = new DBWhereGroup($table);
	}

	/**
	 * Returns table
	 *
	 * @return IDBTable
	 */
	public function get_table() {
		return $this->table;
	}

	/**
	 * Returns root collection of wheres
	 *
	 * @return DBWhereGroup
	 */
	public function get_wheres() {
		return $this->wheres;
	}
	
	/**
	 * Set the fields this query affects
	 *
	 * Dependent on the query time, the array passed can be of different forms
	 *
	 * UPDATE and INSERT: Associative array with field name as key and field value as value
	 *
	 * $query = new DBQuery($some_table);
	 * $query->set_fields(array('name' => 'Johnny'));
	 * $query->add_where('id', '=', 3);
	 * $sql = $query->build_update_sql(); // returns UPDATE some_table SET name = 'Johnny' WHERE id = 3
	 *
	 * SELECT: Either array of field names or associative array with field name as key and field alias as value.
	 * Both can be combined.
	 *
	 * $query = new DBQuery($some_table);
	 * $query->set_fields(array('count(name)' => 'c', 'phone'));
	 * $sql = $query->build_select_sql(); // returns SELECT count(name) AS c, phone FROM some_table
	 *
	 * If invoked with DBQuery::CLEAR, the fields will get cleared
	 *
	 * @param array $arr_fields
	 * @return void
	 */
	public function set_fields($arr_fields) {
		$this->fields = Arr::force($arr_fields, false);
	}

	/**
	 * Add a field
	 *
	 * @param mixed $field
	 */
	public function add_field($field) {
		$this->fields[] = $field;
	}

	/**
	 * Return fields
	 *
	 * @return array
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Set policy
	 *
	 * @param int $policy Either DBQuerySelect::DEFAULT_POLICY or DBQuerySelect::DISTINCT_POLICY
	 */
	public function set_policy($policy) {
		$this->policy = $policy;
	}

	/**
	 * Returns policy
	 *
	 * @return int
	 */
	public function get_policy() {
		return $this->policy;
	}

	/**
	 * Returns true, if has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy) {
		return Common::flag_is_set($this->get_policy(), $policy);
	}

	/**
	 * Adds where to this
	 *
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @param string $mode Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 */
	public function add_where($column, $operator = null, $value = null, $mode = IDBWhere::LOGIC_AND) {
		$this->wheres->add_where($column, $operator, $value, $mode);
	}

	/**
	 * Adds IDBWhere instance to this
	 */
	public function add_where_object(IDBWhere $where) {
		$this->wheres->add_where_object($where);
	}
}
