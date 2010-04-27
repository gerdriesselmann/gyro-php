<?php
/**
 * Defines a relation between two tables
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBRelation implements IDBRelation {
	/**
	 * Do not allow source field(s) to be null
	 */
	const NOT_NULL = 1;
	
	const ONE_TO_ONE = 1;
	const ONE_TO_MANY = 2;
	const MANY_TO_MANY = 3;
	
	protected $target_table;
	protected $arr_fields = array();
	protected $policy;
	protected $type;

	public function __construct($target_table, $fields = null, $policy = self::NOT_NULL, $type = self::ONE_TO_MANY) {
		$this->target_table = $target_table;
		$this->policy = $policy;
		$this->type = $type;
		
		if (!empty($fields)) {
			foreach(Arr::force($fields) as $field) {
				$this->add_field_relation($field);
			}
		}
	}
	
	/**
	 * Return target table name
	 * 
	 * @return string
	 */	
	public function get_target_table_name() {
		return $this->target_table;
	}
	
	/**
	 * Returns field taking parts in this relation
	 * 
	 * @return array Associative array with column name as key and IDFieldRelation instance as value 
	 */
	public function get_fields() {
		return $this->arr_fields;
	}
	
	/**
	 * Returns array of fields, but fields get reversed before 
	 *
	 * @return array Associative array with column name as key and IDFieldRelation instance as value 
	 */
	public function get_reversed_fields() {
		$ret = array();
		foreach($this->get_fields() as $key => $field) {
			$ret[$field->get_target_field_name()] = $field->reverse();	
		}
		return $ret;
	}

	/**
	 * Returns true, if null values are allowed
	 *
	 * @return bool
	 */
	public function get_null_allowed() {
		return !Common::flag_is_set($this->policy, self::NOT_NULL);
	}
	
	/**
	 * Check if relation conditions are fullfiled. This checks from a source perspective, that is:
	 * 
	 * - See if source field conditions are met (e.g. NOT NULL)
	 * - Check if there is a least one record on target table 
	 *
	 * @param array Associative array of form fieldname => fieldvalue
	 * @return Status
	 */
	public function validate($arr_fields) {
		$ret = new Status();
		// Create target instance
		$dao = DB::create($this->target_table);
		
		$b_all_fields_null = true;
		foreach($this->get_fields() as $column => $relation) {
			// Check each fieldrelation
			$value = Arr::get_item($arr_fields, $column, null);
			$b_all_fields_null = $b_all_fields_null && is_null($value);
			
			// Set on target class
			$target_field = $relation->get_target_field_name();
			$dao->$target_field = $value;
		}
		// No item on target table specified
		if ($b_all_fields_null) {
			if (!$this->get_null_allowed()) {
				$ret->append(tr(
					'No instance set for relation to table %target',
					'core',
					array(
						'%target' => tr($this->get_target_table_name(), 'global')
					)
				));
			}
		}
		else if ($dao->count() == 0) { 
			// No such target instance exists
			$ret->append(tr(
				'No matching instance found for relation to table %target',
				'core',
				array(
					'%target' => tr($this->get_target_table_name(), 'global')
				)
			));
		}
		return $ret;
	}
	
	public function add_field_relation(IDBFieldRelation $field) {
		$this->arr_fields[$field->get_source_field_name()] = $field;
	}
	
	/**
	 * Return policy
	 *
	 * @return int
	 */
	public function get_policy() {
		return $this->policy;
	}
	
	/**
	 * Set policy
	 *
	 * @param int $policy
	 */
	public function set_policy($policy) {
		$this->policy = $policy;
	}
	
	/**
	 * Returns true, if client has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy) {
		return Common::flag_is_set($this->policy, $policy);
	}

	/**
	 * Returns type of relation
	 * 
	 * @return int
	 */
	public function get_type() {
		return $this->type;
	}	
}