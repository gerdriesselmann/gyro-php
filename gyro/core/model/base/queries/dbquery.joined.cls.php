<?php
require_once dirname(__FILE__) . '/dbquery.select.cls.php';

/**
 * A query that gets joined to another
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBQueryJoined extends DBQuerySelect {
	/**
	 * INNER JOIN
	 */
	const INNER = 0;
	/**
	 * LEFT JOIN
	 */
	const LEFT = 1;
	/**
	 * RIGHT JOIN
	 */
	const RIGHT = 2;
	/**
	 * Policy to detect join condition fields
	 */	
	const AUTODETECT_CONDITIONS = 128;
	
	/**
	 * The type of join (DBJoinedQuery::INNER, DBJoinedQuery::LEFT etc); 
	 *
	 * @var int
	 */	
	protected $join_type;
	/**
	 * The query this join is joined to
	 *
	 * @var DBQuery
	 */
	protected $parent_query;

	/**
	 * Join conditions
	 *
	 * @var DBWhereGroup
	 */
	protected $join_conditions;
	
	public function __construct(IDBTable $table, DBQuery $parent_query, $join_type = self::INNER, $policy = self::AUTODETECT_CONDITIONS) {
		parent::__construct($table, $policy);
		$this->parent_query = $parent_query;
		$this->join_type = $join_type;
		$this->join_conditions = new DBWhereGroup($table);
	}
	
	/**
	 * Sets join type
	 *
	 * @param int $join_type One of constants DBQueryJoined::INNER, DBJoinedQuery::LEFT etc
	 */
	public function set_join_type($join_type) {
		$this->join_type = $join_type;
	}
	
	/**
	 * Returns type of join
	 *
	 * @return int
	 */
	public function get_join_type() {
		return $this->join_type;
	}
	
	/**
	 * Add join condition
	 *
	 * @param string $this_field Field on this (the joined) table
	 * @param string $parent_field Field on parent table (the table joined to) 
	 * @param string $mode Either AND or OR
	 * @return DBJoinCondition
	 */
	public function add_join_condition($this_field, $parent_field, $mode = IDBWhere::LOGIC_AND) {
		$condition = new DBJoinCondition($this->get_table(), $this_field, $this->parent_query->get_table(), $parent_field, $mode);
		$this->add_join_condition_object($condition);
		return $condition;
	}
	
	/**
	 * Add join condition
	 *
	 * @param IDBWhere $condition
	 */
	public function add_join_condition_object(IDBWhere $condition) {
		$this->join_conditions->add_where_object($condition); 
	}
	
	/**
	 * Returns Join Conditons
	 * 
	 * @return DBWhereGroup
	 */
	public function get_join_conditions() {
		$ret = $this->join_conditions;
		if ($this->has_policy(self::AUTODETECT_CONDITIONS)) {
			$ret->add_where_object($this->compute_join_conditions($this->get_table(), $this->parent_query->get_table()));
		}
		return $ret;
	}
	
	protected function compute_join_conditions(IDBTable $parent, IDBTable $child) {
		$ret = new DBWhereGroup($parent);
		
		$relations = $parent->get_matching_relations($child);
		foreach($relations as $relation) {
			foreach($relation->get_fields() as $fieldrelation) {
				$ret->add_where_object(
					new DBJoinCondition(
						$parent,
						$fieldrelation->get_source_field_name(),
						$child,
						$fieldrelation->get_target_field_name()
					)
				);			
			}
		}
		return $ret;
	}
}
