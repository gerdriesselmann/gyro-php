<?php
require_once dirname(__FILE__) . '/dbquery.ordered.cls.php';

/**
 * A select query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQuerySelect extends DBQueryOrdered {
	const DISTINCT = 1;
	const FOR_UPDATE = 2;

	/**
	 * Subqueries (joins, e.g.)
	 *
	 * @var array
	 */
	protected $subqueries = array();
	/**
	 * Array of associative arrays with members 'field' and 'table'
	 *
	 * @var array
	 */
	protected $group_bys = array();
	/**
	 * Having clauses
	 *
	 * @var DBWhereGroup
	 */
	protected $havings;
	
	public function __construct($table, $policy = self::NORMAL) {
		parent::__construct($table, $policy);
		$this->havings = new DBWhereGroup($table);
	}

	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		$params = $this->prepare_sql_params();
		$builder = $this->create_sql_builder($params);
		return $builder->get_sql();
	}
	
	/**
	 * Create SQL builder
	 * 
	 * @param array $params
	 * @return IDBSqlBuilder
	 */
	protected function create_sql_builder($params) {
		$builder = DBSqlBuilderFactory::create_builder(DBSqlBuilderFactory::SELECT, $this, $params);
		return $builder;
	}
	
	/**
	 * Prepare params for SQL Builder
	 * 
	 * @return array
	 */
	protected function prepare_sql_params() {
		$params = array();
		if ($this->policy & self::DISTINCT) {
			$params['distinct'] = true;
		}
		if ($this->policy & self::FOR_UPDATE) {
			$params['for_update'] = true;
		}
		$params['fields'] = $this->fields;
		$params['group_by'] = $this->get_group_bys();
		$params['having'] = $this->get_havings();
		$params['limit'] = $this->get_limit();
		$params['order_by'] = $this->get_orders();
		return $params;
	}

	/**
	 * Add a table to join
	 *
	 * @param IDBTable $table
	 * @param int $join_type One of the constants defined in DBQueryJoined
	 * @return DBQueryJoined
	 */
	public function add_join(IDBTable $table, $policy = DBQueryJoined::AUTODETECT_CONDITIONS, $join_type = DBQueryJoined::INNER) {
		$join = new DBQueryJoined($table, $this, $join_type, $policy);
		$this->add_subquery($join);
		return $join;
	}

	/**
	 * Return sub queries
	 *
	 * @return array
	 */
	public function get_subqueries() {
		return $this->subqueries;
	}
	
	/**
	 * Add a sub query (this is: a join)
	 *
	 * @param DBQuerySelect $query
	 */
	public function add_subquery(DBQuerySelect $query) {
		$this->subqueries[] = $query;		
	}

	/**
	 * Returns collection of wheres
	 *
	 * @return DBWhereGroup
	 */
	public function get_wheres() {
		$ret = new DBWhereGroup($this->get_table());
		$ret->add_where_object(parent::get_wheres());
		foreach($this->subqueries as  $subquery) {
			$ret->add_where_object($subquery->get_wheres());
		}
		return $ret;
	}

	/**
	 * Returns root collection of having
	 *
	 * @return DBWhereGroup
	 */
	public function get_havings() {
		return $this->havings;
	}

	/**
	 * Add group by, without any column the current group by is cleared
	 * @param string $column
	 */
	public function add_group_by($column) {
		if (empty($column)) {
			$this->group_bys = array();
		} else {
			$table = $this->table;
			$this->group_bys[] = array(
				'field' => $column,
				'table' => $table,
			);
		}
	}

	/**
	 * Returns group by
	 *
	 * @return array Array of associative arrays of form "field => ..., table => ..."
	 */
	public function get_group_bys() {
		return $this->group_bys;
	}

	/**
	 * Adds having clause to this
	 *
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param string $operator Operator to execute
	 * @param mixed $value Value(s) to use
	 * @param string $mode Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 */
	public function add_having($column, $operator = null, $value = null, $mode = IDBWhere::LOGIC_AND) {
		$this->havings->add_where($column, $operator, $value, $mode);
	}

	/**
	 * Adds IDBWhere instance to this
	 */
	public function add_having_object(IDBWhere $having) {
		$this->havings->add_where_object($having);
	}
}
